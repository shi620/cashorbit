<?php
// =============================================
// CASHORBIT - WITHDRAWAL REQUEST
// =============================================
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse("error", ["message" => "Method not allowed"], 405);
}

 $input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    $input = $_POST;
}

 $user_id = intval($input['user_id'] ?? 0);
 $amount = floatval($input['amount'] ?? 0);
 $upi_id = sanitizeString($input['upi_id'] ?? '');

if ($user_id <= 0) {
    jsonResponse("error", ["message" => "Valid user_id is required"], 400);
}

if ($amount < $MIN_WITHDRAW) {
    jsonResponse("error", ["message" => "Minimum withdrawal amount is ₹$MIN_WITHDRAW"], 400);
}

// Validate UPI ID (basic format check)
if (empty($upi_id) || !preg_match('/^[\w.\-]+@[\w]+$/', $upi_id)) {
    jsonResponse("error", ["message" => "Invalid UPI ID format"], 400);
}

try {
    $pdo->beginTransaction();

    // Lock user row
    $stmt = $pdo->prepare("SELECT id, balance FROM users WHERE id = ? FOR UPDATE");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        $pdo->rollBack();
        jsonResponse("error", ["message" => "User not found"], 404);
    }

    if ($user['balance'] < $amount) {
        $pdo->rollBack();
        jsonResponse("error", ["message" => "Insufficient balance"], 400);
    }

    // Check for existing pending withdrawal
    $stmt = $pdo->prepare("
        SELECT id FROM withdraw_requests 
        WHERE user_id = ? AND status = 'pending'
    ");
    $stmt->execute([$user_id]);
    if ($stmt->fetch()) {
        $pdo->rollBack();
        jsonResponse("error", ["message" => "You already have a pending withdrawal request"], 400);
    }

    // Deduct from balance immediately
    $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
    $stmt->execute([$amount, $user_id]);

    // Create withdrawal request
    $stmt = $pdo->prepare("
        INSERT INTO withdraw_requests (user_id, amount, upi_id, status)
        VALUES (?, ?, ?, 'pending')
    ");
    $stmt->execute([$user_id, $amount, $upi_id]);

    // Log transaction
    $stmt = $pdo->prepare("
        INSERT INTO transactions (user_id, amount, type, status, description)
        VALUES (?, ?, 'deduct', 'success', ?)
    ");
    $stmt->execute([$user_id, $amount, "Withdrawal request to UPI: $upi_id"]);

    // Get new balance
    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $new_balance = $stmt->fetchColumn();

    $pdo->commit();

    jsonResponse("success", [
        "message" => "Withdrawal request of ₹$amount submitted successfully",
        "new_balance" => floatval($new_balance)
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    jsonResponse("error", ["message" => "Withdrawal request failed"], 500);
}
?>
<?php
// =============================================
// CASHORBIT - DEDUCT MONEY FROM WALLET
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
 $description = sanitizeString($input['description'] ?? 'Wallet deduction');

if ($user_id <= 0) {
    jsonResponse("error", ["message" => "Valid user_id is required"], 400);
}

if ($amount <= 0) {
    jsonResponse("error", ["message" => "Amount must be greater than 0"], 400);
}

try {
    $pdo->beginTransaction();

    // Check user exists and lock row
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

    // Deduct balance
    $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
    $stmt->execute([$amount, $user_id]);

    // Log transaction
    $stmt = $pdo->prepare("
        INSERT INTO transactions (user_id, amount, type, status, description)
        VALUES (?, ?, 'deduct', 'success', ?)
    ");
    $stmt->execute([$user_id, $amount, $description]);

    // Get updated balance
    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $new_balance = $stmt->fetchColumn();

    $pdo->commit();

    jsonResponse("success", [
        "message" => "₹$amount deducted from wallet",
        "new_balance" => floatval($new_balance)
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    jsonResponse("error", ["message" => "Transaction failed"], 500);
}
?>
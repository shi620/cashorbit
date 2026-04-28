<?php
// =============================================
// CASHORBIT - GET USER PROFILE
// =============================================
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse("error", ["message" => "Method not allowed"], 405);
}

 $user_id = intval($_GET['user_id'] ?? 0);

if ($user_id <= 0) {
    jsonResponse("error", ["message" => "Valid user_id is required"], 400);
}

try {
    $stmt = $pdo->prepare("
        SELECT id, name, email, balance, referral_code, referred_by, referral_earn, referral_count, created_at 
        FROM users 
        WHERE id = ?
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        jsonResponse("error", ["message" => "User not found"], 404);
    }

    // Get recent transactions
    $stmt = $pdo->prepare("
        SELECT id, amount, type, status, description, created_at 
        FROM transactions 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT 20
    ");
    $stmt->execute([$user_id]);
    $transactions = $stmt->fetchAll();

    // Get pending withdrawals
    $stmt = $pdo->prepare("
        SELECT id, amount, upi_id, status, created_at 
        FROM withdraw_requests 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    $stmt->execute([$user_id]);
    $withdrawals = $stmt->fetchAll();

    jsonResponse("success", [
        "user" => $user,
        "transactions" => $transactions,
        "withdrawals" => $withdrawals
    ]);

} catch (Exception $e) {
    jsonResponse("error", ["message" => "Failed to fetch user data"], 500);
}
?>
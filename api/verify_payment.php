<?php
// =============================================
// CASHORBIT - CASHFREE PAYMENT VERIFICATION
// =============================================
require_once __DIR__ . '/config.php';

// This endpoint is called by Cashfree redirect AND can be called from app

if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse("error", ["message" => "Method not allowed"], 405);
}

 $input = $_SERVER['REQUEST_METHOD'] === 'POST'
    ? (json_decode(file_get_contents("php://input"), true) ?: $_POST)
    : $_GET;

 $order_id = sanitizeString($input['order_id'] ?? '');

if (empty($order_id)) {
    // If called as redirect, return HTML page
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        echo '<html><head><title>Payment Processing</title>
        <style>body{font-family:sans-serif;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;background:#0f172a;color:#e2e8f0}
        .box{text-align:center;padding:40px;border-radius:16px;background:#1e293b;box-shadow:0 0 40px rgba(0,0,0,.3)}
        h2{margin:0 0 10px}p{color:#94a3b8}</style></head>
        <body><div class="box"><h2>Processing...</h2><p>Your payment is being verified.</p></div></body></html>';
        exit();
    }
    jsonResponse("error", ["message" => "Order ID is required"], 400);
}

try {
    // Find pending transaction for this order
    $stmt = $pdo->prepare("
        SELECT id, user_id, amount 
        FROM transactions 
        WHERE description LIKE ? AND status = 'pending'
        ORDER BY created_at DESC 
        LIMIT 1
    ");
    $stmt->execute(["%$order_id%"]);
    $transaction = $stmt->fetch();

    if (!$transaction) {
        jsonResponse("error", ["message" => "No pending transaction found for this order"], 404);
    }

    // Verify with Cashfree
    $base_url = $CASHFREE_ENVIRONMENT === "PRODUCTION"
        ? "https://api.cashfree.com/pg/orders"
        : "https://sandbox.cashfree.com/pg/orders";

    $ch = curl_init("$base_url/$order_id");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "x-client-id: $CASHFREE_APP_ID",
        "x-client-secret: $CASHFREE_SECRET_KEY",
        "x-api-version: 2023-08-01"
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($http_code !== 200) {
        // Mark transaction as failed
        $stmt = $pdo->prepare("UPDATE transactions SET status = 'failed' WHERE id = ?");
        $stmt->execute([$transaction['id']]);

        jsonResponse("error", ["message" => "Payment verification failed"], 400);
    }

    $order_status = $result['order_status'] ?? '';

    if ($order_status === 'PAID') {
        $pdo->beginTransaction();

        // Mark transaction as success
        $stmt = $pdo->prepare("UPDATE transactions SET status = 'success' WHERE id = ?");
        $stmt->execute([$transaction['id']]);

        // Add money to wallet
        $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->execute([$transaction['amount'], $transaction['user_id']]);

        // Get new balance
        $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
        $stmt->execute([$transaction['user_id']]);
        $new_balance = $stmt->fetchColumn();

        $pdo->commit();

        // If called as redirect, show success page
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            echo '<html><head><title>Payment Successful</title>
            <style>body{font-family:sans-serif;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;background:#0f172a;color:#e2e8f0}
            .box{text-align:center;padding:40px;border-radius:16px;background:#1e293b;box-shadow:0 0 40px rgba(0,0,0,.3)}
            .icon{font-size:48px;margin-bottom:16px}h2{margin:0 0 10px;color:#22c55e}p{color:#94a3b8}
            a{display:inline-block;margin-top:20px;padding:12px 32px;background:#6366f1;color:#fff;border-radius:8px;text-decoration:none;font-weight:600}</style></head>
            <body><div class="box"><div class="icon">&#10003;</div><h2>Payment Successful!</h2>
            <p>₹' . $transaction['amount'] . ' has been added to your CashOrbit wallet.</p>
            <a href="https://cashorb.42web.io">Back to Home</a></div></body></html>';
            exit();
        }

        jsonResponse("success", [
            "message" => "Payment verified and amount added to wallet",
            "amount" => floatval($transaction['amount']),
            "new_balance" => floatval($new_balance)
        ]);

    } else {
        // Mark as failed
        $stmt = $pdo->prepare("UPDATE transactions SET status = 'failed' WHERE id = ?");
        $stmt->execute([$transaction['id']]);

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            echo '<html><head><title>Payment Failed</title>
            <style>body{font-family:sans-serif;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;background:#0f172a;color:#e2e8f0}
            .box{text-align:center;padding:40px;border-radius:16px;background:#1e293b;box-shadow:0 0 40px rgba(0,0,0,.3)}
            .icon{font-size:48px;margin-bottom:16px}h2{margin:0 0 10px;color:#ef4444}p{color:#94a3b8}
            a{display:inline-block;margin-top:20px;padding:12px 32px;background:#6366f1;color:#fff;border-radius:8px;text-decoration:none;font-weight:600}</style></head>
            <body><div class="box"><div class="icon">&#10007;</div><h2>Payment Failed</h2>
            <p>Your payment could not be processed. Please try again.</p>
            <a href="https://cashorb.42web.io">Back to Home</a></div></body></html>';
            exit();
        }

        jsonResponse("error", ["message" => "Payment not completed. Status: $order_status"], 400);
    }

} catch (Exception $e) {
    jsonResponse("error", ["message" => "Verification failed"], 500);
}
?>
<?php
// =============================================
// CASHORBIT - CASHFREE ORDER CREATION
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

if ($user_id <= 0) {
    jsonResponse("error", ["message" => "Valid user_id is required"], 400);
}

if ($amount < 10) {
    jsonResponse("error", ["message" => "Minimum add amount is ₹10"], 400);
}

try {
    // Verify user exists
    $stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        jsonResponse("error", ["message" => "User not found"], 404);
    }

    // Generate order ID
    $order_id = "CO_" . time() . "_" . rand(1000, 9999);

    // Determine Cashfree API URL
    $base_url = $CASHFREE_ENVIRONMENT === "PRODUCTION"
        ? "https://api.cashfree.com/pg/orders"
        : "https://sandbox.cashfree.com/pg/orders";

    // Prepare order payload
    $payload = [
        "order_id" => $order_id,
        "order_amount" => $amount,
        "order_currency" => "INR",
        "customer_details" => [
            "customer_id" => "CO_USER_" . $user_id,
            "customer_name" => $user['name'],
            "customer_email" => $user['email'],
            "customer_phone" => "9999999999"
        ],
        "order_meta" => [
            "return_url" => "https://cashorb.42web.io/api/verify_payment.php?order_id={order_id}"
        ],
        "order_note" => "CashOrbit wallet top-up - User #$user_id"
    ];

    $json_payload = json_encode($payload);

    // Call Cashfree API
    $ch = curl_init($base_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "x-client-id: $CASHFREE_APP_ID",
        "x-client-secret: $CASHFREE_SECRET_KEY",
        "x-api-version: 2023-08-01"
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_error) {
        jsonResponse("error", ["message" => "Payment gateway connection failed"], 503);
    }

    $result = json_decode($response, true);

    if ($http_code !== 200 && $http_code !== 201) {
        $error_msg = $result['message'] ?? "Payment order creation failed";
        jsonResponse("error", ["message" => $error_msg], 400);
    }

    // Store pending transaction
    $stmt = $pdo->prepare("
        INSERT INTO transactions (user_id, amount, type, status, description)
        VALUES (?, ?, 'add', 'pending', ?)
    ");
    $stmt->execute([$user_id, $amount, "Cashfree order: $order_id"]);

    $transaction_id = $pdo->lastInsertId();

    jsonResponse("success", [
        "message" => "Order created successfully",
        "order_id" => $order_id,
        "payment_session_id" => $result['payment_session_id'] ?? null,
        "order_amount" => floatval($result['order_amount'] ?? $amount),
        "transaction_id" => $transaction_id
    ]);

} catch (Exception $e) {
    jsonResponse("error", ["message" => "Order creation failed"], 500);
}
?>
<?php
// =============================================
// CASHORBIT - USER LOGIN
// =============================================
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse("error", ["message" => "Method not allowed"], 405);
}

 $input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    $input = $_POST;
}

 $email = trim($input['email'] ?? '');
 $password = $input['password'] ?? '';

if (empty($email) || empty($password)) {
    jsonResponse("error", ["message" => "Email and password are required"], 400);
}

try {
    $stmt = $pdo->prepare("
        SELECT id, name, email, password, balance, referral_code, referred_by, referral_earn, referral_count, created_at 
        FROM users 
        WHERE email = ?
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        jsonResponse("error", ["message" => "Invalid email or password"], 401);
    }

    // Remove password from response
    unset($user['password']);

    jsonResponse("success", [
        "message" => "Login successful",
        "user" => $user
    ]);

} catch (Exception $e) {
    jsonResponse("error", ["message" => "Login failed"], 500);
}
?>
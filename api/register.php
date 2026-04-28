<?php
// =============================================
// CASHORBIT - USER REGISTRATION WITH REFERRAL
// =============================================
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse("error", ["message" => "Method not allowed"], 405);
}

 $input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    $input = $_POST;
}

 $name = sanitizeString($input['name'] ?? '');
 $email = trim($input['email'] ?? '');
 $password = $input['password'] ?? '';
 $referral_code_input = sanitizeString($input['referral_code'] ?? '');

// Validation
if (empty($name) || strlen($name) < 2) {
    jsonResponse("error", ["message" => "Name must be at least 2 characters"], 400);
}

if (!validateEmail($email)) {
    jsonResponse("error", ["message" => "Invalid email address"], 400);
}

if (strlen($password) < 6) {
    jsonResponse("error", ["message" => "Password must be at least 6 characters"], 400);
}

try {
    // Check duplicate email
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        jsonResponse("error", ["message" => "Email already registered"], 409);
    }

    // Generate unique referral code
    $user_referral_code = generateReferralCode($pdo);

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

    // Process referral
    $referred_by = null;
    $referrer_id = null;

    if (!empty($referral_code_input)) {
        $stmt = $pdo->prepare("SELECT id, referral_code FROM users WHERE referral_code = ?");
        $stmt->execute([$referral_code_input]);
        $referrer = $stmt->fetch();

        if (!$referrer) {
            jsonResponse("error", ["message" => "Invalid referral code"], 400);
        }

        $referrer_id = $referrer['id'];
        $referred_by = $referrer['referral_code'];
    }

    $pdo->beginTransaction();

    // Insert new user
    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, password, balance, referral_code, referred_by, referral_earn, referral_count)
        VALUES (?, ?, ?, 0.00, ?, ?, 0.00, 0)
    ");
    $stmt->execute([$name, $email, $hashed_password, $user_referral_code, $referred_by]);
    $new_user_id = $pdo->lastInsertId();

    // Apply referral bonus
    if ($referrer_id !== null) {
        // Add bonus to referrer
        $stmt = $pdo->prepare("
            UPDATE users 
            SET balance = balance + ?, 
                referral_earn = referral_earn + ?, 
                referral_count = referral_count + 1 
            WHERE id = ?
        ");
        $stmt->execute([$REFERRAL_BONUS, $REFERRAL_BONUS, $referrer_id]);

        // Log referrer transaction
        $stmt = $pdo->prepare("
            INSERT INTO transactions (user_id, amount, type, status, description)
            VALUES (?, ?, 'referral', 'success', ?)
        ");
        $stmt->execute([
            $referrer_id,
            $REFERRAL_BONUS,
            "Referral bonus for inviting $name"
        ]);

        // Give bonus to new user too
        $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->execute([$REFERRAL_BONUS, $new_user_id]);

        // Log new user transaction
        $stmt = $pdo->prepare("
            INSERT INTO transactions (user_id, amount, type, status, description)
            VALUES (?, ?, 'referral', 'success', ?)
        ");
        $stmt->execute([
            $new_user_id,
            $REFERRAL_BONUS,
            "Welcome bonus for using referral code"
        ]);
    }

    $pdo->commit();

    // Fetch created user
    $stmt = $pdo->prepare("SELECT id, name, email, balance, referral_code, referred_by, referral_earn, referral_count, created_at FROM users WHERE id = ?");
    $stmt->execute([$new_user_id]);
    $user = $stmt->fetch();

    jsonResponse("success", [
        "message" => "Registration successful",
        "user" => $user
    ], 201);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    jsonResponse("error", ["message" => "Registration failed. Please try again."], 500);
}
?>
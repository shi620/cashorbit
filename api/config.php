<?php
// =============================================
// CASHORBIT - DATABASE CONFIGURATION
// =============================================

// Enable error display for debugging (remove in production)
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ============================================
// ⚠️ UPDATE THESE 4 VALUES FROM YOUR 42web.io PANEL
// Control Panel → MySQL Databases → View Details
// ============================================
 $DB_HOST = "localhost";       // e.g., "sql308.epizy.com" or "localhost"
 $DB_NAME = "cashorbit_db";    // Your database name from 42web.io
 $DB_USER = "root";            // Your database username from 42web.io
 $DB_PASS = "";                // Your database password from 42web.io

// Cashfree credentials (replace with your keys)
 $CASHFREE_APP_ID = "YOUR_CASHFREE_APP_ID";
 $CASHFREE_SECRET_KEY = "YOUR_CASHFREE_SECRET_KEY";
 $CASHFREE_ENVIRONMENT = "TEST";

// Referral bonus amount
 $REFERRAL_BONUS = 5.00;

// Minimum withdrawal
 $MIN_WITHDRAW = 50.00;

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 10
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed. Check config.php credentials."
    ]);
    exit();
}

function jsonResponse($status, $data = [], $code = 200) {
    http_response_code($code);
    $response = ["status" => $status];
    $response = array_merge($response, $data);
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit();
}

function generateReferralCode($pdo, $length = 8) {
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $attempts = 0;
    do {
        $code = substr(str_shuffle($chars), 0, $length);
        $stmt = $pdo->prepare("SELECT id FROM users WHERE referral_code = ?");
        $stmt->execute([$code]);
        $attempts++;
        if ($attempts > 100) break;
    } while ($stmt->fetch());
    return $code;
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function sanitizeString($str) {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}
?>

<?php
// =============================================
// CASHORBIT - ADMIN LOGIN
// =============================================
session_start();
error_reporting(0);

// If already logged in, redirect
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

 $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "All fields are required";
    } else {
        require_once __DIR__ . '/../api/config.php';

        try {
            $stmt = $pdo->prepare("SELECT id, username, password FROM admin WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid credentials";
            }
        } catch (Exception $e) {
            $error = "Login failed. Try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - CashOrbit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0f172a;
            font-family: 'Segoe UI', system-ui, sans-serif;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(ellipse at 30% 20%, rgba(99,102,241,0.08) 0%, transparent 50%),
                        radial-gradient(ellipse at 70% 80%, rgba(6,182,212,0.06) 0%, transparent 50%);
            animation: bgFloat 20s ease-in-out infinite;
        }
        @keyframes bgFloat {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(-2%, 1%) rotate(1deg); }
        }
        .login-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(99, 102, 241, 0.15);
            border-radius: 20px;
            padding: 48px 40px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.4), 0 0 80px rgba(99,102,241,0.05);
        }
        .brand-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #6366f1, #06b6d4);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            box-shadow: 0 8px 24px rgba(99,102,241,0.3);
        }
        .login-card h1 {
            color: #f1f5f9;
            font-size: 24px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 4px;
        }
        .login-card .subtitle {
            color: #64748b;
            text-align: center;
            font-size: 14px;
            margin-bottom: 32px;
        }
        .form-floating {
            margin-bottom: 16px;
        }
        .form-floating .form-control {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 12px;
            color: #e2e8f0;
            height: 52px;
            padding-top: 20px;
            font-size: 15px;
            transition: all 0.3s;
        }
        .form-floating .form-control:focus {
            background: rgba(15, 23, 42, 0.8);
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
            color: #f1f5f9;
        }
        .form-floating label {
            color: #64748b;
            font-size: 14px;
        }
        .btn-login {
            width: 100%;
            height: 52px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 8px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(99,102,241,0.4);
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .input-icon {
            position: relative;
        }
        .input-icon i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #475569;
            font-size: 16px;
            z-index: 2;
        }
        .input-icon .form-control {
            padding-left: 44px;
        }
        .input-icon .form-floating label {
            left: 44px;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand-icon">
            <i class="fas fa-rocket"></i>
        </div>
        <h1>CashOrbit Admin</h1>
        <p class="subtitle">Secure access to your dashboard</p>

        <?php if ($error): ?>
        <div class="alert-error">
            <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-floating input-icon">
                <i class="fas fa-user"></i>
                <input type="text" name="username" class="form-control" placeholder="Username" required autofocus>
                <label>Username</label>
            </div>
            <div class="form-floating input-icon">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" class="form-control" placeholder="Password" required>
                <label>Password</label>
            </div>
            <button type="submit" class="btn-login">
                <i class="fas fa-arrow-right-to-bracket me-2"></i>Sign In
            </button>
        </form>
    </div>
</body>
</html>
<?php
// =============================================
// CASHORBIT - ADMIN DASHBOARD (FIXED)
// =============================================
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Load config with error handling
 $configPath = __DIR__ . '/../api/config.php';
if (!file_exists($configPath)) {
    die('<div style="display:flex;align-items:center;justify-content:center;height:100vh;background:#0f172a;color:#f87171;font-family:sans-serif;text-align:center;padding:20px;"><div><h2 style="margin-bottom:10px;">Config Not Found</h2><p style="color:#94a3b8;">File /api/config.php does not exist. Upload all API files first.</p></div></div>');
}

require_once $configPath;

// Fetch stats with error handling
try {
    $total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $total_balance = $pdo->query("SELECT COALESCE(SUM(balance), 0) FROM users")->fetchColumn();
    $pending_withdrawals = $pdo->query("SELECT COUNT(*) FROM withdraw_requests WHERE status = 'pending'")->fetchColumn();
    $total_referral_earn = $pdo->query("SELECT COALESCE(SUM(referral_earn), 0) FROM users")->fetchColumn();
    $approved_withdrawals = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM withdraw_requests WHERE status = 'approved'")->fetchColumn();
    $recent_users = $pdo->query("SELECT id, name, email, balance, referral_code, created_at FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();
    $recent_withdrawals = $pdo->query("
        SELECT w.id, w.amount, w.upi_id, w.status, w.created_at, u.name 
        FROM withdraw_requests w 
        JOIN users u ON w.user_id = u.id 
        ORDER BY w.created_at DESC LIMIT 5
    ")->fetchAll();
} catch (Exception $e) {
    die('<div style="display:flex;align-items:center;justify-content:center;height:100vh;background:#0f172a;color:#f87171;font-family:sans-serif;text-align:center;padding:20px;"><div><h2 style="margin-bottom:10px;">Database Error</h2><p style="color:#94a3b8;">Tables may not exist. Run the SQL schema in phpMyAdmin.</p><p style="color:#64748b;font-size:13px;margin-top:10px;">' . htmlspecialchars($e->getMessage()) . '</p></div></div>');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CashOrbit Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0f172a;
            --bg-card: #1e293b;
            --bg-card-hover: #263348;
            --border-color: rgba(99,102,241,0.12);
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --accent: #6366f1;
            --accent-light: #818cf8;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --cyan: #06b6d4;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse at 20% 0%, rgba(99,102,241,0.06) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 100%, rgba(6,182,212,0.04) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 260px;
            background: var(--bg-card);
            border-right: 1px solid var(--border-color);
            z-index: 100;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }
        .sidebar-brand {
            padding: 24px 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sidebar-brand .icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--accent), var(--cyan));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            flex-shrink: 0;
        }
        .sidebar-brand h5 { margin: 0; font-size: 18px; font-weight: 700; color: var(--text-primary); }
        .sidebar-brand small { color: var(--text-muted); font-size: 11px; display: block; }
        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 10px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 4px;
            transition: all 0.2s;
        }
        .nav-item:hover { background: rgba(99,102,241,0.08); color: var(--text-primary); }
        .nav-item.active {
            background: linear-gradient(135deg, rgba(99,102,241,0.15), rgba(99,102,241,0.05));
            color: var(--accent-light);
            border: 1px solid rgba(99,102,241,0.2);
        }
        .nav-item i { width: 20px; text-align: center; font-size: 15px; }
        .sidebar-footer { padding: 16px 12px; border-top: 1px solid var(--border-color); }
        .main-content { margin-left: 260px; padding: 32px; position: relative; z-index: 1; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; }
        .top-bar h2 { font-size: 26px; font-weight: 700; }
        .top-bar h2 span { color: var(--accent-light); }
        .menu-toggle {
            display: none;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            width: 42px;
            height: 42px;
            border-radius: 10px;
            font-size: 18px;
            cursor: pointer;
        }
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.3);
            border-color: rgba(99,102,241,0.25);
        }
        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 80px;
            height: 80px;
            border-radius: 0 0 0 80px;
            opacity: 0.06;
        }
        .stat-card:nth-child(1)::after { background: var(--accent); }
        .stat-card:nth-child(2)::after { background: var(--success); }
        .stat-card:nth-child(3)::after { background: var(--warning); }
        .stat-card:nth-child(4)::after { background: var(--cyan); }
        .stat-card:nth-child(5)::after { background: #a855f7; }
        .stat-card:nth-child(6)::after { background: var(--danger); }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 16px;
        }
        .stat-value { font-size: 28px; font-weight: 800; margin-bottom: 4px; }
        .stat-label { color: var(--text-muted); font-size: 13px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }
        .card-panel {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            overflow: hidden;
        }
        .card-header-custom {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-header-custom h6 { margin: 0; font-weight: 600; font-size: 16px; }
        .table-dark-custom { margin: 0; color: var(--text-secondary); font-size: 13px; }
        .table-dark-custom thead th {
            background: rgba(15, 23, 42, 0.5);
            border-bottom: 1px solid var(--border-color);
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            padding: 12px 16px;
        }
        .table-dark-custom tbody td {
            padding: 12px 16px;
            border-bottom: 1px solid rgba(99,102,241,0.06);
            vertical-align: middle;
        }
        .badge-status { padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; display: inline-block; }
        .badge-pending { background: rgba(245,158,11,0.15); color: #fbbf24; }
        .badge-approved { background: rgba(34,197,94,0.15); color: #4ade80; }
        .badge-rejected { background: rgba(239,68,68,0.15); color: #f87171; }
        .overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 99; }
        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 20px; }
            .menu-toggle { display: flex; align-items: center; justify-content: center; }
            .overlay.show { display: block; }
        }
        @media (max-width: 575px) {
            .stat-value { font-size: 22px; }
            .top-bar h2 { font-size: 20px; }
        }
    </style>
</head>
<body>

<div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

<nav class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="icon"><i class="fas fa-rocket"></i></div>
        <div>
            <h5>CashOrbit</h5>
            <small>Admin Panel</small>
        </div>
    </div>
    <div class="sidebar-nav">
        <a href="dashboard.php" class="nav-item active">
            <i class="fas fa-chart-pie"></i> Dashboard
        </a>
        <a href="users.php" class="nav-item">
            <i class="fas fa-users"></i> Users
        </a>
        <a href="wallet.php" class="nav-item">
            <i class="fas fa-wallet"></i> Transactions
        </a>
        <a href="withdraw.php" class="nav-item">
            <i class="fas fa-money-bill-transfer"></i> Withdrawals
        </a>
    </div>
    <div class="sidebar-footer">
        <a href="logout.php" class="nav-item" style="color: var(--danger);">
            <i class="fas fa-right-from-bracket"></i> Logout
        </a>
    </div>
</nav>

<div class="main-content">
    <div class="top-bar">
        <div style="display:flex;align-items:center;gap:12px;">
            <button class="menu-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <h2>Welcome, <span><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></span></h2>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-4 col-xl-2">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(99,102,241,0.15);color:var(--accent-light);">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value"><?= number_format($total_users) ?></div>
                <div class="stat-label">Total Users</div>
            </div>
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(34,197,94,0.15);color:var(--success);">
                    <i class="fas fa-indian-rupee-sign"></i>
                </div>
                <div class="stat-value"><?= number_format($total_balance, 2) ?></div>
                <div class="stat-label">Total Balance</div>
            </div>
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(245,158,11,0.15);color:var(--warning);">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value"><?= number_format($pending_withdrawals) ?></div>
                <div class="stat-label">Pending Withdraw</div>
            </div>
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(6,182,212,0.15);color:var(--cyan);">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="stat-value"><?= number_format($total_referral_earn, 2) ?></div>
                <div class="stat-label">Referral Earnings</div>
            </div>
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(168,85,247,0.15);color:#a855f7;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-value"><?= number_format($approved_withdrawals, 2) ?></div>
                <div class="stat-label">Approved Payouts</div>
            </div>
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(239,68,68,0.15);color:var(--danger);">
                    <i class="fas fa-arrow-trend-up"></i>
                </div>
                <div class="stat-value"><?= number_format($total_balance - $approved_withdrawals, 2) ?></div>
                <div class="stat-label">Net Liability</div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card-panel">
                <div class="card-header-custom">
                    <h6><i class="fas fa-users me-2" style="color:var(--accent-light)"></i>Recent Users</h6>
                    <a href="users.php" style="color:var(--accent-light);text-decoration:none;font-size:13px;">View All <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
                <div class="table-responsive">
                    <table class="table table-dark-custom">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Balance</th>
                                <th>Referral Code</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_users)): ?>
                            <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted);">No users yet</td></tr>
                            <?php endif; ?>
                            <?php foreach ($recent_users as $u): ?>
                            <tr>
                                <td style="color:var(--text-primary);font-weight:500;"><?= htmlspecialchars($u['name']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td style="color:var(--success);font-weight:600;">₹<?= number_format($u['balance'], 2) ?></td>
                                <td><code style="background:rgba(99,102,241,0.1);padding:3px 8px;border-radius:6px;font-size:12px;color:var(--accent-light);"><?= htmlspecialchars($u['referral_code']) ?></code></td>
                                <td><?= date('M d', strtotime($u['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card-panel">
                <div class="card-header-custom">
                    <h6><i class="fas fa-money-bill-transfer me-2" style="color:var(--warning)"></i>Recent Withdrawals</h6>
                    <a href="withdraw.php" style="color:var(--accent-light);text-decoration:none;font-size:13px;">View All <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
                <div class="table-responsive">
                    <table class="table table-dark-custom">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Amount</th>
                                <th>UPI</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_withdrawals)): ?>
                            <tr><td colspan="4" style="text-align:center;padding:40px;color:var(--text-muted);">No withdrawals yet</td></tr>
                            <?php endif; ?>
                            <?php foreach ($recent_withdrawals as $w): ?>
                            <tr>
                                <td style="color:var(--text-primary);font-weight:500;"><?= htmlspecialchars($w['name']) ?></td>
                                <td style="font-weight:600;">₹<?= number_format($w['amount'], 2) ?></td>
                                <td style="font-size:12px;"><?= htmlspecialchars($w['upi_id']) ?></td>
                                <td><span class="badge-status badge-<?= $w['status'] ?>"><?= ucfirst($w['status']) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('overlay').classList.toggle('show');
}
</script>
</body>
</html>
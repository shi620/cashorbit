<?php
// =============================================
// CASHORBIT - USERS MANAGEMENT
// =============================================
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }

require_once __DIR__ . '/../api/config.php';

// Handle balance update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_balance') {
    $uid = intval($_POST['user_id'] ?? 0);
    $new_balance = floatval($_POST['new_balance'] ?? 0);
    
    if ($uid > 0 && $new_balance >= 0) {
        $stmt = $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
        $stmt->execute([$new_balance, $uid]);
        
        $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
        $stmt->execute([$uid]);
        echo json_encode(["status" => "success", "balance" => $stmt->fetchColumn()]);
        exit();
    }
    echo json_encode(["status" => "error"]);
    exit();
}

// Search
 $search = sanitizeString($_GET['search'] ?? '');
 $page = max(1, intval($_GET['page'] ?? 1));
 $per_page = 20;
 $offset = ($page - 1) * $per_page;

if (!empty($search)) {
    $where = "WHERE name LIKE ? OR email LIKE ? OR referral_code LIKE ?";
    $params = ["%$search%", "%$search%", "%$search%"];
} else {
    $where = "";
    $params = [];
}

 $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM users $where");
 $count_stmt->execute($params);
 $total = $count_stmt->fetchColumn();
 $total_pages = max(1, ceil($total / $per_page));

 $stmt = $pdo->prepare("SELECT * FROM users $where ORDER BY created_at DESC LIMIT $per_page OFFSET $offset");
 $stmt->execute($params);
 $users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - CashOrbit Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0f172a; --bg-card: #1e293b; --border-color: rgba(99,102,241,0.12);
            --text-primary: #f1f5f9; --text-secondary: #94a3b8; --text-muted: #64748b;
            --accent: #6366f1; --accent-light: #818cf8; --success: #22c55e; --warning: #f59e0b; --danger: #ef4444; --cyan: #06b6d4;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: var(--bg-primary); color: var(--text-primary); font-family: 'Segoe UI', system-ui, sans-serif; min-height: 100vh; }
        body::before { content: ''; position: fixed; inset: 0; background: radial-gradient(ellipse at 20% 0%, rgba(99,102,241,0.06) 0%, transparent 50%); pointer-events: none; z-index: 0; }
        .sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: 260px; background: var(--bg-card); border-right: 1px solid var(--border-color); z-index: 100; display: flex; flex-direction: column; transition: transform 0.3s; }
        .sidebar-brand { padding: 24px 20px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 12px; }
        .sidebar-brand .icon { width: 40px; height: 40px; background: linear-gradient(135deg, var(--accent), var(--cyan)); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; }
        .sidebar-brand h5 { margin: 0; font-size: 18px; font-weight: 700; }
        .sidebar-brand small { color: var(--text-muted); font-size: 11px; }
        .sidebar-nav { flex: 1; padding: 16px 12px; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 10px; color: var(--text-secondary); text-decoration: none; font-size: 14px; font-weight: 500; margin-bottom: 4px; transition: all 0.2s; }
        .nav-item:hover { background: rgba(99,102,241,0.08); color: var(--text-primary); }
        .nav-item.active { background: linear-gradient(135deg, rgba(99,102,241,0.15), rgba(99,102,241,0.05)); color: var(--accent-light); border: 1px solid rgba(99,102,241,0.2); }
        .nav-item i { width: 20px; text-align: center; }
        .sidebar-footer { padding: 16px 12px; border-top: 1px solid var(--border-color); }
        .main-content { margin-left: 260px; padding: 32px; position: relative; z-index: 1; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
        .top-bar h2 { font-size: 24px; font-weight: 700; }
        .menu-toggle { display: none; background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary); width: 42px; height: 42px; border-radius: 10px; font-size: 18px; cursor: pointer; align-items: center; justify-content: center; }
        .search-box { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 10px; padding: 10px 16px; color: var(--text-primary); font-size: 14px; width: 280px; max-width: 100%; }
        .search-box:focus { outline: none; border-color: var(--accent); }
        .card-panel { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden; }
        .table-dark-custom { margin: 0; color: var(--text-secondary); font-size: 13px; }
        .table-dark-custom thead th { background: rgba(15,23,42,0.5); border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; padding: 14px 12px; white-space: nowrap; }
        .table-dark-custom tbody td { padding: 12px; border-bottom: 1px solid rgba(99,102,241,0.06); vertical-align: middle; }
        .btn-sm-custom { padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s; }
        .btn-edit { background: rgba(99,102,241,0.15); color: var(--accent-light); }
        .btn-edit:hover { background: rgba(99,102,241,0.25); }
        code.ref { background: rgba(99,102,241,0.1); padding: 3px 8px; border-radius: 6px; font-size: 11px; color: var(--accent-light); }
        .pagination-dark { display: flex; gap: 4px; padding: 16px; justify-content: center; }
        .pagination-dark a, .pagination-dark span { padding: 8px 14px; border-radius: 8px; color: var(--text-secondary); text-decoration: none; font-size: 13px; border: 1px solid transparent; }
        .pagination-dark .active { background: var(--accent); color: white; }
        .pagination-dark a:hover { background: rgba(99,102,241,0.1); }
        .overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 99; }
        .modal-custom { display: none; position: fixed; inset: 0; z-index: 200; align-items: center; justify-content: center; }
        .modal-custom.show { display: flex; }
        .modal-box { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 32px; width: 90%; max-width: 400px; }
        .modal-box h6 { margin-bottom: 20px; font-size: 18px; }
        .modal-input { width: 100%; background: rgba(15,23,42,0.6); border: 1px solid var(--border-color); border-radius: 10px; padding: 12px 16px; color: var(--text-primary); font-size: 15px; margin-bottom: 16px; }
        .modal-input:focus { outline: none; border-color: var(--accent); }
        .btn-save { background: linear-gradient(135deg, var(--accent), #8b5cf6); color: white; border: none; padding: 12px 24px; border-radius: 10px; font-weight: 600; cursor: pointer; }
        .btn-cancel { background: transparent; border: 1px solid var(--border-color); color: var(--text-secondary); padding: 12px 24px; border-radius: 10px; cursor: pointer; }
        @media (max-width: 991px) { .sidebar { transform: translateX(-100%); } .sidebar.open { transform: translateX(0); } .main-content { margin-left: 0; padding: 20px; } .menu-toggle { display: flex; } .overlay.show { display: block; } }
        @media (max-width: 575px) { .search-box { width: 100%; } }
    </style>
</head>
<body>

<div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="icon"><i class="fas fa-rocket"></i></div>
        <div><h5>CashOrbit</h5><small>Admin Panel</small></div>
    </div>
    <div class="sidebar-nav">
        <a href="dashboard.php" class="nav-item"><i class="fas fa-chart-pie"></i> Dashboard</a>
        <a href="users.php" class="nav-item active"><i class="fas fa-users"></i> Users</a>
        <a href="wallet.php" class="nav-item"><i class="fas fa-wallet"></i> Transactions</a>
        <a href="withdraw.php" class="nav-item"><i class="fas fa-money-bill-transfer"></i> Withdrawals</a>
    </div>
    <div class="sidebar-footer">
        <a href="logout.php" class="nav-item" style="color:var(--danger)"><i class="fas fa-right-from-bracket"></i> Logout</a>
    </div>
</nav>

<!-- Balance Edit Modal -->
<div class="modal-custom" id="balanceModal">
    <div class="modal-box">
        <h6><i class="fas fa-wallet me-2" style="color:var(--accent-light)"></i>Update Balance</h6>
        <p style="color:var(--text-muted);font-size:13px;margin-bottom:16px;" id="modalUserInfo"></p>
        <input type="number" step="0.01" min="0" class="modal-input" id="newBalanceInput" placeholder="Enter new balance">
        <input type="hidden" id="modalUserId">
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button class="btn-cancel" onclick="closeModal()">Cancel</button>
            <button class="btn-save" onclick="saveBalance()">Save</button>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="top-bar">
        <div style="display:flex;align-items:center;gap:12px;">
            <button class="menu-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
            <h2><i class="fas fa-users me-2" style="color:var(--accent-light)"></i>Users <span style="color:var(--text-muted);font-size:16px;font-weight:400;">(<?= number_format($total) ?>)</span></h2>
        </div>
        <form method="GET">
            <input type="text" name="search" class="search-box" placeholder="Search name, email, referral..." value="<?= htmlspecialchars($search) ?>">
        </form>
    </div>

    <div class="card-panel">
        <div class="table-responsive">
            <table class="table table-dark-custom">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Balance</th>
                        <th>Referral Code</th>
                        <th>Referred By</th>
                        <th>Ref. Count</th>
                        <th>Ref. Earn</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                    <tr><td colspan="10" style="text-align:center;padding:40px;color:var(--text-muted);">No users found</td></tr>
                    <?php endif; ?>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td style="color:var(--text-muted);">#<?= $u['id'] ?></td>
                        <td style="color:var(--text-primary);font-weight:600;"><?= htmlspecialchars($u['name']) ?></td>
                        <td style="font-size:12px;"><?= htmlspecialchars($u['email']) ?></td>
                        <td style="color:var(--success);font-weight:700;">₹<?= number_format($u['balance'], 2) ?></td>
                        <td><code class="ref"><?= $u['referral_code'] ?></code></td>
                        <td><?= $u['referred_by'] ? '<code class="ref">' . htmlspecialchars($u['referred_by']) . '</code>' : '<span style="color:var(--text-muted)">—</span>' ?></td>
                        <td style="text-align:center;font-weight:600;"><?= $u['referral_count'] ?></td>
                        <td style="color:var(--cyan);font-weight:600;">₹<?= number_format($u['referral_earn'], 2) ?></td>
                        <td style="font-size:12px;white-space:nowrap;"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                        <td>
                            <button class="btn-sm-custom btn-edit" onclick="openModal(<?= $u['id'] ?>, '<?= htmlspecialchars(addslashes($u['name'])) ?>', <?= $u['balance'] ?>)">
                                <i class="fas fa-pen me-1"></i>Edit
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if ($total_pages > 1): ?>
        <div class="pagination-dark">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="active"><?= $i ?></span>
                <?php else: ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('overlay').classList.toggle('show');
}
function openModal(uid, name, balance) {
    document.getElementById('modalUserId').value = uid;
    document.getElementById('modalUserInfo').textContent = name + ' — Current: ₹' + parseFloat(balance).toFixed(2);
    document.getElementById('newBalanceInput').value = balance;
    document.getElementById('balanceModal').classList.add('show');
}
function closeModal() {
    document.getElementById('balanceModal').classList.remove('show');
}
function saveBalance() {
    const uid = document.getElementById('modalUserId').value;
    const val = document.getElementById('newBalanceInput').value;
    if (val === '' || parseFloat(val) < 0) { alert('Enter valid balance'); return; }
    const fd = new FormData();
    fd.append('action', 'update_balance');
    fd.append('user_id', uid);
    fd.append('new_balance', val);
    fetch('users.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(d => {
            if (d.status === 'success') { location.reload(); }
            else { alert('Failed to update'); }
        });
}
</script>
</body>
</html>
<?php
// =============================================
// CASHORBIT - TRANSACTIONS VIEWER
// =============================================
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }

require_once __DIR__ . '/../api/config.php';

 $search = sanitizeString($_GET['search'] ?? '');
 $type_filter = sanitizeString($_GET['type'] ?? '');
 $page = max(1, intval($_GET['page'] ?? 1));
 $per_page = 30;
 $offset = ($page - 1) * $per_page;

 $conditions = [];
 $params = [];

if (!empty($search)) {
    $conditions[] = "(u.name LIKE ? OR u.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($type_filter) && in_array($type_filter, ['add', 'deduct', 'referral'])) {
    $conditions[] = "t.type = ?";
    $params[] = $type_filter;
}

 $where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

 $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM transactions t JOIN users u ON t.user_id = u.id $where");
 $count_stmt->execute($params);
 $total = $count_stmt->fetchColumn();
 $total_pages = max(1, ceil($total / $per_page));

 $stmt = $pdo->prepare("
    SELECT t.*, u.name, u.email 
    FROM transactions t 
    JOIN users u ON t.user_id = u.id 
    $where 
    ORDER BY t.created_at DESC 
    LIMIT $per_page OFFSET $offset
");
 $stmt->execute($params);
 $transactions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - CashOrbit Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --bg-primary:#0f172a; --bg-card:#1e293b; --border-color:rgba(99,102,241,0.12); --text-primary:#f1f5f9; --text-secondary:#94a3b8; --text-muted:#64748b; --accent:#6366f1; --accent-light:#818cf8; --success:#22c55e; --danger:#ef4444; --cyan:#06b6d4; --warning:#f59e0b; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { background:var(--bg-primary); color:var(--text-primary); font-family:'Segoe UI',system-ui,sans-serif; min-height:100vh; }
        body::before { content:''; position:fixed; inset:0; background:radial-gradient(ellipse at 20% 0%,rgba(99,102,241,0.06) 0%,transparent 50%); pointer-events:none; }
        .sidebar { position:fixed; left:0; top:0; bottom:0; width:260px; background:var(--bg-card); border-right:1px solid var(--border-color); z-index:100; display:flex; flex-direction:column; transition:transform 0.3s; }
        .sidebar-brand { padding:24px 20px; border-bottom:1px solid var(--border-color); display:flex; align-items:center; gap:12px; }
        .sidebar-brand .icon { width:40px; height:40px; background:linear-gradient(135deg,var(--accent),var(--cyan)); border-radius:10px; display:flex; align-items:center; justify-content:center; color:white; font-size:18px; }
        .sidebar-brand h5 { margin:0; font-size:18px; font-weight:700; }
        .sidebar-brand small { color:var(--text-muted); font-size:11px; }
        .sidebar-nav { flex:1; padding:16px 12px; }
        .nav-item { display:flex; align-items:center; gap:12px; padding:12px 16px; border-radius:10px; color:var(--text-secondary); text-decoration:none; font-size:14px; font-weight:500; margin-bottom:4px; transition:all 0.2s; }
        .nav-item:hover { background:rgba(99,102,241,0.08); color:var(--text-primary); }
        .nav-item.active { background:linear-gradient(135deg,rgba(99,102,241,0.15),rgba(99,102,241,0.05)); color:var(--accent-light); border:1px solid rgba(99,102,241,0.2); }
        .nav-item i { width:20px; text-align:center; }
        .sidebar-footer { padding:16px 12px; border-top:1px solid var(--border-color); }
        .main-content { margin-left:260px; padding:32px; position:relative; z-index:1; }
        .top-bar { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
        .top-bar h2 { font-size:24px; font-weight:700; }
        .menu-toggle { display:none; background:var(--bg-card); border:1px solid var(--border-color); color:var(--text-primary); width:42px; height:42px; border-radius:10px; font-size:18px; cursor:pointer; align-items:center; justify-content:center; }
        .filters { display:flex; gap:10px; flex-wrap:wrap; }
        .search-box, .filter-select { background:var(--bg-card); border:1px solid var(--border-color); border-radius:10px; padding:10px 16px; color:var(--text-primary); font-size:14px; }
        .filter-select { cursor:pointer; }
        .search-box:focus, .filter-select:focus { outline:none; border-color:var(--accent); }
        .filter-select option { background:var(--bg-card); }
        .card-panel { background:var(--bg-card); border:1px solid var(--border-color); border-radius:16px; overflow:hidden; }
        .table-dark-custom { margin:0; color:var(--text-secondary); font-size:13px; }
        .table-dark-custom thead th { background:rgba(15,23,42,0.5); border-bottom:1px solid var(--border-color); color:var(--text-muted); font-weight:600; text-transform:uppercase; font-size:11px; letter-spacing:0.5px; padding:14px 12px; white-space:nowrap; }
        .table-dark-custom tbody td { padding:12px; border-bottom:1px solid rgba(99,102,241,0.06); vertical-align:middle; }
        .badge-type { padding:4px 10px; border-radius:6px; font-size:11px; font-weight:600; }
        .badge-add { background:rgba(34,197,94,0.15); color:#4ade80; }
        .badge-deduct { background:rgba(239,68,68,0.15); color:#f87171; }
        .badge-referral { background:rgba(6,182,212,0.15); color:#22d3ee; }
        .badge-success { background:rgba(34,197,94,0.15); color:#4ade80; }
        .badge-failed { background:rgba(239,68,68,0.15); color:#f87171; }
        .badge-pending { background:rgba(245,158,11,0.15); color:#fbbf24; }
        .pagination-dark { display:flex; gap:4px; padding:16px; justify-content:center; }
        .pagination-dark a,.pagination-dark span { padding:8px 14px; border-radius:8px; color:var(--text-secondary); text-decoration:none; font-size:13px; }
        .pagination-dark .active { background:var(--accent); color:white; }
        .pagination-dark a:hover { background:rgba(99,102,241,0.1); }
        .overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:99; }
        @media(max-width:991px){ .sidebar{transform:translateX(-100%)} .sidebar.open{transform:translateX(0)} .main-content{margin-left:0;padding:20px} .menu-toggle{display:flex} .overlay.show{display:block} }
    </style>
</head>
<body>

<div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

<nav class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="icon"><i class="fas fa-rocket"></i></div>
        <div><h5>CashOrbit</h5><small>Admin Panel</small></div>
    </div>
    <div class="sidebar-nav">
        <a href="dashboard.php" class="nav-item"><i class="fas fa-chart-pie"></i> Dashboard</a>
        <a href="users.php" class="nav-item"><i class="fas fa-users"></i> Users</a>
        <a href="wallet.php" class="nav-item active"><i class="fas fa-wallet"></i> Transactions</a>
        <a href="withdraw.php" class="nav-item"><i class="fas fa-money-bill-transfer"></i> Withdrawals</a>
    </div>
    <div class="sidebar-footer">
        <a href="logout.php" class="nav-item" style="color:var(--danger)"><i class="fas fa-right-from-bracket"></i> Logout</a>
    </div>
</nav>

<div class="main-content">
    <div class="top-bar">
        <div style="display:flex;align-items:center;gap:12px;">
            <button class="menu-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
            <h2><i class="fas fa-wallet me-2" style="color:var(--accent-light)"></i>Transactions <span style="color:var(--text-muted);font-size:16px;font-weight:400;">(<?= number_format($total) ?>)</span></h2>
        </div>
    </div>

    <form method="GET" class="filters" style="margin-bottom:20px;">
        <input type="text" name="search" class="search-box" placeholder="Search user..." value="<?= htmlspecialchars($search) ?>" style="flex:1;min-width:180px;">
        <select name="type" class="filter-select">
            <option value="">All Types</option>
            <option value="add" <?= $type_filter === 'add' ? 'selected' : '' ?>>Add</option>
            <option value="deduct" <?= $type_filter === 'deduct' ? 'selected' : '' ?>>Deduct</option>
            <option value="referral" <?= $type_filter === 'referral' ? 'selected' : '' ?>>Referral</option>
        </select>
        <button type="submit" style="background:var(--accent);color:white;border:none;padding:10px 20px;border-radius:10px;cursor:pointer;font-weight:600;"><i class="fas fa-filter me-1"></i>Filter</button>
    </form>

    <div class="card-panel">
        <div class="table-responsive">
            <table class="table table-dark-custom">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Description</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                    <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">No transactions found</td></tr>
                    <?php endif; ?>
                    <?php foreach ($transactions as $t): ?>
                    <tr>
                        <td style="color:var(--text-muted);">#<?= $t['id'] ?></td>
                        <td>
                            <div style="color:var(--text-primary);font-weight:600;"><?= htmlspecialchars($t['name']) ?></div>
                            <div style="font-size:11px;color:var(--text-muted);"><?= htmlspecialchars($t['email']) ?></div>
                        </td>
                        <td style="font-weight:700; color:<?= $t['type'] === 'deduct' ? 'var(--danger)' : 'var(--success)' ?>;">
                            <?= $t['type'] === 'deduct' ? '-' : '+' ?>₹<?= number_format($t['amount'], 2) ?>
                        </td>
                        <td><span class="badge-type badge-<?= $t['type'] ?>"><?= ucfirst($t['type']) ?></span></td>
                        <td><span class="badge-type badge-<?= $t['status'] ?>"><?= ucfirst($t['status']) ?></span></td>
                        <td style="max-width:200px;font-size:12px;color:var(--text-muted);"><?= htmlspecialchars($t['description'] ?? '—') ?></td>
                        <td style="white-space:nowrap;font-size:12px;"><?= date('M d, H:i', strtotime($t['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if ($total_pages > 1): ?>
        <div class="pagination-dark">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?= $i == $page ? '<span class="active">'.$i.'</span>' : '<a href="?page='.$i.'&search='.urlencode($search).'&type='.urlencode($type_filter).'">'.$i.'</a>' ?>
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
</script>
</body>
</html>
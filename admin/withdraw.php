<?php
// =============================================
// CASHORBIT - WITHDRAWAL MANAGEMENT
// =============================================
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }

require_once __DIR__ . '/../api/config.php';

// Handle approve/reject
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $wid = intval($_POST['withdraw_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    $note = sanitizeString($_POST['note'] ?? '');

    if ($wid > 0 && in_array($action, ['approve', 'reject'])) {
        $new_status = $action === 'approve' ? 'approved' : 'rejected';

        $stmt = $pdo->prepare("SELECT * FROM withdraw_requests WHERE id = ? AND status = 'pending'");
        $stmt->execute([$wid]);
        $withdraw = $stmt->fetch();

        if ($withdraw) {
            if ($action === 'reject') {
                // Refund amount to user
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("UPDATE withdraw_requests SET status = 'rejected', admin_note = ?, processed_at = NOW() WHERE id = ?");
                $stmt->execute([$note, $wid]);

                $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
                $stmt->execute([$withdraw['amount'], $withdraw['user_id']]);

                $stmt = $pdo->prepare("
                    INSERT INTO transactions (user_id, amount, type, status, description)
                    VALUES (?, ?, 'add', 'success', ?)
                ");
                $stmt->execute([$withdraw['user_id'], $withdraw['amount'], "Withdrawal rejected - ₹{$withdraw['amount']} refunded"]);

                $pdo->commit();
            } else {
                $stmt = $pdo->prepare("UPDATE withdraw_requests SET status = 'approved', admin_note = ?, processed_at = NOW() WHERE id = ?");
                $stmt->execute([$note, $wid]);
            }

            header("Location: withdraw.php?msg=" . ($action === 'approve' ? 'approved' : 'rejected'));
            exit();
        }
    }
    header("Location: withdraw.php?error=1");
    exit();
}

 $status_filter = sanitizeString($_GET['status'] ?? '');
 $page = max(1, intval($_GET['page'] ?? 1));
 $per_page = 20;
 $offset = ($page - 1) * $per_page;

 $conditions = [];
 $params = [];

if (!empty($status_filter) && in_array($status_filter, ['pending', 'approved', 'rejected'])) {
    $conditions[] = "w.status = ?";
    $params[] = $status_filter;
}

 $where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

 $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM withdraw_requests w $where");
 $count_stmt->execute($params);
 $total = $count_stmt->fetchColumn();
 $total_pages = max(1, ceil($total / $per_page));

 $stmt = $pdo->prepare("
    SELECT w.*, u.name, u.email, u.referral_code 
    FROM withdraw_requests w 
    JOIN users u ON w.user_id = u.id 
    $where 
    ORDER BY 
        CASE WHEN w.status = 'pending' THEN 0 ELSE 1 END,
        w.created_at DESC 
    LIMIT $per_page OFFSET $offset
");
 $stmt->execute($params);
 $withdrawals = $stmt->fetchAll();

// Summary stats
 $pending_total = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM withdraw_requests WHERE status='pending'")->fetchColumn();
 $approved_total = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM withdraw_requests WHERE status='approved'")->fetchColumn();
 $rejected_total = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM withdraw_requests WHERE status='rejected'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdrawals - CashOrbit Admin</title>
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
        .mini-stat { background:var(--bg-card); border:1px solid var(--border-color); border-radius:12px; padding:16px 20px; }
        .mini-stat .label { font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px; }
        .mini-stat .value { font-size:20px; font-weight:700; }
        .filter-btn { background:var(--bg-card); border:1px solid var(--border-color); color:var(--text-secondary); padding:8px 16px; border-radius:8px; cursor:pointer; font-size:13px; font-weight:500; transition:all 0.2s; text-decoration:none; }
        .filter-btn:hover,.filter-btn.active { background:rgba(99,102,241,0.15); color:var(--accent-light); border-color:rgba(99,102,241,0.3); }
        .card-panel { background:var(--bg-card); border:1px solid var(--border-color); border-radius:16px; overflow:hidden; }
        .table-dark-custom { margin:0; color:var(--text-secondary); font-size:13px; }
        .table-dark-custom thead th { background:rgba(15,23,42,0.5); border-bottom:1px solid var(--border-color); color:var(--text-muted); font-weight:600; text-transform:uppercase; font-size:11px; letter-spacing:0.5px; padding:14px 12px; white-space:nowrap; }
        .table-dark-custom tbody td { padding:12px; border-bottom:1px solid rgba(99,102,241,0.06); vertical-align:middle; }
        .badge-status { padding:4px 10px; border-radius:6px; font-size:11px; font-weight:600; }
        .badge-pending { background:rgba(245,158,11,0.15); color:#fbbf24; }
        .badge-approved { background:rgba(34,197,94,0.15); color:#4ade80; }
        .badge-rejected { background:rgba(239,68,68,0.15); color:#f87171; }
        .btn-action { padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600; border:none; cursor:pointer; transition:all 0.2s; }
        .btn-approve { background:rgba(34,197,94,0.15); color:#4ade80; }
        .btn-approve:hover { background:rgba(34,197,94,0.3); }
        .btn-reject { background:rgba(239,68,68,0.15); color:#f87171; }
        .btn-reject:hover { background:rgba(239,68,68,0.3); }
        .toast-msg { position:fixed; top:24px; right:24px; z-index:300; padding:14px 24px; border-radius:12px; font-size:14px; font-weight:500; animation:slideIn 0.3s ease; }
        @keyframes slideIn { from{transform:translateX(100%);opacity:0} to{transform:translateX(0);opacity:1} }
        .modal-custom { display:none; position:fixed; inset:0; z-index:200; align-items:center; justify-content:center; background:rgba(0,0,0,0.6); }
        .modal-custom.show { display:flex; }
        .modal-box { background:var(--bg-card); border:1px solid var(--border-color); border-radius:16px; padding:32px; width:90%; max-width:420px; }
        .modal-box h6 { margin-bottom:20px; font-size:18px; }
        .modal-input { width:100%; background:rgba(15,23,42,0.6); border:1px solid var(--border-color); border-radius:10px; padding:12px 16px; color:var(--text-primary); font-size:14px; margin-bottom:16px; }
        .modal-input:focus { outline:none; border-color:var(--accent); }
        .btn-save { background:linear-gradient(135deg,var(--success),#16a34a); color:white; border:none; padding:12px 24px; border-radius:10px; font-weight:600; cursor:pointer; }
        .btn-danger-save { background:linear-gradient(135deg,var(--danger),#dc2626); color:white; border:none; padding:12px 24px; border-radius:10px; font-weight:600; cursor:pointer; }
        .btn-cancel { background:transparent; border:1px solid var(--border-color); color:var(--text-secondary); padding:12px 24px; border-radius:10px; cursor:pointer; }
        .pagination-dark { display:flex; gap:4px; padding:16px; justify-content:center; }
        .pagination-dark a,.pagination-dark span { padding:8px 14px; border-radius:8px; color:var(--text-secondary); text-decoration:none; font-size:13px; }
        .pagination-dark .active { background:var(--accent); color:white; }
        .overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:99; }
        @media(max-width:991px){ .sidebar{transform:translateX(-100%)} .sidebar.open{transform:translateX(0)} .main-content{margin-left:0;padding:20px} .menu-toggle{display:flex} .overlay.show{display:block} }
    </style>
</head>
<body>

<?php if (isset($_GET['msg'])): ?>
<div class="toast-msg" style="background:rgba(34,197,94,0.15);border:1px solid rgba(34,197,94,0.3);color:#4ade80;">
    <i class="fas fa-check-circle me-2"></i>Withdrawal <?= htmlspecialchars($_GET['msg']) ?> successfully
</div>
<?php endif; ?>

<div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

<nav class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="icon"><i class="fas fa-rocket"></i></div>
        <div><h5>CashOrbit</h5><small>Admin Panel</small></div>
    </div>
    <div class="sidebar-nav">
        <a href="dashboard.php" class="nav-item"><i class="fas fa-chart-pie"></i> Dashboard</a>
        <a href="users.php" class="nav-item"><i class="fas fa-users"></i> Users</a>
        <a href="wallet.php" class="nav-item"><i class="fas fa-wallet"></i> Transactions</a>
        <a href="withdraw.php" class="nav-item active"><i class="fas fa-money-bill-transfer"></i> Withdrawals</a>
    </div>
    <div class="sidebar-footer">
        <a href="logout.php" class="nav-item" style="color:var(--danger)"><i class="fas fa-right-from-bracket"></i> Logout</a>
    </div>
</nav>

<!-- Approve Modal -->
<div class="modal-custom" id="approveModal">
    <div class="modal-box">
        <h6 style="color:var(--success);"><i class="fas fa-check-circle me-2"></i>Approve Withdrawal</h6>
        <p style="color:var(--text-muted);font-size:13px;" id="approveInfo"></p>
        <input type="text" class="modal-input" id="approveNote" placeholder="Optional note (e.g. TXN ID)">
        <input type="hidden" id="approveWid">
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button class="btn-cancel" onclick="closeModals()">Cancel</button>
            <button class="btn-save" onclick="submitAction('approve')"><i class="fas fa-check me-1"></i>Approve</button>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal-custom" id="rejectModal">
    <div class="modal-box">
        <h6 style="color:var(--danger);"><i class="fas fa-times-circle me-2"></i>Reject Withdrawal</h6>
        <p style="color:var(--text-muted);font-size:13px;" id="rejectInfo"></p>
        <input type="text" class="modal-input" id="rejectNote" placeholder="Reason for rejection" required>
        <input type="hidden" id="rejectWid">
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button class="btn-cancel" onclick="closeModals()">Cancel</button>
            <button class="btn-danger-save" onclick="submitAction('reject')"><i class="fas fa-times me-1"></i>Reject</button>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="top-bar">
        <div style="display:flex;align-items:center;gap:12px;">
            <button class="menu-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
            <h2><i class="fas fa-money-bill-transfer me-2" style="color:var(--warning)"></i>Withdrawals</h2>
        </div>
    </div>

    <!-- Mini Stats -->
    <div class="row g-3 mb-4">
        <div class="col-4">
            <div class="mini-stat">
                <div class="label">Pending</div>
                <div class="value" style="color:var(--warning);">₹<?= number_format($pending_total, 2) ?></div>
            </div>
        </div>
        <div class="col-4">
            <div class="mini-stat">
                <div class="label">Approved</div>
                <div class="value" style="color:var(--success);">₹<?= number_format($approved_total, 2) ?></div>
            </div>
        </div>
        <div class="col-4">
            <div class="mini-stat">
                <div class="label">Rejected</div>
                <div class="value" style="color:var(--danger);">₹<?= number_format($rejected_total, 2) ?></div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
        <a href="withdraw.php" class="filter-btn <?= empty($status_filter) ? 'active' : '' ?>">All</a>
        <a href="withdraw.php?status=pending" class="filter-btn <?= $status_filter === 'pending' ? 'active' : '' ?>">
            <i class="fas fa-clock me-1"></i>Pending
        </a>
        <a href="withdraw.php?status=approved" class="filter-btn <?= $status_filter === 'approved' ? 'active' : '' ?>">
            <i class="fas fa-check me-1"></i>Approved
        </a>
        <a href="withdraw.php?status=rejected" class="filter-btn <?= $status_filter === 'rejected' ? 'active' : '' ?>">
            <i class="fas fa-times me-1"></i>Rejected
        </a>
    </div>

    <div class="card-panel">
        <div class="table-responsive">
            <table class="table table-dark-custom">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>UPI ID</th>
                        <th>Status</th>
                        <th>Note</th>
                        <th>Requested</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($withdrawals)): ?>
                    <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">No withdrawals found</td></tr>
                    <?php endif; ?>
                    <?php foreach ($withdrawals as $w): ?>
                    <tr>
                        <td style="color:var(--text-muted);">#<?= $w['id'] ?></td>
                        <td>
                            <div style="color:var(--text-primary);font-weight:600;"><?= htmlspecialchars($w['name']) ?></div>
                            <div style="font-size:11px;color:var(--text-muted);"><?= htmlspecialchars($w['email']) ?></div>
                        </td>
                        <td style="font-weight:700;color:var(--text-primary);">₹<?= number_format($w['amount'], 2) ?></td>
                        <td style="font-size:12px;"><?= htmlspecialchars($w['upi_id']) ?></td>
                        <td><span class="badge-status badge-<?= $w['status'] ?>"><?= ucfirst($w['status']) ?></span></td>
                        <td style="font-size:12px;color:var(--text-muted);max-width:150px;"><?= htmlspecialchars($w['admin_note'] ?? '—') ?></td>
                        <td style="white-space:nowrap;font-size:12px;"><?= date('M d, H:i', strtotime($w['created_at'])) ?></td>
                        <td>
                            <?php if ($w['status'] === 'pending'): ?>
                            <div style="display:flex;gap:6px;">
                                <button class="btn-action btn-approve" onclick="openApprove(<?= $w['id'] ?>, '<?= htmlspecialchars(addslashes($w['name'])) ?>', <?= $w['amount'] ?>)">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn-action btn-reject" onclick="openReject(<?= $w['id'] ?>, '<?= htmlspecialchars(addslashes($w['name'])) ?>', <?= $w['amount'] ?>)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <?php else: ?>
                            <span style="color:var(--text-muted);font-size:12px;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if ($total_pages > 1): ?>
        <div class="pagination-dark">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?= $i == $page ? '<span class="active">'.$i.'</span>' : '<a href="?page='.$i.'&status='.urlencode($status_filter).'">'.$i.'</a>' ?>
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
function openApprove(wid, name, amt) {
    document.getElementById('approveWid').value = wid;
    document.getElementById('approveInfo').textContent = name + ' — ₹' + amt.toFixed(2);
    document.getElementById('approveNote').value = '';
    document.getElementById('approveModal').classList.add('show');
}
function openReject(wid, name, amt) {
    document.getElementById('rejectWid').value = wid;
    document.getElementById('rejectInfo').textContent = name + ' — ₹' + amt.toFixed(2) + ' (amount will be refunded)';
    document.getElementById('rejectNote').value = '';
    document.getElementById('rejectModal').classList.add('show');
}
function closeModals() {
    document.getElementById('approveModal').classList.remove('show');
    document.getElementById('rejectModal').classList.remove('show');
}
function submitAction(type) {
    const wid = document.getElementById(type + 'Wid').value;
    const note = document.getElementById(type + 'Note').value;
    const fd = new FormData();
    fd.append('action', type);
    fd.append('withdraw_id', wid);
    fd.append('note', note);
    fetch('withdraw.php', { method: 'POST', body: fd })
        .then(r => r.redirected ? window.location.href = r.url : location.reload())
        .catch(() => location.reload());
}
</script>
</body>
</html>

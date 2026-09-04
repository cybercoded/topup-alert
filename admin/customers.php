<?php
$pageTitle = 'Customers';
$currentPage = 'customers';
require_once __DIR__ . '/../includes/header.php';

$db = getDB();

// Filters
$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$where = [];
$params = [];

if ($search) {
    $where[] = "(first_name LIKE ? OR last_name LIKE ? OR phone LIKE ? OR email LIKE ? OR customer_code LIKE ?)";
    $term = "%$search%";
    $params = array_merge($params, [$term, $term, $term, $term, $term]);
}
if ($status && in_array($status, ['active', 'inactive', 'suspended'])) {
    $where[] = "status = ?";
    $params[] = $status;
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$countStmt = $db->prepare("SELECT COUNT(*) FROM customers $whereSQL");
$countStmt->execute($params);
$total = $countStmt->fetchColumn();
$totalPages = max(1, ceil($total / $perPage));

$stmt = $db->prepare("SELECT * FROM customers $whereSQL ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$customers = $stmt->fetchAll();
?>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-users" style="margin-right:0.4rem;color:var(--primary);"></i> All Customers (<?= number_format($total) ?>)</h3>
        <div class="filters">
            <form method="GET" style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:center;">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="form-control" placeholder="Search name, phone, email..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    <option value="suspended" <?= $status === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
                <?php if ($search || $status): ?>
                    <a href="customers.php" class="btn btn-secondary btn-sm">Clear</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Location</th>
                        <th>Wallet Balance</th>
                        <th>Status</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($customers)): ?>
                        <tr><td colspan="7"><div class="empty-state"><i class="fas fa-users"></i><p>No customers found</p></div></td></tr>
                    <?php else: ?>
                        <?php foreach ($customers as $c): ?>
                        <tr>
                            <td class="fw-600" style="font-family:monospace;font-size:0.8rem;"><?= htmlspecialchars($c['customer_code']) ?></td>
                            <td>
                                <div class="fw-600"><?= htmlspecialchars($c['first_name'] . ' ' . $c['last_name']) ?></div>
                                <div class="text-muted" style="font-size:0.75rem;"><?= htmlspecialchars($c['email'] ?? '—') ?></div>
                            </td>
                            <td><?= htmlspecialchars($c['phone']) ?></td>
                            <td>
                                <div><?= htmlspecialchars($c['city']) ?></div>
                                <div class="text-muted" style="font-size:0.75rem;"><?= htmlspecialchars($c['state']) ?></div>
                            </td>
                            <td class="fw-700" style="color:var(--primary);"><?= formatNaira($c['wallet_balance']) ?></td>
                            <td>
                                <?php
                                $badge = match($c['status']) {
                                    'active' => 'badge-success',
                                    'inactive' => 'badge-gray',
                                    'suspended' => 'badge-danger',
                                    default => 'badge-gray'
                                };
                                ?>
                                <span class="badge <?= $badge ?>"><?= ucfirst($c['status']) ?></span>
                            </td>
                            <td class="text-muted" style="font-size:0.8rem;"><?= date('d M Y', strtotime($c['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= min($totalPages, 10); $i++): ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>" 
                   class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

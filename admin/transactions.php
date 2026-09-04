<?php
$pageTitle = 'Transactions';
$currentPage = 'transactions';
require_once __DIR__ . '/../includes/header.php';

$db = getDB();

$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';
$service = $_GET['service'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$where = [];
$params = [];

if ($search) {
    $where[] = "(t.transaction_ref LIKE ? OR c.first_name LIKE ? OR c.last_name LIKE ? OR t.beneficiary_phone LIKE ?)";
    $term = "%$search%";
    $params = array_merge($params, [$term, $term, $term, $term]);
}
if ($status && in_array($status, ['pending', 'successful', 'failed', 'reversed'])) {
    $where[] = "t.status = ?";
    $params[] = $status;
}
if ($service && is_numeric($service)) {
    $where[] = "t.service_type_id = ?";
    $params[] = (int)$service;
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$countStmt = $db->prepare("
    SELECT COUNT(*) FROM transactions t
    JOIN customers c ON c.id = t.customer_id
    $whereSQL
");
$countStmt->execute($params);
$total = $countStmt->fetchColumn();
$totalPages = max(1, ceil($total / $perPage));

$stmt = $db->prepare("
    SELECT t.*, c.first_name, c.last_name, c.phone as customer_phone, st.name as service_name, st.code as service_code
    FROM transactions t
    JOIN customers c ON c.id = t.customer_id
    JOIN service_types st ON st.id = t.service_type_id
    $whereSQL
    ORDER BY t.created_at DESC
    LIMIT $perPage OFFSET $offset
");
$stmt->execute($params);
$transactions = $stmt->fetchAll();

$services = $db->query("SELECT id, name FROM service_types")->fetchAll();
?>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-exchange-alt" style="margin-right:0.4rem;color:var(--info);"></i> All Transactions (<?= number_format($total) ?>)</h3>
        <div class="filters">
            <form method="GET" style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:center;">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="form-control" placeholder="Ref, customer, phone..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="successful" <?= $status === 'successful' ? 'selected' : '' ?>>Successful</option>
                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="failed" <?= $status === 'failed' ? 'selected' : '' ?>>Failed</option>
                    <option value="reversed" <?= $status === 'reversed' ? 'selected' : '' ?>>Reversed</option>
                </select>
                <select name="service" class="form-control" onchange="this.form.submit()">
                    <option value="">All Services</option>
                    <?php foreach ($services as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= $service == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
                <?php if ($search || $status || $service): ?>
                    <a href="transactions.php" class="btn btn-secondary btn-sm">Clear</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Details</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                        <tr><td colspan="8"><div class="empty-state"><i class="fas fa-exchange-alt"></i><p>No transactions found</p></div></td></tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $t): 
                            $iconClass = match($t['service_code']) {
                                'AIRTIME' => 'service-airtime',
                                'DATA' => 'service-data',
                                'ELECTRICITY' => 'service-electricity',
                                'CABLE' => 'service-cable',
                                default => ''
                            };
                            $icon = match($t['service_code']) {
                                'AIRTIME' => 'fa-mobile-alt',
                                'DATA' => 'fa-wifi',
                                'ELECTRICITY' => 'fa-bolt',
                                'CABLE' => 'fa-tv',
                                default => 'fa-circle'
                            };
                            $badge = match($t['status']) {
                                'successful' => 'badge-success',
                                'pending' => 'badge-pending',
                                'failed' => 'badge-danger',
                                'reversed' => 'badge-gray',
                                default => 'badge-gray'
                            };
                        ?>
                        <tr>
                            <td class="fw-600" style="font-family:monospace;font-size:0.78rem;"><?= htmlspecialchars($t['transaction_ref']) ?></td>
                            <td>
                                <div class="fw-600"><?= htmlspecialchars($t['first_name'] . ' ' . $t['last_name']) ?></div>
                                <div class="text-muted" style="font-size:0.75rem;"><?= htmlspecialchars($t['customer_phone']) ?></div>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:0.45rem;">
                                    <div class="service-icon <?= $iconClass ?>" style="width:28px;height:28px;font-size:0.75rem;"><i class="fas <?= $icon ?>"></i></div>
                                    <span style="font-size:0.85rem;"><?= htmlspecialchars($t['service_name']) ?></span>
                                </div>
                            </td>
                            <td style="font-size:0.8rem;max-width:180px;">
                                <?php if ($t['network']): ?>
                                    <span class="badge badge-primary"><?= htmlspecialchars($t['network']) ?></span>
                                <?php endif; ?>
                                <?php if ($t['beneficiary_phone']): ?>
                                    <div class="text-muted"><?= htmlspecialchars($t['beneficiary_phone']) ?></div>
                                <?php endif; ?>
                                <?php if ($t['data_plan']): ?>
                                    <div class="text-muted"><?= htmlspecialchars($t['data_plan']) ?></div>
                                <?php endif; ?>
                                <?php if ($t['disco']): ?>
                                    <span class="badge badge-info"><?= htmlspecialchars($t['disco']) ?></span>
                                    <div class="text-muted"><?= htmlspecialchars($t['beneficiary_meter'] ?? '') ?></div>
                                <?php endif; ?>
                                <?php if ($t['cable_provider']): ?>
                                    <span class="badge badge-info"><?= htmlspecialchars($t['cable_provider']) ?></span>
                                    <div class="text-muted"><?= htmlspecialchars($t['beneficiary_smartcard'] ?? '') ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-700"><?= formatNaira($t['total_amount']) ?></div>
                                <?php if ($t['fee'] > 0): ?>
                                    <div class="text-muted" style="font-size:0.7rem;">Fee: <?= formatNaira($t['fee']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge badge-gray"><?= ucfirst(str_replace('_', ' ', $t['payment_method'])) ?></span></td>
                            <td><span class="badge <?= $badge ?>"><?= ucfirst($t['status']) ?></span></td>
                            <td class="text-muted" style="font-size:0.8rem;white-space:nowrap;"><?= date('d M Y, H:i', strtotime($t['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= min($totalPages, 10); $i++): ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&service=<?= urlencode($service) ?>" 
                   class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

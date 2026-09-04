<?php
$pageTitle = 'Transaction History';
$currentPage = 'history';
require_once __DIR__ . '/includes/header.php';

$db = getDB();
$customerId = $_SESSION['customer_id'];

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

$countStmt = $db->prepare("SELECT COUNT(*) FROM transactions WHERE customer_id = ?");
$countStmt->execute([$customerId]);
$total = $countStmt->fetchColumn();
$totalPages = max(1, ceil($total / $perPage));

$stmt = $db->prepare("
    SELECT t.*, st.name as service_name, st.code as service_code
    FROM transactions t
    JOIN service_types st ON st.id = t.service_type_id
    WHERE t.customer_id = ?
    ORDER BY t.created_at DESC
    LIMIT $perPage OFFSET $offset
");
$stmt->execute([$customerId]);
$transactions = $stmt->fetchAll();
?>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-history" style="margin-right:0.4rem;color:var(--info);"></i> Your Transactions (<?= number_format($total) ?>)</h3>
    </div>
    <div class="card-body">
        <?php if (empty($transactions)): ?>
            <div class="empty-state">
                <i class="fas fa-receipt"></i>
                <p>No transactions yet</p>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Service</th>
                        <th>Details</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
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
                            default => 'badge-gray'
                        };
                    ?>
                    <tr>
                        <td class="fw-600" style="font-family:monospace;font-size:0.78rem;"><?= htmlspecialchars($t['transaction_ref']) ?></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.45rem;">
                                <div class="service-icon <?= $iconClass ?>" style="width:28px;height:28px;font-size:0.75rem;"><i class="fas <?= $icon ?>"></i></div>
                                <?= htmlspecialchars($t['service_name']) ?>
                            </div>
                        </td>
                        <td style="font-size:0.85rem;max-width:220px;"><?= htmlspecialchars($t['description'] ?? '—') ?></td>
                        <td class="fw-700"><?= formatNaira($t['total_amount']) ?></td>
                        <td><span class="badge <?= $badge ?>"><?= ucfirst($t['status']) ?></span></td>
                        <td class="text-muted" style="font-size:0.8rem;white-space:nowrap;"><?= date('d M Y, H:i', strtotime($t['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= min($totalPages, 10); $i++): ?>
                <a href="?page=<?= $i ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

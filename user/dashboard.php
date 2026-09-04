<?php
$pageTitle = 'Home';
$currentPage = 'dashboard';
require_once __DIR__ . '/includes/header.php';

$db = getDB();
$customerId = $_SESSION['customer_id'];

// Recent transactions
$stmt = $db->prepare("
    SELECT t.*, st.name as service_name, st.code as service_code
    FROM transactions t
    JOIN service_types st ON st.id = t.service_type_id
    WHERE t.customer_id = ?
    ORDER BY t.created_at DESC
    LIMIT 8
");
$stmt->execute([$customerId]);
$recentTxns = $stmt->fetchAll();

// Stats
$stmt = $db->prepare("SELECT COUNT(*) FROM transactions WHERE customer_id = ? AND status = 'successful'");
$stmt->execute([$customerId]);
$successCount = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COALESCE(SUM(total_amount),0) FROM transactions WHERE customer_id = ? AND status = 'successful'");
$stmt->execute([$customerId]);
$totalSpent = $stmt->fetchColumn();
?>

<div class="wallet-banner">
    <div>
        <div class="label">Available Wallet Balance</div>
        <div class="balance"><?= formatNaira($walletBalance) ?></div>
    </div>
    <div style="font-size:0.85rem;opacity:0.9;">
        <div><?= number_format($successCount) ?> successful payments</div>
        <div>Total spent: <?= formatNaira($totalSpent) ?></div>
    </div>
</div>

<h3 style="font-size:1.05rem;font-weight:700;margin-bottom:1rem;color:var(--dark);">Quick Services</h3>
<div class="service-grid">
    <a href="airtime.php" class="service-card">
        <div class="icon-wrap service-airtime"><i class="fas fa-mobile-alt"></i></div>
        <h3>Airtime</h3>
        <p>MTN, Airtel, Glo, 9mobile</p>
    </a>
    <a href="data.php" class="service-card">
        <div class="icon-wrap service-data"><i class="fas fa-wifi"></i></div>
        <h3>Data Bundles</h3>
        <p>Daily, Weekly & Monthly plans</p>
    </a>
    <a href="electricity.php" class="service-card">
        <div class="icon-wrap service-electricity"><i class="fas fa-bolt"></i></div>
        <h3>Electricity</h3>
        <p>BEDC, EKEDC, IKEDC & more</p>
    </a>
    <a href="cable.php" class="service-card">
        <div class="icon-wrap service-cable"><i class="fas fa-tv"></i></div>
        <h3>Cable TV</h3>
        <p>DStv, GOtv, Startimes</p>
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-history" style="margin-right:0.4rem;color:var(--info);"></i> Recent Transactions</h3>
        <a href="history.php" class="btn btn-sm btn-outline">View All</a>
    </div>
    <div class="card-body">
        <?php if (empty($recentTxns)): ?>
            <div class="empty-state">
                <i class="fas fa-receipt"></i>
                <p>No transactions yet. Try buying airtime or data!</p>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Details</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentTxns as $t):
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
                        <td>
                            <div style="display:flex;align-items:center;gap:0.5rem;">
                                <div class="service-icon <?= $iconClass ?>" style="width:28px;height:28px;font-size:0.75rem;"><i class="fas <?= $icon ?>"></i></div>
                                <?= htmlspecialchars($t['service_name']) ?>
                            </div>
                        </td>
                        <td style="font-size:0.85rem;max-width:200px;">
                            <?= htmlspecialchars(mb_strimwidth($t['description'] ?? '', 0, 40, '…')) ?>
                        </td>
                        <td class="fw-700"><?= formatNaira($t['total_amount']) ?></td>
                        <td><span class="badge <?= $badge ?>"><?= ucfirst($t['status']) ?></span></td>
                        <td class="text-muted" style="font-size:0.8rem;"><?= date('d M, H:i', strtotime($t['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

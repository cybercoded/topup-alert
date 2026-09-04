<?php
$pageTitle = 'Dashboard';
$currentPage = 'dashboard';
require_once 'includes/header.php';

$db = getDB();

// Stats
$totalCustomers = $db->query("SELECT COUNT(*) FROM customers")->fetchColumn();
$activeCustomers = $db->query("SELECT COUNT(*) FROM customers WHERE status = 'active'")->fetchColumn();
$totalApps = $db->query("SELECT COUNT(*) FROM customer_applications")->fetchColumn();
$pendingApps = $db->query("SELECT COUNT(*) FROM customer_applications WHERE status = 'pending'")->fetchColumn();
$totalTxns = $db->query("SELECT COUNT(*) FROM transactions")->fetchColumn();
$successTxns = $db->query("SELECT COUNT(*) FROM transactions WHERE status = 'successful'")->fetchColumn();
$totalVolume = $db->query("SELECT COALESCE(SUM(total_amount),0) FROM transactions WHERE status = 'successful'")->fetchColumn();
$todayVolume = $db->query("SELECT COALESCE(SUM(total_amount),0) FROM transactions WHERE status = 'successful' AND DATE(created_at) = CURDATE()")->fetchColumn();

// Service breakdown
$serviceStats = $db->query("
    SELECT st.name, st.code, COUNT(t.id) as cnt, COALESCE(SUM(t.total_amount),0) as volume
    FROM service_types st
    LEFT JOIN transactions t ON t.service_type_id = st.id AND t.status = 'successful'
    GROUP BY st.id
")->fetchAll();

// Recent transactions
$recentTxns = $db->query("
    SELECT t.*, c.first_name, c.last_name, st.name as service_name, st.code as service_code
    FROM transactions t
    JOIN customers c ON c.id = t.customer_id
    JOIN service_types st ON st.id = t.service_type_id
    ORDER BY t.created_at DESC
    LIMIT 10
")->fetchAll();

// Recent applications
$recentApps = $db->query("
    SELECT * FROM customer_applications
    ORDER BY created_at DESC
    LIMIT 5
")->fetchAll();
?>

<div class="stats-grid">
    <div class="stat-card teal">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-label">Total Customers</div>
        <div class="stat-value"><?= number_format($totalCustomers) ?></div>
        <div class="stat-change"><?= number_format($activeCustomers) ?> active</div>
    </div>
    <div class="stat-card amber">
        <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
        <div class="stat-label">Applications</div>
        <div class="stat-value"><?= number_format($totalApps) ?></div>
        <div class="stat-change"><?= number_format($pendingApps) ?> pending review</div>
    </div>
    <div class="stat-card blue">
        <div class="stat-icon"><i class="fas fa-exchange-alt"></i></div>
        <div class="stat-label">Transactions</div>
        <div class="stat-value"><?= number_format($totalTxns) ?></div>
        <div class="stat-change"><?= number_format($successTxns) ?> successful</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon"><i class="fas fa-naira-sign"></i></div>
        <div class="stat-label">Total Volume</div>
        <div class="stat-value"><?= formatNaira($totalVolume) ?></div>
        <div class="stat-change">All successful transactions</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.75rem;">
    <!-- Service Breakdown -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-chart-bar" style="margin-right:0.4rem;color:var(--primary);"></i> Service Breakdown</h3>
        </div>
        <div class="card-body" style="padding:1.25rem;">
            <?php foreach ($serviceStats as $s): 
                $iconClass = match($s['code']) {
                    'AIRTIME' => 'service-airtime',
                    'DATA' => 'service-data',
                    'ELECTRICITY' => 'service-electricity',
                    'CABLE' => 'service-cable',
                    default => 'service-airtime'
                };
                $icon = match($s['code']) {
                    'AIRTIME' => 'fa-mobile-alt',
                    'DATA' => 'fa-wifi',
                    'ELECTRICITY' => 'fa-bolt',
                    'CABLE' => 'fa-tv',
                    default => 'fa-circle'
                };
            ?>
            <div style="display:flex;align-items:center;justify-content:space-between;padding:0.75rem 0;border-bottom:1px solid var(--gray-200);">
                <div style="display:flex;align-items:center;gap:0.75rem;">
                    <div class="service-icon <?= $iconClass ?>"><i class="fas <?= $icon ?>"></i></div>
                    <div>
                        <div class="fw-600"><?= htmlspecialchars($s['name']) ?></div>
                        <div class="text-muted" style="font-size:0.8rem;"><?= number_format($s['cnt']) ?> transactions</div>
                    </div>
                </div>
                <div class="fw-700" style="color:var(--primary);"><?= formatNaira($s['volume']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Recent Applications -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-user-plus" style="margin-right:0.4rem;color:var(--secondary);"></i> Recent Applications</h3>
            <a href="admin/applications.php" class="btn btn-sm btn-outline">View All</a>
        </div>
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>Applicant</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentApps as $app): ?>
                    <tr>
                        <td>
                            <div class="fw-600"><?= htmlspecialchars($app['first_name'] . ' ' . $app['last_name']) ?></div>
                            <div class="text-muted" style="font-size:0.75rem;"><?= htmlspecialchars($app['phone']) ?></div>
                        </td>
                        <td>
                            <?php
                            $badgeClass = match($app['status']) {
                                'approved' => 'badge-success',
                                'pending' => 'badge-pending',
                                'rejected' => 'badge-danger',
                                'under_review' => 'badge-info',
                                default => 'badge-gray'
                            };
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= ucfirst(str_replace('_', ' ', $app['status'])) ?></span>
                        </td>
                        <td class="text-muted" style="font-size:0.8rem;"><?= date('d M Y', strtotime($app['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-history" style="margin-right:0.4rem;color:var(--info);"></i> Recent Transactions</h3>
        <a href="admin/transactions.php" class="btn btn-sm btn-outline">View All</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentTxns as $txn): 
                        $iconClass = match($txn['service_code']) {
                            'AIRTIME' => 'service-airtime',
                            'DATA' => 'service-data',
                            'ELECTRICITY' => 'service-electricity',
                            'CABLE' => 'service-cable',
                            default => ''
                        };
                        $icon = match($txn['service_code']) {
                            'AIRTIME' => 'fa-mobile-alt',
                            'DATA' => 'fa-wifi',
                            'ELECTRICITY' => 'fa-bolt',
                            'CABLE' => 'fa-tv',
                            default => 'fa-circle'
                        };
                        $badgeClass = match($txn['status']) {
                            'successful' => 'badge-success',
                            'pending' => 'badge-pending',
                            'failed' => 'badge-danger',
                            'reversed' => 'badge-gray',
                            default => 'badge-gray'
                        };
                    ?>
                    <tr>
                        <td class="fw-600" style="font-family:monospace;font-size:0.8rem;"><?= htmlspecialchars($txn['transaction_ref']) ?></td>
                        <td><?= htmlspecialchars($txn['first_name'] . ' ' . $txn['last_name']) ?></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.5rem;">
                                <div class="service-icon <?= $iconClass ?>" style="width:28px;height:28px;font-size:0.75rem;"><i class="fas <?= $icon ?>"></i></div>
                                <?= htmlspecialchars($txn['service_name']) ?>
                            </div>
                        </td>
                        <td class="fw-600"><?= formatNaira($txn['total_amount']) ?></td>
                        <td><span class="badge <?= $badgeClass ?>"><?= ucfirst($txn['status']) ?></span></td>
                        <td class="text-muted" style="font-size:0.8rem;"><?= date('d M Y, H:i', strtotime($txn['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

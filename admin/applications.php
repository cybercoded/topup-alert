<?php
$pageTitle = 'Customer Applications';
$currentPage = 'applications';
require_once __DIR__ . '/../includes/header.php';

$db = getDB();

$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$where = [];
$params = [];

if ($search) {
    $where[] = "(first_name LIKE ? OR last_name LIKE ? OR phone LIKE ? OR email LIKE ? OR application_code LIKE ?)";
    $term = "%$search%";
    $params = array_merge($params, [$term, $term, $term, $term, $term]);
}
if ($status && in_array($status, ['pending', 'approved', 'rejected', 'under_review'])) {
    $where[] = "status = ?";
    $params[] = $status;
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$countStmt = $db->prepare("SELECT COUNT(*) FROM customer_applications $whereSQL");
$countStmt->execute($params);
$total = $countStmt->fetchColumn();
$totalPages = max(1, ceil($total / $perPage));

$stmt = $db->prepare("SELECT * FROM customer_applications $whereSQL ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$applications = $stmt->fetchAll();
?>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-file-alt" style="margin-right:0.4rem;color:var(--secondary);"></i> Customer Applications (<?= number_format($total) ?>)</h3>
        <div class="filters">
            <form method="GET" style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:center;">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="form-control" placeholder="Search name, phone..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="under_review" <?= $status === 'under_review' ? 'selected' : '' ?>>Under Review</option>
                    <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
                <?php if ($search || $status): ?>
                    <a href="applications.php" class="btn btn-secondary btn-sm">Clear</a>
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
                        <th>Applicant</th>
                        <th>Phone / Email</th>
                        <th>ID Type</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($applications)): ?>
                        <tr><td colspan="7"><div class="empty-state"><i class="fas fa-file-alt"></i><p>No applications found</p></div></td></tr>
                    <?php else: ?>
                        <?php foreach ($applications as $a): ?>
                        <tr>
                            <td class="fw-600" style="font-family:monospace;font-size:0.8rem;"><?= htmlspecialchars($a['application_code']) ?></td>
                            <td>
                                <div class="fw-600"><?= htmlspecialchars($a['first_name'] . ' ' . $a['last_name']) ?></div>
                                <div class="text-muted" style="font-size:0.75rem;"><?= htmlspecialchars($a['gender']) ?> · DOB: <?= date('d/m/Y', strtotime($a['date_of_birth'])) ?></div>
                            </td>
                            <td>
                                <div><?= htmlspecialchars($a['phone']) ?></div>
                                <div class="text-muted" style="font-size:0.75rem;"><?= htmlspecialchars($a['email'] ?? '—') ?></div>
                            </td>
                            <td>
                                <div><?= htmlspecialchars($a['id_type']) ?></div>
                                <div class="text-muted" style="font-size:0.75rem;font-family:monospace;"><?= htmlspecialchars($a['id_number']) ?></div>
                            </td>
                            <td>
                                <div><?= htmlspecialchars($a['city']) ?></div>
                                <div class="text-muted" style="font-size:0.75rem;"><?= htmlspecialchars($a['state']) ?></div>
                            </td>
                            <td>
                                <?php
                                $badge = match($a['status']) {
                                    'approved' => 'badge-success',
                                    'pending' => 'badge-pending',
                                    'rejected' => 'badge-danger',
                                    'under_review' => 'badge-info',
                                    default => 'badge-gray'
                                };
                                ?>
                                <span class="badge <?= $badge ?>"><?= ucfirst(str_replace('_', ' ', $a['status'])) ?></span>
                                <?php if ($a['status'] === 'rejected' && $a['rejection_reason']): ?>
                                    <div class="text-muted" style="font-size:0.7rem;margin-top:0.2rem;" title="<?= htmlspecialchars($a['rejection_reason']) ?>">
                                        <?= htmlspecialchars(mb_strimwidth($a['rejection_reason'], 0, 30, '…')) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted" style="font-size:0.8rem;"><?= date('d M Y', strtotime($a['created_at'])) ?></td>
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

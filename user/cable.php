<?php
$pageTitle = 'Cable TV Subscription';
$currentPage = 'cable';
require_once __DIR__ . '/process.php';
require_once __DIR__ . '/includes/header.php';

$packages = [
    'DStv' => [
        ['name' => 'DStv Padi', 'price' => 2500],
        ['name' => 'DStv Yanga', 'price' => 3500],
        ['name' => 'DStv Confam', 'price' => 6200],
        ['name' => 'DStv Compact', 'price' => 10500],
        ['name' => 'DStv Compact Plus', 'price' => 16600],
        ['name' => 'DStv Premium', 'price' => 24500],
    ],
    'GOtv' => [
        ['name' => 'GOtv Smallie', 'price' => 1575],
        ['name' => 'GOtv Jinja', 'price' => 2250],
        ['name' => 'GOtv Jolli', 'price' => 3200],
        ['name' => 'GOtv Max', 'price' => 4850],
        ['name' => 'GOtv Supa', 'price' => 6400],
    ],
    'Startimes' => [
        ['name' => 'Nova', 'price' => 1200],
        ['name' => 'Basic', 'price' => 2100],
        ['name' => 'Smart', 'price' => 2800],
        ['name' => 'Classic', 'price' => 3100],
        ['name' => 'Super', 'price' => 5300],
    ],
];

$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $provider = $_POST['provider'] ?? '';
    $smartcard = preg_replace('/\s+/', '', $_POST['smartcard'] ?? '');
    $packageName = $_POST['package'] ?? '';

    if (!isset($packages[$provider])) {
        $result = ['success' => false, 'message' => 'Please select a provider'];
    } elseif (strlen($smartcard) < 8) {
        $result = ['success' => false, 'message' => 'Enter a valid smartcard / IUC number'];
    } else {
        $pkg = null;
        foreach ($packages[$provider] as $p) {
            if ($p['name'] === $packageName) { $pkg = $p; break; }
        }
        if (!$pkg) {
            $result = ['success' => false, 'message' => 'Please select a package'];
        } else {
            $result = processPayment([
                'service_type_id' => 4,
                'amount' => $pkg['price'],
                'fee' => 0,
                'cable_provider' => $provider,
                'beneficiary_smartcard' => $smartcard,
                'description' => "$provider {$pkg['name']} — ₦" . number_format($pkg['price']) . " (Smartcard: $smartcard)"
            ]);
            if ($result['success']) {
                $stmt = $db->prepare("SELECT wallet_balance FROM customers WHERE id = ?");
                $stmt->execute([$_SESSION['customer_id']]);
                $walletBalance = $stmt->fetchColumn();
            }
        }
    }
}
?>

<?php if ($result): ?>
    <div class="<?= $result['success'] ? 'alert-success' : 'alert-error' ?>">
        <i class="fas <?= $result['success'] ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
        <?= htmlspecialchars($result['message']) ?>
        <?php if ($result['success']): ?>
            <br><small>Ref: <strong><?= htmlspecialchars($result['ref']) ?></strong> · Subscription activated (demo) · New balance: <?= formatNaira($result['new_balance']) ?></small>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="card form-card">
    <div class="card-header">
        <h3><i class="fas fa-tv" style="color:#7c3aed;margin-right:0.4rem;"></i> Cable TV Subscription</h3>
    </div>
    <div class="card-body" style="padding:1.5rem;">
        <form method="POST">
            <div class="form-group">
                <label>Provider</label>
                <div class="network-grid" style="grid-template-columns: repeat(3, 1fr);">
                    <?php foreach (array_keys($packages) as $prov): ?>
                    <label class="network-btn" onclick="showPackages('<?= $prov ?>')">
                        <input type="radio" name="provider" value="<?= $prov ?>" required>
                        <?= $prov ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-group">
                <label>Smartcard / IUC Number</label>
                <input type="text" name="smartcard" class="form-control" placeholder="e.g. 1234567890" value="<?= htmlspecialchars($_POST['smartcard'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>Select Package</label>
                <select name="package" id="package-select" class="form-control" required>
                    <option value="">— Select provider first —</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:0.8rem;margin-top:0.5rem;">
                <i class="fas fa-tv"></i> Subscribe Now
            </button>
        </form>
    </div>
</div>

<script>
const packages = <?= json_encode($packages) ?>;
function showPackages(provider) {
    const sel = document.getElementById('package-select');
    sel.innerHTML = '<option value="">— Select a package —</option>';
    (packages[provider] || []).forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.name;
        opt.textContent = p.name + ' — ₦' + p.price.toLocaleString();
        sel.appendChild(opt);
    });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

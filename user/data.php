<?php
$pageTitle = 'Buy Data';
$currentPage = 'data';
require_once __DIR__ . '/process.php';
require_once __DIR__ . '/includes/header.php';

$dataPlans = [
    'MTN' => [
        ['name' => '500MB - 1 Day', 'price' => 200],
        ['name' => '1GB - 1 Day', 'price' => 350],
        ['name' => '1.5GB - 7 Days', 'price' => 500],
        ['name' => '2GB - 30 Days', 'price' => 1200],
        ['name' => '3GB - 30 Days', 'price' => 1500],
        ['name' => '5GB - 30 Days', 'price' => 2500],
        ['name' => '10GB - 30 Days', 'price' => 3500],
        ['name' => '15GB - 30 Days', 'price' => 5000],
    ],
    'Airtel' => [
        ['name' => '500MB - 1 Day', 'price' => 200],
        ['name' => '1GB - 1 Day', 'price' => 300],
        ['name' => '1.5GB - 7 Days', 'price' => 500],
        ['name' => '2GB - 30 Days', 'price' => 1000],
        ['name' => '3GB - 30 Days', 'price' => 1500],
        ['name' => '6GB - 30 Days', 'price' => 2500],
        ['name' => '10GB - 30 Days', 'price' => 3000],
        ['name' => '18GB - 30 Days', 'price' => 5000],
    ],
    'Glo' => [
        ['name' => '750MB - 1 Day', 'price' => 200],
        ['name' => '1.5GB - 1 Day', 'price' => 300],
        ['name' => '2.5GB - 7 Days', 'price' => 500],
        ['name' => '3.5GB - 30 Days', 'price' => 1000],
        ['name' => '5.2GB - 30 Days', 'price' => 1500],
        ['name' => '7.7GB - 30 Days', 'price' => 2000],
        ['name' => '10GB - 30 Days', 'price' => 2500],
        ['name' => '13.3GB - 30 Days', 'price' => 3000],
    ],
    '9mobile' => [
        ['name' => '500MB - 1 Day', 'price' => 200],
        ['name' => '1GB - 1 Day', 'price' => 350],
        ['name' => '1.5GB - 7 Days', 'price' => 500],
        ['name' => '2GB - 30 Days', 'price' => 1200],
        ['name' => '3GB - 30 Days', 'price' => 1500],
        ['name' => '4.5GB - 30 Days', 'price' => 2000],
        ['name' => '11GB - 30 Days', 'price' => 4000],
        ['name' => '15GB - 30 Days', 'price' => 5000],
    ],
];

$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $network = $_POST['network'] ?? '';
    $phone = preg_replace('/\s+/', '', $_POST['phone'] ?? '');
    $planKey = $_POST['plan'] ?? '';

    if (!isset($dataPlans[$network])) {
        $result = ['success' => false, 'message' => 'Please select a network'];
    } elseif (!preg_match('/^0[7-9][0-9]{9}$/', $phone)) {
        $result = ['success' => false, 'message' => 'Enter a valid Nigerian phone number'];
    } else {
        $plan = null;
        foreach ($dataPlans[$network] as $p) {
            if ($p['name'] === $planKey) { $plan = $p; break; }
        }
        if (!$plan) {
            $result = ['success' => false, 'message' => 'Please select a data plan'];
        } else {
            $result = processPayment([
                'service_type_id' => 2,
                'amount' => $plan['price'],
                'fee' => 0,
                'network' => $network,
                'beneficiary_phone' => $phone,
                'data_plan' => $plan['name'],
                'description' => "$network Data - {$plan['name']} for $phone"
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
            <br><small>Ref: <strong><?= htmlspecialchars($result['ref']) ?></strong> · New balance: <?= formatNaira($result['new_balance']) ?></small>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="card form-card">
    <div class="card-header">
        <h3><i class="fas fa-wifi" style="color:#2563eb;margin-right:0.4rem;"></i> Data Subscription</h3>
    </div>
    <div class="card-body" style="padding:1.5rem;">
        <form method="POST" id="data-form">
            <div class="form-group">
                <label>Select Network</label>
                <div class="network-grid">
                    <?php foreach (array_keys($dataPlans) as $net): ?>
                    <label class="network-btn" onclick="showPlans('<?= $net ?>')">
                        <input type="radio" name="network" value="<?= $net ?>" required>
                        <?= $net ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" class="form-control" placeholder="08031234567" value="<?= htmlspecialchars($_POST['phone'] ?? $_SESSION['customer_phone'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>Select Data Plan</label>
                <select name="plan" id="plan-select" class="form-control" required>
                    <option value="">— Select network first —</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:0.8rem;margin-top:0.5rem;">
                <i class="fas fa-paper-plane"></i> Buy Data
            </button>
        </form>
    </div>
</div>

<script>
const plans = <?= json_encode($dataPlans) ?>;
function showPlans(network) {
    const sel = document.getElementById('plan-select');
    sel.innerHTML = '<option value="">— Select a plan —</option>';
    (plans[network] || []).forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.name;
        opt.textContent = p.name + ' — ₦' + p.price.toLocaleString();
        sel.appendChild(opt);
    });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

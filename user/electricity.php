<?php
$pageTitle = 'Pay Electricity';
$currentPage = 'electricity';
require_once __DIR__ . '/process.php';
require_once __DIR__ . '/includes/header.php';

$discos = [
    'BEDC' => 'Benin Electricity (BEDC)',
    'EKEDC' => 'Eko Electricity (EKEDC)',
    'IKEDC' => 'Ikeja Electric (IKEDC)',
    'AEDC' => 'Abuja Electricity (AEDC)',
    'PHED' => 'Port Harcourt Electric (PHED)',
    'JED' => 'Jos Electricity (JED)',
    'KEDCO' => 'Kano Electricity (KEDCO)',
];

$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $disco = $_POST['disco'] ?? '';
    $meter = preg_replace('/\s+/', '', $_POST['meter'] ?? '');
    $amount = (float)($_POST['amount'] ?? 0);

    if (!isset($discos[$disco])) {
        $result = ['success' => false, 'message' => 'Please select a distribution company'];
    } elseif (strlen($meter) < 8) {
        $result = ['success' => false, 'message' => 'Enter a valid meter number'];
    } elseif ($amount < 500 || $amount > 100000) {
        $result = ['success' => false, 'message' => 'Amount must be between ₦500 and ₦100,000'];
    } else {
        $fee = 50;
        $result = processPayment([
            'service_type_id' => 3,
            'amount' => $amount,
            'fee' => $fee,
            'disco' => $disco,
            'beneficiary_meter' => $meter,
            'description' => "Electricity payment ₦" . number_format($amount) . " - $disco Meter: $meter"
        ]);
        if ($result['success']) {
            $stmt = $db->prepare("SELECT wallet_balance FROM customers WHERE id = ?");
            $stmt->execute([$_SESSION['customer_id']]);
            $walletBalance = $stmt->fetchColumn();
        }
    }
}
?>

<?php if ($result): ?>
    <div class="<?= $result['success'] ? 'alert-success' : 'alert-error' ?>">
        <i class="fas <?= $result['success'] ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
        <?= htmlspecialchars($result['message']) ?>
        <?php if ($result['success']): ?>
            <br><small>Ref: <strong><?= htmlspecialchars($result['ref']) ?></strong> · Token will be sent via SMS (demo) · New balance: <?= formatNaira($result['new_balance']) ?></small>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="card form-card">
    <div class="card-header">
        <h3><i class="fas fa-bolt" style="color:#059669;margin-right:0.4rem;"></i> Electricity Bill Payment</h3>
    </div>
    <div class="card-body" style="padding:1.5rem;">
        <form method="POST">
            <div class="form-group">
                <label>Distribution Company (Disco)</label>
                <select name="disco" class="form-control" required>
                    <option value="">— Select Disco —</option>
                    <?php foreach ($discos as $code => $name): ?>
                        <option value="<?= $code ?>" <?= ($_POST['disco'] ?? '') === $code ? 'selected' : '' ?>><?= htmlspecialchars($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Meter Number</label>
                <input type="text" name="meter" class="form-control" placeholder="e.g. 04123456789" value="<?= htmlspecialchars($_POST['meter'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>Amount (₦)</label>
                <input type="number" name="amount" id="elec-amount" class="form-control" placeholder="e.g. 5000" min="500" max="100000" step="1" value="<?= htmlspecialchars($_POST['amount'] ?? '') ?>" required>
                <div class="amount-chips">
                    <?php foreach ([1000, 2000, 5000, 10000, 15000, 20000] as $amt): ?>
                    <span class="amount-chip" data-target="#elec-amount" data-value="<?= $amt ?>">₦<?= number_format($amt) ?></span>
                    <?php endforeach; ?>
                </div>
                <small class="text-muted" style="display:block;margin-top:0.4rem;">Service fee: ₦50</small>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:0.8rem;margin-top:0.5rem;">
                <i class="fas fa-bolt"></i> Pay Electricity Bill
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

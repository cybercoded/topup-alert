<?php
$pageTitle = 'Buy Airtime';
$currentPage = 'airtime';
require_once __DIR__ . '/process.php';
require_once __DIR__ . '/includes/header.php';

$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $network = $_POST['network'] ?? '';
    $phone = preg_replace('/\s+/', '', $_POST['phone'] ?? '');
    $amount = (float)($_POST['amount'] ?? 0);

    if (!in_array($network, ['MTN', 'Airtel', 'Glo', '9mobile'])) {
        $result = ['success' => false, 'message' => 'Please select a network'];
    } elseif (!preg_match('/^0[7-9][0-9]{9}$/', $phone)) {
        $result = ['success' => false, 'message' => 'Enter a valid Nigerian phone number (e.g. 08031234567)'];
    } elseif ($amount < 50 || $amount > 50000) {
        $result = ['success' => false, 'message' => 'Amount must be between ₦50 and ₦50,000'];
    } else {
        $fee = $amount < 500 ? 10 : 0;
        $result = processPayment([
            'service_type_id' => 1,
            'amount' => $amount,
            'fee' => $fee,
            'network' => $network,
            'beneficiary_phone' => $phone,
            'description' => "Airtime top-up of ₦" . number_format($amount) . " to $phone ($network)"
        ]);
        if ($result['success']) {
            // Refresh wallet display
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
            <br><small>Ref: <strong><?= htmlspecialchars($result['ref']) ?></strong> · New balance: <?= formatNaira($result['new_balance']) ?></small>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="card form-card">
    <div class="card-header">
        <h3><i class="fas fa-mobile-alt" style="color:#d97706;margin-right:0.4rem;"></i> Airtime Top-up</h3>
    </div>
    <div class="card-body" style="padding:1.5rem;">
        <form method="POST">
            <div class="form-group">
                <label>Select Network</label>
                <div class="network-grid">
                    <?php foreach (['MTN', 'Airtel', 'Glo', '9mobile'] as $net): ?>
                    <label class="network-btn">
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
                <label>Amount (₦)</label>
                <input type="number" name="amount" id="airtime-amount" class="form-control" placeholder="e.g. 1000" min="50" max="50000" step="1" value="<?= htmlspecialchars($_POST['amount'] ?? '') ?>" required>
                <div class="amount-chips">
                    <?php foreach ([100, 200, 500, 1000, 2000, 5000] as $amt): ?>
                    <span class="amount-chip" data-target="#airtime-amount" data-value="<?= $amt ?>">₦<?= number_format($amt) ?></span>
                    <?php endforeach; ?>
                </div>
                <small class="text-muted" style="display:block;margin-top:0.4rem;">Fee: ₦10 for amounts below ₦500 · Free for ₦500+</small>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:0.8rem;margin-top:0.5rem;">
                <i class="fas fa-paper-plane"></i> Buy Airtime
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

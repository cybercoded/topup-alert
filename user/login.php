<?php
require_once __DIR__ . '/../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($login && $password) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM customers WHERE (phone = ? OR email = ?) AND status = 'active' LIMIT 1");
        $stmt->execute([$login, $login]);
        $user = $stmt->fetch();

        // Demo: accept user123 or password
        if ($user && (
            ($user['password_hash'] && password_verify($password, $user['password_hash'])) ||
            $password === 'user123' ||
            $password === 'password'
        )) {
            $_SESSION['customer_id'] = $user['id'];
            $_SESSION['customer_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['customer_phone'] = $user['phone'];
            $_SESSION['customer_code'] = $user['customer_code'];
            header('Location: dashboard.php');
            exit;
        }
        $error = 'Invalid phone/email or password';
    } else {
        $error = 'Please enter phone/email and password';
    }
}

if (!empty($_SESSION['customer_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login | Topupalert</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-left">
        <div>
            <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:2rem;">
                <div style="width:52px;height:52px;background:#fff;border-radius:12px;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                    <img src="../assets/img/logo-64.png" alt="Topupalert" style="width:100%;height:100%;object-fit:contain;">
                </div>
                <div>
                    <div style="font-size:1.3rem;font-weight:700;">Topupalert</div>
                    <div style="font-size:0.8rem;opacity:0.7;">Limited · Akure</div>
                </div>
            </div>
            <h1>Pay Bills.<br>Buy Airtime.<br>Stay Connected.</h1>
            <p>Instantly top up airtime & data, pay electricity, and renew DStv, GOtv or Startimes — all in one place.</p>
            
            <div class="auth-features">
                <div class="auth-feature">
                    <i class="fas fa-mobile-alt"></i>
                    <span>Airtime & Data — MTN, Airtel, Glo, 9mobile</span>
                </div>
                <div class="auth-feature">
                    <i class="fas fa-bolt"></i>
                    <span>Electricity — BEDC, EKEDC, IKEDC & more</span>
                </div>
                <div class="auth-feature">
                    <i class="fas fa-tv"></i>
                    <span>Cable TV — DStv, GOtv, Startimes</span>
                </div>
                <div class="auth-feature">
                    <i class="fas fa-wallet"></i>
                    <span>Wallet balance for instant payments</span>
                </div>
            </div>
        </div>
    </div>
    <div class="auth-right">
        <div class="auth-card">
            <h2>Customer Login</h2>
            <p class="subtitle">Access your Topup Alert account</p>
            
            <?php if ($error): ?>
                <div style="background:rgba(239,68,68,0.1);color:#dc2626;padding:0.75rem 1rem;border-radius:8px;font-size:0.875rem;margin-bottom:1.25rem;">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Phone Number or Email</label>
                    <input type="text" name="login" class="form-control" placeholder="08031234567" value="08031234567" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" value="user123" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:0.75rem;margin-top:0.5rem;">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>
            
            <div style="margin-top:1.5rem;padding:1rem;background:var(--gray-100);border-radius:8px;font-size:0.8rem;color:var(--gray-600);">
                <strong>Test Account</strong><br>
                Phone: <code>08031234567</code><br>
                Email: <code>testuser@topupalert.ng</code><br>
                Password: <code>user123</code><br>
                Wallet: <strong>₦50,000.00</strong>
            </div>
            
            <p style="text-align:center;margin-top:1.25rem;font-size:0.8rem;color:var(--gray-500);">
                Admin? <a href="../login.php">Go to Admin Login</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>

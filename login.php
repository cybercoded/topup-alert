<?php
require_once 'config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM admins WHERE username = ? OR email = ? LIMIT 1");
        $stmt->execute([$username, $username]);
        $admin = $stmt->fetch();

        // Demo: accept admin123 or any password for demo purposes
        if ($admin && (password_verify($password, $admin['password_hash']) || $password === 'admin123')) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['full_name'];
            $_SESSION['admin_role'] = $admin['role'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: dashboard.php');
            exit;
        }
        $error = 'Invalid username or password';
    } else {
        $error = 'Please enter username and password';
    }
}

// Redirect if already logged in
if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Topup Alert Limited</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-left">
        <div>
            <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:2rem;">
                <div style="width:48px;height:48px;background:linear-gradient(135deg,#14b8a6,#0f766e);border-radius:12px;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.2rem;">TA</div>
                <div>
                    <div style="font-size:1.3rem;font-weight:700;">Topup Alert</div>
                    <div style="font-size:0.8rem;opacity:0.7;">Limited · Akure</div>
                </div>
            </div>
            <h1>Digital Payments<br>Made Simple</h1>
            <p>Fully automated platform for airtime, data, electricity & cable TV payments across Nigeria.</p>
            
            <div class="auth-features">
                <div class="auth-feature">
                    <i class="fas fa-mobile-alt"></i>
                    <span>Airtime & Data for all networks</span>
                </div>
                <div class="auth-feature">
                    <i class="fas fa-bolt"></i>
                    <span>Electricity bill payments (BEDC & more)</span>
                </div>
                <div class="auth-feature">
                    <i class="fas fa-tv"></i>
                    <span>DStv, GOtv & Startimes subscriptions</span>
                </div>
                <div class="auth-feature">
                    <i class="fas fa-shield-alt"></i>
                    <span>Secure transactions & wallet system</span>
                </div>
            </div>
        </div>
    </div>
    <div class="auth-right">
        <div class="auth-card">
            <h2>Welcome back</h2>
            <p class="subtitle">Sign in to the admin dashboard</p>
            
            <?php if ($error): ?>
                <div style="background:rgba(239,68,68,0.1);color:#dc2626;padding:0.75rem 1rem;border-radius:8px;font-size:0.875rem;margin-bottom:1.25rem;">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Username or Email</label>
                    <input type="text" name="username" class="form-control" placeholder="admin" value="admin" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" value="admin123" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:0.75rem;margin-top:0.5rem;">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>
            
            <p style="text-align:center;margin-top:1.5rem;font-size:0.8rem;color:var(--gray-500);">
                Demo credentials: <strong>admin</strong> / <strong>admin123</strong>
            </p>
        </div>
    </div>
</div>
</body>
</html>

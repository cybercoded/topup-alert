<?php
require_once __DIR__ . '/../config/database.php';

// Simple auth check for admin pages
$isAdminPage = strpos($_SERVER['PHP_SELF'], '/admin/') !== false || 
               basename($_SERVER['PHP_SELF']) === 'dashboard.php';

if ($isAdminPage && empty($_SESSION['admin_id'])) {
    header('Location: ' . (strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? '../login.php' : 'login.php'));
    exit;
}

$pageTitle = $pageTitle ?? 'Dashboard';
$currentPage = $currentPage ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | Topup Alert Limited</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? '../' : '' ?>assets/css/style.css">
</head>
<body>
<?php if ($isAdminPage): ?>
<div class="sidebar">
    <div class="sidebar-brand">
        <div class="logo">TA</div>
        <div>
            <h1>Topup Alert</h1>
            <small>Digital Payments</small>
        </div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-title">Main</div>
            <a href="<?= strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? '../dashboard.php' : 'dashboard.php' ?>" 
               class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-chart-pie"></i> <span>Dashboard</span>
            </a>
        </div>
        <div class="nav-section">
            <div class="nav-section-title">Management</div>
            <a href="<?= strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? 'customers.php' : 'admin/customers.php' ?>" 
               class="nav-link <?= $currentPage === 'customers' ? 'active' : '' ?>">
                <i class="fas fa-users"></i> <span>Customers</span>
            </a>
            <a href="<?= strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? 'applications.php' : 'admin/applications.php' ?>" 
               class="nav-link <?= $currentPage === 'applications' ? 'active' : '' ?>">
                <i class="fas fa-file-alt"></i> <span>Applications</span>
            </a>
            <a href="<?= strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? 'transactions.php' : 'admin/transactions.php' ?>" 
               class="nav-link <?= $currentPage === 'transactions' ? 'active' : '' ?>">
                <i class="fas fa-exchange-alt"></i> <span>Transactions</span>
            </a>
        </div>
        <div class="nav-section">
            <div class="nav-section-title">Services</div>
            <a href="#" class="nav-link">
                <i class="fas fa-mobile-alt"></i> <span>Airtime</span>
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-wifi"></i> <span>Data</span>
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-bolt"></i> <span>Electricity</span>
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-tv"></i> <span>Cable TV</span>
            </a>
        </div>
    </nav>
</div>

<div class="main-content">
    <header class="topbar">
        <div class="topbar-left">
            <h2><?= htmlspecialchars($pageTitle) ?></h2>
            <p>Topup Alert Limited · Akure, Nigeria</p>
        </div>
        <div class="topbar-right">
            <div class="user-info">
                <div class="user-avatar">
                    <?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) ?>
                </div>
                <div>
                    <div style="font-size:0.85rem;font-weight:600;"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></div>
                    <div style="font-size:0.7rem;color:var(--gray-500);"><?= htmlspecialchars($_SESSION['admin_role'] ?? 'admin') ?></div>
                </div>
            </div>
            <a href="<?= strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? '../logout.php' : 'logout.php' ?>" class="btn btn-sm btn-secondary">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </header>
    <div class="content-area">
<?php endif; ?>

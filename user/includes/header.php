<?php
require_once __DIR__ . '/../../config/database.php';

if (empty($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = $pageTitle ?? 'Dashboard';
$currentPage = $currentPage ?? 'dashboard';

// Fetch latest wallet balance
$db = getDB();
$stmt = $db->prepare("SELECT wallet_balance, first_name, last_name FROM customers WHERE id = ?");
$stmt->execute([$_SESSION['customer_id']]);
$customer = $stmt->fetch();
$walletBalance = $customer['wallet_balance'] ?? 0;
$_SESSION['customer_name'] = ($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | Topupalert</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .service-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.25rem;
            margin-bottom: 1.75rem;
        }
        .service-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1.5rem;
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow);
            text-align: center;
            transition: var(--transition);
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .service-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-light);
            color: inherit;
        }
        .service-card .icon-wrap {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin: 0 auto 1rem;
        }
        .service-card h3 {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 0.35rem;
            color: var(--dark);
        }
        .service-card p {
            font-size: 0.8rem;
            color: var(--gray-500);
        }
        .wallet-banner {
            background: linear-gradient(135deg, #0f766e 0%, #0d5c56 50%, #134e4a 100%);
            color: white;
            border-radius: var(--radius);
            padding: 1.5rem 1.75rem;
            margin-bottom: 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .wallet-banner .label { font-size: 0.85rem; opacity: 0.85; }
        .wallet-banner .balance { font-size: 1.9rem; font-weight: 800; letter-spacing: -0.02em; }
        .form-card {
            max-width: 520px;
            margin: 0 auto;
        }
        .amount-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        .amount-chip {
            padding: 0.45rem 0.9rem;
            border: 1.5px solid var(--gray-300);
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            background: white;
        }
        .amount-chip:hover, .amount-chip.active {
            border-color: var(--primary);
            background: rgba(15, 118, 110, 0.08);
            color: var(--primary);
        }
        .network-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.6rem;
            margin-top: 0.5rem;
        }
        .network-btn {
            padding: 0.75rem 0.5rem;
            border: 1.5px solid var(--gray-300);
            border-radius: var(--radius-sm);
            text-align: center;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: var(--transition);
            background: white;
        }
        .network-btn:hover, .network-btn.active {
            border-color: var(--primary);
            background: rgba(15, 118, 110, 0.08);
            color: var(--primary);
        }
        .network-btn input { display: none; }
        .alert-success {
            background: rgba(16, 185, 129, 0.12);
            color: #059669;
            padding: 1rem 1.25rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1.25rem;
            font-size: 0.9rem;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            padding: 1rem 1.25rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1.25rem;
            font-size: 0.9rem;
        }
        @media (max-width: 640px) {
            .network-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="sidebar-brand">
        <div class="logo"><img src="../assets/img/logo-64.png" alt="Topupalert"></div>
        <div>
            <h1>Topupalert</h1>
            <small>Customer Portal</small>
        </div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-title">Menu</div>
            <a href="dashboard.php" class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-home"></i> <span>Home</span>
            </a>
            <a href="airtime.php" class="nav-link <?= $currentPage === 'airtime' ? 'active' : '' ?>">
                <i class="fas fa-mobile-alt"></i> <span>Airtime</span>
            </a>
            <a href="data.php" class="nav-link <?= $currentPage === 'data' ? 'active' : '' ?>">
                <i class="fas fa-wifi"></i> <span>Data</span>
            </a>
            <a href="electricity.php" class="nav-link <?= $currentPage === 'electricity' ? 'active' : '' ?>">
                <i class="fas fa-bolt"></i> <span>Electricity</span>
            </a>
            <a href="cable.php" class="nav-link <?= $currentPage === 'cable' ? 'active' : '' ?>">
                <i class="fas fa-tv"></i> <span>Cable TV</span>
            </a>
            <a href="history.php" class="nav-link <?= $currentPage === 'history' ? 'active' : '' ?>">
                <i class="fas fa-history"></i> <span>History</span>
            </a>
        </div>
    </nav>
</div>

<div class="main-content">
    <header class="topbar">
        <div class="topbar-left">
            <h2><?= htmlspecialchars($pageTitle) ?></h2>
            <p>Topup Alert · Akure, Nigeria</p>
        </div>
        <div class="topbar-right">
            <div style="text-align:right;margin-right:0.5rem;">
                <div style="font-size:0.75rem;color:var(--gray-500);">Wallet</div>
                <div style="font-weight:700;color:var(--primary);font-size:0.95rem;"><?= formatNaira($walletBalance) ?></div>
            </div>
            <div class="user-info">
                <div class="user-avatar">
                    <?= strtoupper(substr($_SESSION['customer_name'] ?? 'U', 0, 1)) ?>
                </div>
                <div>
                    <div style="font-size:0.85rem;font-weight:600;"><?= htmlspecialchars($_SESSION['customer_name'] ?? 'User') ?></div>
                    <div style="font-size:0.7rem;color:var(--gray-500);"><?= htmlspecialchars($_SESSION['customer_phone'] ?? '') ?></div>
                </div>
            </div>
            <a href="logout.php" class="btn btn-sm btn-secondary">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </header>
    <div class="content-area">

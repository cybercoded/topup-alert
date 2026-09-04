<?php
/**
 * Topupalert Limited - Landing / Entry Point
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Topupalert Limited | Digital Payments Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .landing {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #0f766e 50%, #0d5c56 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            color: white;
            text-align: center;
        }
        .landing h1 {
            font-size: 2.8rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            margin-bottom: 0.75rem;
        }
        .landing p {
            font-size: 1.15rem;
            opacity: 0.9;
            max-width: 520px;
            margin-bottom: 2.5rem;
            line-height: 1.7;
        }
        .landing-cards {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        .landing-card {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 16px;
            padding: 2rem 2.25rem;
            width: 280px;
            text-align: left;
            transition: all 0.25s ease;
            text-decoration: none;
            color: white;
        }
        .landing-card:hover {
            background: rgba(255,255,255,0.18);
            transform: translateY(-4px);
            color: white;
        }
        .landing-card i {
            font-size: 1.8rem;
            margin-bottom: 1rem;
            display: block;
        }
        .landing-card h3 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 0.4rem;
        }
        .landing-card p {
            font-size: 0.85rem;
            opacity: 0.8;
            margin: 0;
        }
        .landing-badge {
            display: inline-block;
            background: rgba(245,158,11,0.2);
            color: #fbbf24;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.3rem 0.75rem;
            border-radius: 50px;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
<div class="landing">
    <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1rem;">
        <div style="width:64px;height:64px;background:#fff;border-radius:14px;display:flex;align-items:center;justify-content:center;overflow:hidden;">
            <img src="assets/img/logo-128.png" alt="Topupalert" style="width:100%;height:100%;object-fit:contain;">
        </div>
        <div style="text-align:left;">
            <div style="font-size:1.4rem;font-weight:700;">Topupalert</div>
            <div style="font-size:0.8rem;opacity:0.7;">Limited · Akure, Nigeria</div>
        </div>
    </div>
    <div class="landing-badge">Digital Payments Platform · Demo</div>
    <h1>Pay. Top-up. Subscribe.</h1>
    <p>Airtime, data, electricity bills and cable TV subscriptions — all in one seamless platform.</p>

    <div class="landing-cards">
        <a href="user/login.php" class="landing-card">
            <i class="fas fa-user"></i>
            <h3>Customer Portal</h3>
            <p>Buy airtime & data, pay electricity, renew DStv / GOtv / Startimes</p>
        </a>
        <a href="login.php" class="landing-card">
            <i class="fas fa-user-shield"></i>
            <h3>Admin Dashboard</h3>
            <p>Manage customers, applications, transactions and view reports</p>
        </a>
    </div>

    <div style="margin-top:3rem;font-size:0.8rem;opacity:0.6;">
        Test Customer: 08031234567 / user123 &nbsp;·&nbsp; Admin: admin / admin123
    </div>
</div>
</body>
</html>

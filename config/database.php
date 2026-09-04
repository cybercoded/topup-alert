<?php
/**
 * Topup Alert Limited - Database Configuration
 * Digital Payments Platform | Akure, Nigeria
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'topup_alert');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// App settings
define('APP_NAME', 'Topup Alert');
define('APP_FULL_NAME', 'Topup Alert Limited');
define('APP_LOCATION', 'Akure, Nigeria');
define('APP_CURRENCY', '₦');
define('APP_CURRENCY_CODE', 'NGN');
define('BASE_URL', '/topup-alert'); // Adjust based on your setup

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    return $pdo;
}

function formatNaira($amount) {
    return '₦' . number_format((float)$amount, 2);
}

function generateRef($prefix = 'TXN') {
    return $prefix . date('Y') . strtoupper(substr(uniqid(), -8));
}

session_start();
?>

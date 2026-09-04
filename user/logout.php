<?php
session_start();
// Only clear customer session keys
unset($_SESSION['customer_id'], $_SESSION['customer_name'], $_SESSION['customer_phone'], $_SESSION['customer_code']);
header('Location: login.php');
exit;
?>

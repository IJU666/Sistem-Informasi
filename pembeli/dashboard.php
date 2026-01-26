<?php
require_once '../config/database.php';
require_once '../config/session.php';

if (!isLoggedIn()) {
    header('Location: ../auth/login.php');
    exit;
}

// Redirect to riwayat pesanan
header('Location: riwayat_pesanan.php');
exit;
?>
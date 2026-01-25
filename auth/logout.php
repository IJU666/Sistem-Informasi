<?php
require_once '../config/database.php';
require_once '../config/session.php';

if (isLoggedIn()) {
    // Update logout time di session log
    try {
        $stmt = $pdo->prepare("
            UPDATE user_session 
            SET logout_time = NOW() 
            WHERE id_pengguna = ? AND logout_time IS NULL 
            ORDER BY login_time DESC 
            LIMIT 1
        ");
        $stmt->execute([getUserId()]);
    } catch (PDOException $e) {
        // Silent error
    }
}

// Destroy session
session_destroy();

// Redirect ke halaman login
header('Location: login.php');
exit;
?>
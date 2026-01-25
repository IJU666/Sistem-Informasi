<?php
require_once '../config/database.php';
require_once '../config/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$email = trim($_POST['email']);
$password = trim($_POST['password']);

try {
    // Cek user berdasarkan email
    $stmt = $pdo->prepare("SELECT * FROM pengguna WHERE email = ? AND status_akun = 'aktif'");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Verifikasi password (untuk demo menggunakan plain text, production harus gunakan password_verify)
        // Production: if (password_verify($password, $user['password']))
        if ($password === $user['password'] || password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $user['id_pengguna'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            // Insert session log
            $token = bin2hex(random_bytes(32));
            $stmt = $pdo->prepare("INSERT INTO user_session (id_pengguna, token_session, login_time) VALUES (?, ?, NOW())");
            $stmt->execute([$user['id_pengguna'], $token]);
            
            // Redirect berdasarkan role
            if ($user['role'] === 'admin') {
                header('Location: ../admin/dashboard.php');
            } elseif ($user['role'] === 'penjual') {
                header('Location: ../penjual/dashboard.php');
            } else {
                header('Location: ../index.php');
            }
            exit;
        } else {
            $_SESSION['error'] = 'Password salah!';
            header('Location: login.php');
            exit;
        }
    } else {
        $_SESSION['error'] = 'Email tidak ditemukan atau akun tidak aktif!';
        header('Location: login.php');
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['error'] = 'Terjadi kesalahan sistem: ' . $e->getMessage();
    header('Location: login.php');
    exit;
}
?>
<?php
require_once '../config/database.php';
require_once '../config/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

$nama = trim($_POST['nama']);
$email = trim($_POST['email']);
$no_hp = trim($_POST['no_hp'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$password = trim($_POST['password']);
$confirm_password = trim($_POST['confirm_password']);
$role = trim($_POST['role']);

// Validasi
if ($password !== $confirm_password) {
    $_SESSION['error'] = 'Password dan konfirmasi password tidak sama!';
    header('Location: register.php');
    exit;
}

if (strlen($password) < 6) {
    $_SESSION['error'] = 'Password minimal 6 karakter!';
    header('Location: register.php');
    exit;
}

if (!in_array($role, ['pembeli', 'penjual'])) {
    $_SESSION['error'] = 'Role tidak valid!';
    header('Location: register.php');
    exit;
}

try {
    // Cek apakah email sudah terdaftar
    $stmt = $pdo->prepare("SELECT id_pengguna FROM pengguna WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = 'Email sudah terdaftar!';
        header('Location: register.php');
        exit;
    }
    
    // Hash password (production)
    // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    // Untuk demo, simpan plain text (JANGAN DIGUNAKAN DI PRODUCTION!)
    $hashed_password = $password;
    
    // Insert user
    $stmt = $pdo->prepare("
        INSERT INTO pengguna (nama, email, password, role, no_hp, alamat, status_akun) 
        VALUES (?, ?, ?, ?, ?, ?, 'aktif')
    ");
    $stmt->execute([$nama, $email, $hashed_password, $role, $no_hp, $alamat]);
    $user_id = $pdo->lastInsertId();
    
    // Jika penjual, insert ke tabel penjual
    if ($role === 'penjual') {
        $nama_toko = trim($_POST['nama_toko'] ?? '');
        $deskripsi_toko = trim($_POST['deskripsi_toko'] ?? '');
        
        if (empty($nama_toko)) {
            $_SESSION['error'] = 'Nama toko harus diisi untuk penjual!';
            // Rollback - hapus user yang sudah dibuat
            $stmt = $pdo->prepare("DELETE FROM pengguna WHERE id_pengguna = ?");
            $stmt->execute([$user_id]);
            header('Location: register.php');
            exit;
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO penjual (id_pengguna, nama_toko, deskripsi_toko, status_verifikasi) 
            VALUES (?, ?, ?, 'diterima')
        ");
        $stmt->execute([$user_id, $nama_toko, $deskripsi_toko]);
    }
    
    // Create keranjang untuk user
    $stmt = $pdo->prepare("INSERT INTO keranjang (id_pengguna, tanggal_update) VALUES (?, NOW())");
    $stmt->execute([$user_id]);
    
    $_SESSION['success'] = 'Registrasi berhasil! Silakan login.';
    header('Location: login.php');
    exit;
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
    header('Location: register.php');
    exit;
}
?>
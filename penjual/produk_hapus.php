<?php
require_once '../config/database.php';
require_once '../config/session.php';

if (!isLoggedIn() || !isPenjual()) {
    header('Location: ../auth/login.php');
    exit;
}

// Get penjual data
$stmt = $pdo->prepare("SELECT * FROM penjual WHERE id_pengguna = ?");
$stmt->execute([getUserId()]);
$penjual = $stmt->fetch();

// Get product ID
$id_produk = $_GET['id'] ?? 0;

try {
    // Cek apakah produk milik penjual ini
    $stmt = $pdo->prepare("SELECT * FROM produk WHERE id_produk = ? AND id_penjual = ?");
    $stmt->execute([$id_produk, $penjual['id_penjual']]);
    $produk = $stmt->fetch();

    if (!$produk) {
        $_SESSION['error'] = 'Produk tidak ditemukan!';
        header('Location: produk_list.php');
        exit;
    }

    // Cek apakah produk ada di pesanan yang belum selesai
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total
        FROM detail_pesanan dp
        JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan
        WHERE dp.id_produk = ? AND ps.status_pesanan IN ('menunggu', 'diproses')
    ");
    $stmt->execute([$id_produk]);
    $pesanan_aktif = $stmt->fetchColumn();

    if ($pesanan_aktif > 0) {
        $_SESSION['error'] = 'Produk tidak dapat dihapus karena masih ada pesanan aktif!';
        header('Location: produk_list.php');
        exit;
    }

    // Delete produk
    $stmt = $pdo->prepare("DELETE FROM produk WHERE id_produk = ? AND id_penjual = ?");
    $stmt->execute([$id_produk, $penjual['id_penjual']]);

    $_SESSION['success'] = 'Produk berhasil dihapus!';
    header('Location: produk_list.php');
    exit;

} catch (PDOException $e) {
    $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
    header('Location: produk_list.php');
    exit;
}
?>
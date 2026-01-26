<?php
require_once '../config/database.php';
require_once '../config/session.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Anda harus login terlebih dahulu']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$id_pesanan = (int)$_POST['id_pesanan'];

try {
    // Check ownership
    $stmt = $pdo->prepare("SELECT * FROM pesanan WHERE id_pesanan = ? AND id_pengguna = ?");
    $stmt->execute([$id_pesanan, getUserId()]);
    $order = $stmt->fetch();
    
    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan']);
        exit;
    }
    
    // Check status
    if ($order['status_pesanan'] !== 'menunggu') {
        echo json_encode(['success' => false, 'message' => 'Hanya pesanan dengan status menunggu yang bisa dibatalkan']);
        exit;
    }
    
    // Update status
    $stmt = $pdo->prepare("UPDATE pesanan SET status_pesanan = 'dibatalkan' WHERE id_pesanan = ?");
    $stmt->execute([$id_pesanan]);
    
    // Restore stock
    $stmt = $pdo->prepare("
        SELECT dp.id_produk, dp.jumlah 
        FROM detail_pesanan dp 
        WHERE dp.id_pesanan = ?
    ");
    $stmt->execute([$id_pesanan]);
    $details = $stmt->fetchAll();
    
    foreach ($details as $detail) {
        $stmt = $pdo->prepare("UPDATE produk SET stok = stok + ? WHERE id_produk = ?");
        $stmt->execute([$detail['jumlah'], $detail['id_produk']]);
    }
    
    echo json_encode(['success' => true, 'message' => 'Pesanan berhasil dibatalkan']);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>
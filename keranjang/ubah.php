<?php
require_once '../config/database.php';
require_once '../config/session.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Anda harus login terlebih dahulu']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$id_isi = (int)$_POST['id_isi'];
$jumlah = (int)$_POST['jumlah'];

if ($id_isi <= 0 || $jumlah <= 0) {
    echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
    exit;
}

try {
    // Cek apakah item ada di keranjang user
    $stmt = $pdo->prepare("
        SELECT ik.*, p.stok, k.id_pengguna
        FROM isi_keranjang ik
        JOIN keranjang k ON ik.id_keranjang = k.id_keranjang
        JOIN produk p ON ik.id_produk = p.id_produk
        WHERE ik.id_isi = ?
    ");
    $stmt->execute([$id_isi]);
    $item = $stmt->fetch();
    
    if (!$item) {
        echo json_encode(['success' => false, 'message' => 'Item tidak ditemukan']);
        exit;
    }
    
    // Cek kepemilikan
    if ($item['id_pengguna'] != getUserId()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    
    // Cek stok
    if ($jumlah > $item['stok']) {
        echo json_encode(['success' => false, 'message' => 'Stok tidak mencukupi. Maksimal: ' . $item['stok']]);
        exit;
    }
    
    // Update jumlah
    $stmt = $pdo->prepare("UPDATE isi_keranjang SET jumlah = ? WHERE id_isi = ?");
    $stmt->execute([$jumlah, $id_isi]);
    
    // Update tanggal_update keranjang
    $stmt = $pdo->prepare("UPDATE keranjang SET tanggal_update = NOW() WHERE id_pengguna = ?");
    $stmt->execute([getUserId()]);
    
    echo json_encode(['success' => true, 'message' => 'Jumlah berhasil diubah']);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>
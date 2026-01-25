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

$id_produk = (int)$_POST['id_produk'];
$jumlah = (int)($_POST['jumlah'] ?? 1);

if ($id_produk <= 0 || $jumlah <= 0) {
    echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
    exit;
}

try {
    // Cek produk
    $stmt = $pdo->prepare("SELECT * FROM produk WHERE id_produk = ? AND status_produk = 'aktif'");
    $stmt->execute([$id_produk]);
    $produk = $stmt->fetch();
    
    if (!$produk) {
        echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan']);
        exit;
    }
    
    // Cek stok
    if ($jumlah > $produk['stok']) {
        echo json_encode(['success' => false, 'message' => 'Stok tidak mencukupi']);
        exit;
    }
    
    // Get keranjang user
    $stmt = $pdo->prepare("SELECT id_keranjang FROM keranjang WHERE id_pengguna = ?");
    $stmt->execute([getUserId()]);
    $keranjang = $stmt->fetch();
    
    if (!$keranjang) {
        // Create keranjang if not exists
        $stmt = $pdo->prepare("INSERT INTO keranjang (id_pengguna, tanggal_update) VALUES (?, NOW())");
        $stmt->execute([getUserId()]);
        $id_keranjang = $pdo->lastInsertId();
    } else {
        $id_keranjang = $keranjang['id_keranjang'];
    }
    
    // Cek apakah produk sudah ada di keranjang
    $stmt = $pdo->prepare("SELECT * FROM isi_keranjang WHERE id_keranjang = ? AND id_produk = ?");
    $stmt->execute([$id_keranjang, $id_produk]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Update jumlah
        $new_jumlah = $existing['jumlah'] + $jumlah;
        
        if ($new_jumlah > $produk['stok']) {
            echo json_encode(['success' => false, 'message' => 'Stok tidak mencukupi']);
            exit;
        }
        
        $stmt = $pdo->prepare("UPDATE isi_keranjang SET jumlah = ? WHERE id_isi = ?");
        $stmt->execute([$new_jumlah, $existing['id_isi']]);
    } else {
        // Insert baru
        $stmt = $pdo->prepare("INSERT INTO isi_keranjang (id_keranjang, id_produk, jumlah) VALUES (?, ?, ?)");
        $stmt->execute([$id_keranjang, $id_produk, $jumlah]);
    }
    
    // Update tanggal_update keranjang
    $stmt = $pdo->prepare("UPDATE keranjang SET tanggal_update = NOW() WHERE id_keranjang = ?");
    $stmt->execute([$id_keranjang]);
    
    echo json_encode(['success' => true, 'message' => 'Produk berhasil ditambahkan ke keranjang']);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>
<?php
require_once '../config/database.php';
require_once '../config/session.php';

if (!isLoggedIn()) {
    if (isset($_GET['clear'])) {
        header('Location: ../auth/login.php');
        exit;
    }
    echo json_encode(['success' => false, 'message' => 'Anda harus login terlebih dahulu']);
    exit;
}

// Handle clear all cart
if (isset($_GET['clear']) && $_GET['clear'] === 'all') {
    try {
        // Get keranjang user
        $stmt = $pdo->prepare("SELECT id_keranjang FROM keranjang WHERE id_pengguna = ?");
        $stmt->execute([getUserId()]);
        $keranjang = $stmt->fetch();
        
        if ($keranjang) {
            // Delete all items
            $stmt = $pdo->prepare("DELETE FROM isi_keranjang WHERE id_keranjang = ?");
            $stmt->execute([$keranjang['id_keranjang']]);
            
            $_SESSION['success'] = 'Keranjang berhasil dikosongkan';
        }
        
        header('Location: index.php');
        exit;
        
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
        header('Location: index.php');
        exit;
    }
}

// Handle delete single item
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$id_isi = (int)$_POST['id_isi'];

if ($id_isi <= 0) {
    echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
    exit;
}

try {
    // Cek apakah item ada di keranjang user
    $stmt = $pdo->prepare("
        SELECT ik.id_isi, k.id_pengguna
        FROM isi_keranjang ik
        JOIN keranjang k ON ik.id_keranjang = k.id_keranjang
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
    
    // Delete item
    $stmt = $pdo->prepare("DELETE FROM isi_keranjang WHERE id_isi = ?");
    $stmt->execute([$id_isi]);
    
    // Update tanggal_update keranjang
    $stmt = $pdo->prepare("UPDATE keranjang SET tanggal_update = NOW() WHERE id_pengguna = ?");
    $stmt->execute([getUserId()]);
    
    echo json_encode(['success' => true, 'message' => 'Produk berhasil dihapus dari keranjang']);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>
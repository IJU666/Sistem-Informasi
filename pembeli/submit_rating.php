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
$id_produk_array = $_POST['id_produk'] ?? [];
$rating_array = $_POST['rating'] ?? [];
$komentar_array = $_POST['komentar'] ?? [];

try {
    // Validate order ownership and status
    $stmt = $pdo->prepare("
        SELECT * FROM pesanan 
        WHERE id_pesanan = ? AND id_pengguna = ? AND status_pesanan = 'selesai'
    ");
    $stmt->execute([$id_pesanan, getUserId()]);
    $order = $stmt->fetch();
    
    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Pesanan tidak valid atau belum selesai']);
        exit;
    }
    
    // Validate arrays have same length
    if (count($id_produk_array) !== count($rating_array)) {
        echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
        exit;
    }
    
    $pdo->beginTransaction();
    
    $success_count = 0;
    
    foreach ($id_produk_array as $index => $id_produk) {
        $id_produk = (int)$id_produk;
        $rating = (int)$rating_array[$index];
        $komentar = trim($komentar_array[$index] ?? '');
        
        // Validate rating
        if ($rating < 1 || $rating > 5) {
            continue;
        }
        
        // Check if product is in this order
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM detail_pesanan 
            WHERE id_pesanan = ? AND id_produk = ?
        ");
        $stmt->execute([$id_pesanan, $id_produk]);
        
        if ($stmt->fetchColumn() > 0) {
            // Check if already rated
            $stmt = $pdo->prepare("
                SELECT id_rating FROM rating 
                WHERE id_pengguna = ? AND id_produk = ?
            ");
            $stmt->execute([getUserId(), $id_produk]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                // Update existing rating
                $stmt = $pdo->prepare("
                    UPDATE rating 
                    SET rating = ?, komentar = ?, tanggal = NOW()
                    WHERE id_rating = ?
                ");
                $stmt->execute([$rating, $komentar, $existing['id_rating']]);
            } else {
                // Insert new rating
                $stmt = $pdo->prepare("
                    INSERT INTO rating (id_pengguna, id_produk, rating, komentar, tanggal)
                    VALUES (?, ?, ?, ?, NOW())
                ");
                $stmt->execute([getUserId(), $id_produk, $rating, $komentar]);
            }
            
            $success_count++;
        }
    }
    
    $pdo->commit();
    
    if ($success_count > 0) {
        echo json_encode([
            'success' => true, 
            'message' => "Berhasil memberi rating untuk $success_count produk"
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Tidak ada rating yang disimpan'
        ]);
    }
    
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>
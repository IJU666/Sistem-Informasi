    <?php
require_once '../config/database.php';
require_once '../config/session.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !isPenjual()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$id_pesanan = (int)$_POST['id_pesanan'];
$new_status = $_POST['status'];

// Validate status
$valid_statuses = ['diproses', 'selesai', 'dibatalkan'];
if (!in_array($new_status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Status tidak valid']);
    exit;
}

try {
    // Get penjual data
    $stmt = $pdo->prepare("SELECT id_penjual FROM penjual WHERE id_pengguna = ?");
    $stmt->execute([getUserId()]);
    $penjual = $stmt->fetch();
    
    // Check if order has products from this seller
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM detail_pesanan dp
        JOIN produk pr ON dp.id_produk = pr.id_produk
        WHERE dp.id_pesanan = ? AND pr.id_penjual = ?
    ");
    $stmt->execute([$id_pesanan, $penjual['id_penjual']]);
    $has_products = $stmt->fetchColumn();
    
    if (!$has_products) {
        echo json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan']);
        exit;
    }
    
    // Get current order status
    $stmt = $pdo->prepare("SELECT status_pesanan FROM pesanan WHERE id_pesanan = ?");
    $stmt->execute([$id_pesanan]);
    $order = $stmt->fetch();
    
    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan']);
        exit;
    }
    
    // Validate status transition
    if ($order['status_pesanan'] == 'menunggu' && $new_status == 'dibatalkan') {
        // Restore stock if rejected
        $stmt = $pdo->prepare("
            SELECT dp.id_produk, dp.jumlah 
            FROM detail_pesanan dp 
            JOIN produk pr ON dp.id_produk = pr.id_produk
            WHERE dp.id_pesanan = ? AND pr.id_penjual = ?
        ");
        $stmt->execute([$id_pesanan, $penjual['id_penjual']]);
        $details = $stmt->fetchAll();
        
        foreach ($details as $detail) {
            $stmt = $pdo->prepare("UPDATE produk SET stok = stok + ? WHERE id_produk = ?");
            $stmt->execute([$detail['jumlah'], $detail['id_produk']]);
        }
    }
    
    // Update order status
    $stmt = $pdo->prepare("UPDATE pesanan SET status_pesanan = ? WHERE id_pesanan = ?");
    $stmt->execute([$new_status, $id_pesanan]);
    
    $messages = [
        'diproses' => 'Pesanan berhasil diterima dan sedang diproses',
        'selesai' => 'Pesanan berhasil diselesaikan',
        'dibatalkan' => 'Pesanan berhasil ditolak'
    ];
    
    echo json_encode([
        'success' => true, 
        'message' => $messages[$new_status] ?? 'Status berhasil diupdate'
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>
<?php
require_once '../config/database.php';
require_once '../config/session.php';

if (!isLoggedIn()) {
    header('Location: ../auth/login.php');
    exit;
}

// Get filter
$status_filter = $_GET['status'] ?? '';

// Build query
$query = "
    SELECT 
        p.id_pesanan,
        p.tanggal_pesanan,
        p.total,
        p.status_pesanan,
        COUNT(DISTINCT dp.id_detail) as total_items,
        GROUP_CONCAT(DISTINCT pr.nama_produk SEPARATOR ', ') as produk_names
    FROM pesanan p
    LEFT JOIN detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
    LEFT JOIN produk pr ON dp.id_produk = pr.id_produk
    WHERE p.id_pengguna = ?
";

$params = [getUserId()];

if ($status_filter) {
    $query .= " AND p.status_pesanan = ?";
    $params[] = $status_filter;
}

$query .= " GROUP BY p.id_pesanan ORDER BY p.tanggal_pesanan DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll();

// Statistics
$stmt = $pdo->prepare("SELECT COUNT(*) FROM pesanan WHERE id_pengguna = ?");
$stmt->execute([getUserId()]);
$total_orders = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM pesanan WHERE id_pengguna = ? AND status_pesanan = 'menunggu'");
$stmt->execute([getUserId()]);
$pending_orders = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM pesanan WHERE id_pengguna = ? AND status_pesanan = 'selesai'");
$stmt->execute([getUserId()]);
$completed_orders = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COALESCE(SUM(total), 0) FROM pesanan WHERE id_pengguna = ? AND status_pesanan = 'selesai'");
$stmt->execute([getUserId()]);
$total_spent = $stmt->fetchColumn();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h2 class="fw-bold mb-4">Riwayat Pesanan</h2>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Total Pesanan</small>
                            <h3 class="mb-0"><?= number_format($total_orders) ?></h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 text-primary rounded p-3">
                            <i class="bi bi-box-seam fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Menunggu</small>
                            <h3 class="mb-0"><?= number_format($pending_orders) ?></h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 text-warning rounded p-3">
                            <i class="bi bi-clock-history fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Selesai</small>
                            <h3 class="mb-0"><?= number_format($completed_orders) ?></h3>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success rounded p-3">
                            <i class="bi bi-check-circle fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Total Belanja</small>
                            <h3 class="mb-0">Rp<?= number_format($total_spent/1000) ?>K</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 text-info rounded p-3">
                            <i class="bi bi-wallet2 fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="d-flex gap-2">
                <a href="riwayat_pesanan.php" class="btn btn-<?= !$status_filter ? 'primary' : 'outline-primary' ?>">
                    Semua
                </a>
                <a href="?status=menunggu" class="btn btn-<?= $status_filter == 'menunggu' ? 'warning' : 'outline-warning' ?>">
                    Menunggu
                </a>
                <a href="?status=diproses" class="btn btn-<?= $status_filter == 'diproses' ? 'info' : 'outline-info' ?>">
                    Diproses
                </a>
                <a href="?status=selesai" class="btn btn-<?= $status_filter == 'selesai' ? 'success' : 'outline-success' ?>">
                    Selesai
                </a>
                <a href="?status=dibatalkan" class="btn btn-<?= $status_filter == 'dibatalkan' ? 'danger' : 'outline-danger' ?>">
                    Dibatalkan
                </a>
            </div>
        </div>
    </div>

    <!-- Orders List -->
    <?php if (empty($orders)): ?>
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox" style="font-size: 80px; color: #ddd;"></i>
                <h4 class="mt-3 mb-2">Belum Ada Pesanan</h4>
                <p class="text-muted mb-4">Anda belum memiliki riwayat pesanan</p>
                <a href="../produk/katalog.php" class="btn btn-primary">
                    <i class="bi bi-shop"></i> Mulai Belanja
                </a>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): 
            // Get order details
            $stmt = $pdo->prepare("
                SELECT dp.*, pr.nama_produk, pr.harga, pj.nama_toko
                FROM detail_pesanan dp
                JOIN produk pr ON dp.id_produk = pr.id_produk
                JOIN penjual pj ON pr.id_penjual = pj.id_penjual
                WHERE dp.id_pesanan = ?
            ");
            $stmt->execute([$order['id_pesanan']]);
            $details = $stmt->fetchAll();
            
            $status_colors = [
                'menunggu' => 'warning',
                'diproses' => 'info',
                'selesai' => 'success',
                'dibatalkan' => 'danger'
            ];
            $color = $status_colors[$order['status_pesanan']] ?? 'secondary';
        ?>
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h6 class="mb-1">
                            <i class="bi bi-receipt"></i> Pesanan #<?= $order['id_pesanan'] ?>
                        </h6>
                        <small class="text-muted">
                            <i class="bi bi-calendar"></i> <?= date('d F Y, H:i', strtotime($order['tanggal_pesanan'])) ?>
                        </small>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <span class="badge bg-<?= $color ?> px-3 py-2">
                            <?= ucfirst($order['status_pesanan']) ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Order Items -->
                <?php foreach ($details as $item): ?>
                <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="bi bi-image fs-3 text-muted"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1"><?= htmlspecialchars($item['nama_produk']) ?></h6>
                        <small class="text-muted">
                            <i class="bi bi-shop"></i> <?= htmlspecialchars($item['nama_toko']) ?>
                        </small>
                        <p class="mb-0 mt-1">
                            <span class="text-muted">Jumlah: <?= $item['jumlah'] ?>x</span>
                            <span class="ms-3">Rp<?= number_format($item['harga_satuan'], 0, ',', '.') ?></span>
                        </p>
                    </div>
                    <div class="text-end">
                        <h6 class="mb-0 text-primary">Rp<?= number_format($item['jumlah'] * $item['harga_satuan'], 0, ',', '.') ?></h6>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Order Summary -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <p class="mb-1">
                            <i class="bi bi-box"></i> <strong><?= $order['total_items'] ?></strong> Item
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h5 class="mb-3">
                            Total: <span class="text-primary">Rp<?= number_format($order['total'], 0, ',', '.') ?></span>
                        </h5>
                        
                        <?php if ($order['status_pesanan'] == 'menunggu'): ?>
                            <button class="btn btn-outline-danger btn-sm" onclick="cancelOrder(<?= $order['id_pesanan'] ?>)">
                                <i class="bi bi-x-circle"></i> Batalkan Pesanan
                            </button>
                        <?php endif; ?>
                        
                        <?php if ($order['status_pesanan'] == 'selesai'): ?>
                            <button class="btn btn-warning btn-sm me-2" data-bs-toggle="modal" data-bs-target="#ratingModal<?= $order['id_pesanan'] ?>">
                                <i class="bi bi-star"></i> Beri Rating
                            </button>
                            <a href="../produk/katalog.php" class="btn btn-primary btn-sm">
                                <i class="bi bi-cart-plus"></i> Beli Lagi
                            </a>
                        <?php endif; ?>
                        
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#detail-<?= $order['id_pesanan'] ?>">
                            <i class="bi bi-eye"></i> Lihat Detail
                        </button>
                    </div>
                </div>

                <!-- Collapsible Detail -->
                <div class="collapse mt-3" id="detail-<?= $order['id_pesanan'] ?>">
                    <div class="alert alert-light mb-0">
                        <h6 class="mb-2">Informasi Pesanan</h6>
                        <div class="row small">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>ID Pesanan:</strong> #<?= $order['id_pesanan'] ?></p>
                                <p class="mb-1"><strong>Tanggal:</strong> <?= date('d F Y, H:i', strtotime($order['tanggal_pesanan'])) ?></p>
                                <p class="mb-1"><strong>Status:</strong> <span class="badge bg-<?= $color ?>"><?= ucfirst($order['status_pesanan']) ?></span></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Subtotal:</strong> Rp<?= number_format($order['total'] - 10000, 0, ',', '.') ?></p>
                                <p class="mb-1"><strong>Ongkir:</strong> Rp10.000</p>
                                <p class="mb-1"><strong>Total:</strong> Rp<?= number_format($order['total'], 0, ',', '.') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        
        <!-- Rating Modals -->
        <?php foreach ($orders as $order): 
            if ($order['status_pesanan'] !== 'selesai') continue;
            
            // Get order details again for modal
            $stmt = $pdo->prepare("
                SELECT dp.*, pr.nama_produk, pr.id_produk, pj.nama_toko
                FROM detail_pesanan dp
                JOIN produk pr ON dp.id_produk = pr.id_produk
                JOIN penjual pj ON pr.id_penjual = pj.id_penjual
                WHERE dp.id_pesanan = ?
            ");
            $stmt->execute([$order['id_pesanan']]);
            $modal_details = $stmt->fetchAll();
        ?>
        <!-- Modal Rating untuk Pesanan #<?= $order['id_pesanan'] ?> -->
        <div class="modal fade" id="ratingModal<?= $order['id_pesanan'] ?>" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-star-fill text-warning"></i> Beri Rating - Pesanan #<?= $order['id_pesanan'] ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="ratingForm<?= $order['id_pesanan'] ?>">
                            <input type="hidden" name="id_pesanan" value="<?= $order['id_pesanan'] ?>">
                            
                            <?php foreach ($modal_details as $index => $item): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                            <i class="bi bi-image fs-4 text-muted"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?= htmlspecialchars($item['nama_produk']) ?></h6>
                                            <small class="text-muted"><?= htmlspecialchars($item['nama_toko']) ?></small>
                                        </div>
                                    </div>
                                    
                                    <input type="hidden" name="id_produk[]" value="<?= $item['id_produk'] ?>">
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Rating</label>
                                        <div class="rating-stars" data-product="<?= $item['id_produk'] ?>">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="bi bi-star fs-3 text-muted rating-star" 
                                               data-rating="<?= $i ?>" 
                                               style="cursor: pointer;"
                                               onclick="setRating(<?= $item['id_produk'] ?>, <?= $i ?>)"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <input type="hidden" name="rating[]" id="rating_<?= $item['id_produk'] ?>" required>
                                    </div>
                                    
                                    <div class="mb-0">
                                        <label class="form-label fw-bold">Komentar (Opsional)</label>
                                        <textarea name="komentar[]" class="form-control" rows="3" placeholder="Bagikan pengalaman Anda tentang produk ini..."></textarea>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> 
                                Rating dan komentar Anda akan membantu pembeli lain dalam memilih produk
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" onclick="submitRating(<?= $order['id_pesanan'] ?>)">
                            <i class="bi bi-send"></i> Kirim Rating
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
.rating-star:hover,
.rating-star.active {
    color: #ffc107 !important;
}
</style>

<script>
function setRating(productId, rating) {
    // Set hidden input value
    document.getElementById('rating_' + productId).value = rating;
    
    // Update star display
    const container = document.querySelector(`.rating-stars[data-product="${productId}"]`);
    const stars = container.querySelectorAll('.rating-star');
    
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('bi-star');
            star.classList.add('bi-star-fill', 'text-warning');
        } else {
            star.classList.remove('bi-star-fill', 'text-warning');
            star.classList.add('bi-star');
        }
    });
}

function submitRating(orderId) {
    const form = document.getElementById('ratingForm' + orderId);
    const formData = new FormData(form);
    
    // Validate all products have rating
    const ratings = form.querySelectorAll('input[name="rating[]"]');
    let allRated = true;
    
    ratings.forEach(input => {
        if (!input.value) {
            allRated = false;
        }
    });
    
    if (!allRated) {
        alert('Mohon beri rating untuk semua produk!');
        return;
    }
    
    fetch('submit_rating.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Rating berhasil dikirim! Terima kasih atas feedback Anda.');
            location.reload();
        } else {
            alert(data.message || 'Gagal mengirim rating');
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan');
    });
}

function cancelOrder(orderId) {
    if (!confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')) {
        return;
    }
    
    fetch('cancel_order.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id_pesanan=${orderId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Pesanan berhasil dibatalkan!');
            location.reload();
        } else {
            alert(data.message || 'Gagal membatalkan pesanan');
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan');
    });
}
</script>

<?php include '../includes/footer.php'; ?>
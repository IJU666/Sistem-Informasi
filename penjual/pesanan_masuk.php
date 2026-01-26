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

// Get filter
$status_filter = $_GET['status'] ?? '';

// Build query - Ambil pesanan yang ada produk dari penjual ini
$query = "
    SELECT DISTINCT
        p.id_pesanan,
        p.tanggal_pesanan,
        p.total,
        p.status_pesanan,
        u.nama as nama_pembeli,
        u.email,
        u.no_hp,
        u.alamat,
        GROUP_CONCAT(DISTINCT pr.nama_produk SEPARATOR ', ') as produk_names,
        SUM(dp.jumlah * dp.harga_satuan) as subtotal_penjual
    FROM pesanan p
    JOIN pengguna u ON p.id_pengguna = u.id_pengguna
    JOIN detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
    JOIN produk pr ON dp.id_produk = pr.id_produk
    WHERE pr.id_penjual = ?
";

$params = [$penjual['id_penjual']];

if ($status_filter) {
    $query .= " AND p.status_pesanan = ?";
    $params[] = $status_filter;
}

$query .= " GROUP BY p.id_pesanan ORDER BY p.tanggal_pesanan DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll();

// Statistics
$stmt = $pdo->prepare("
    SELECT COUNT(DISTINCT p.id_pesanan) 
    FROM pesanan p
    JOIN detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
    JOIN produk pr ON dp.id_produk = pr.id_produk
    WHERE pr.id_penjual = ?
");
$stmt->execute([$penjual['id_penjual']]);
$total_orders = $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT COUNT(DISTINCT p.id_pesanan) 
    FROM pesanan p
    JOIN detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
    JOIN produk pr ON dp.id_produk = pr.id_produk
    WHERE pr.id_penjual = ? AND p.status_pesanan = 'menunggu'
");
$stmt->execute([$penjual['id_penjual']]);
$pending_orders = $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT COUNT(DISTINCT p.id_pesanan) 
    FROM pesanan p
    JOIN detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
    JOIN produk pr ON dp.id_produk = pr.id_produk
    WHERE pr.id_penjual = ? AND p.status_pesanan = 'diproses'
");
$stmt->execute([$penjual['id_penjual']]);
$processing_orders = $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT COUNT(DISTINCT p.id_pesanan) 
    FROM pesanan p
    JOIN detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
    JOIN produk pr ON dp.id_produk = pr.id_produk
    WHERE pr.id_penjual = ? AND p.status_pesanan = 'selesai'
");
$stmt->execute([$penjual['id_penjual']]);
$completed_orders = $stmt->fetchColumn();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h2 class="fw-bold mb-1">Pesanan Masuk</h2>
            <p class="text-muted mb-4">Kelola pesanan dari pembeli</p>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
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
                            <small class="text-muted">Menunggu Konfirmasi</small>
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
                            <small class="text-muted">Sedang Diproses</small>
                            <h3 class="mb-0"><?= number_format($processing_orders) ?></h3>
                        </div>
                        <div class="bg-info bg-opacity-10 text-info rounded p-3">
                            <i class="bi bi-arrow-repeat fs-3"></i>
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
    </div>

    <!-- Filter -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="d-flex gap-2 flex-wrap">
                <a href="pesanan_masuk.php" class="btn btn-<?= !$status_filter ? 'primary' : 'outline-primary' ?>">
                    Semua
                </a>
                <a href="?status=menunggu" class="btn btn-<?= $status_filter == 'menunggu' ? 'warning' : 'outline-warning' ?>">
                    <i class="bi bi-clock"></i> Menunggu Konfirmasi
                </a>
                <a href="?status=diproses" class="btn btn-<?= $status_filter == 'diproses' ? 'info' : 'outline-info' ?>">
                    <i class="bi bi-arrow-repeat"></i> Diproses
                </a>
                <a href="?status=selesai" class="btn btn-<?= $status_filter == 'selesai' ? 'success' : 'outline-success' ?>">
                    <i class="bi bi-check-circle"></i> Selesai
                </a>
                <a href="?status=dibatalkan" class="btn btn-<?= $status_filter == 'dibatalkan' ? 'danger' : 'outline-danger' ?>">
                    <i class="bi bi-x-circle"></i> Dibatalkan
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
                <p class="text-muted mb-0">Belum ada pesanan masuk untuk toko Anda</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): 
            // Get order details (hanya produk dari penjual ini)
            $stmt = $pdo->prepare("
                SELECT dp.*, pr.nama_produk, pr.harga
                FROM detail_pesanan dp
                JOIN produk pr ON dp.id_produk = pr.id_produk
                WHERE dp.id_pesanan = ? AND pr.id_penjual = ?
            ");
            $stmt->execute([$order['id_pesanan'], $penjual['id_penjual']]);
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
                    <div class="col-md-6">
                        <h6 class="mb-1">
                            <i class="bi bi-receipt"></i> Pesanan #<?= $order['id_pesanan'] ?>
                        </h6>
                        <small class="text-muted">
                            <i class="bi bi-calendar"></i> <?= date('d F Y, H:i', strtotime($order['tanggal_pesanan'])) ?>
                        </small>
                    </div>
                    <div class="col-md-6 text-md-end mt-2 mt-md-0">
                        <span class="badge bg-<?= $color ?> px-3 py-2">
                            <?= ucfirst($order['status_pesanan']) ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Customer Info -->
                <div class="alert alert-light mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-2"><i class="bi bi-person"></i> Informasi Pembeli</h6>
                            <p class="mb-1"><strong><?= htmlspecialchars($order['nama_pembeli']) ?></strong></p>
                            <p class="mb-1 small"><i class="bi bi-envelope"></i> <?= htmlspecialchars($order['email']) ?></p>
                            <p class="mb-0 small"><i class="bi bi-phone"></i> <?= htmlspecialchars($order['no_hp']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-2"><i class="bi bi-geo-alt"></i> Alamat Pengiriman</h6>
                            <p class="mb-0 small"><?= htmlspecialchars($order['alamat']) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <h6 class="mb-3">Produk Dipesan:</h6>
                <?php foreach ($details as $item): ?>
                <div class="d-flex align-items-center mb-2 pb-2 border-bottom">
                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-image fs-4 text-muted"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1"><?= htmlspecialchars($item['nama_produk']) ?></h6>
                        <small class="text-muted">
                            <?= $item['jumlah'] ?> x Rp<?= number_format($item['harga_satuan'], 0, ',', '.') ?>
                        </small>
                    </div>
                    <div class="text-end">
                        <h6 class="mb-0 text-primary">Rp<?= number_format($item['jumlah'] * $item['harga_satuan'], 0, ',', '.') ?></h6>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Order Summary & Actions -->
                <div class="row mt-4 align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            Total Anda: <span class="text-primary">Rp<?= number_format($order['subtotal_penjual'], 0, ',', '.') ?></span>
                        </h5>
                        <small class="text-muted">Total keseluruhan: Rp<?= number_format($order['total'], 0, ',', '.') ?></small>
                    </div>
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        <?php if ($order['status_pesanan'] == 'menunggu'): ?>
                            <button class="btn btn-success me-2" onclick="confirmOrder(<?= $order['id_pesanan'] ?>, 'diproses')">
                                <i class="bi bi-check-circle"></i> Terima Pesanan
                            </button>
                            <button class="btn btn-danger" onclick="confirmOrder(<?= $order['id_pesanan'] ?>, 'dibatalkan')">
                                <i class="bi bi-x-circle"></i> Tolak
                            </button>
                        <?php elseif ($order['status_pesanan'] == 'diproses'): ?>
                            <button class="btn btn-success" onclick="confirmOrder(<?= $order['id_pesanan'] ?>, 'selesai')">
                                <i class="bi bi-check-circle"></i> Selesaikan Pesanan
                            </button>
                        <?php elseif ($order['status_pesanan'] == 'selesai'): ?>
                            <span class="badge bg-success fs-6 px-3 py-2">
                                <i class="bi bi-check-circle"></i> Pesanan Selesai
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
function confirmOrder(orderId, newStatus) {
    let message = '';
    if (newStatus === 'diproses') {
        message = 'Terima dan proses pesanan ini?';
    } else if (newStatus === 'selesai') {
        message = 'Tandai pesanan ini sebagai selesai?';
    } else if (newStatus === 'dibatalkan') {
        message = 'Tolak pesanan ini?';
    }
    
    if (!confirm(message)) {
        return;
    }
    
    fetch('update_pesanan.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id_pesanan=${orderId}&status=${newStatus}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Status pesanan berhasil diupdate!');
            location.reload();
        } else {
            alert(data.message || 'Gagal mengupdate pesanan');
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan');
    });
}
</script>

<?php include '../includes/footer.php'; ?>
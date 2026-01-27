<?php
require_once '../config/database.php';
require_once '../config/session.php';

// Cek apakah user adalah penjual
if (!isLoggedIn() || !isPenjual()) {
    header('Location: ../auth/login.php');
    exit;
}

// Get penjual data
$stmt = $pdo->prepare("SELECT * FROM penjual WHERE id_pengguna = ?");
$stmt->execute([getUserId()]);
$penjual = $stmt->fetch();

if (!$penjual) {
    die("Data penjual tidak ditemukan!");
}

// Statistik Dashboard
// Total Produk
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM produk WHERE id_penjual = ?");
$stmt->execute([$penjual['id_penjual']]);
$total_produk = $stmt->fetchColumn();

// Total Penjualan
$stmt = $pdo->prepare("
    SELECT COUNT(DISTINCT ps.id_pesanan) as total_pesanan,
           COALESCE(SUM(dp.jumlah * dp.harga_satuan), 0) as total_pendapatan
    FROM penjual pj
    LEFT JOIN produk p ON pj.id_penjual = p.id_penjual
    LEFT JOIN detail_pesanan dp ON p.id_produk = dp.id_produk
    LEFT JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan
    WHERE pj.id_penjual = ?
");
$stmt->execute([$penjual['id_penjual']]);
$stats = $stmt->fetch();

// Produk Stok Menipis (< 10)
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM produk WHERE id_penjual = ? AND stok < 10");
$stmt->execute([$penjual['id_penjual']]);
$stok_menipis = $stmt->fetchColumn();

// Pesanan Terbaru
$stmt = $pdo->prepare("
    SELECT 
        ps.id_pesanan,
        ps.tanggal_pesanan,
        ps.status_pesanan,
        ps.total,
        u.nama as nama_pembeli,
        GROUP_CONCAT(p.nama_produk SEPARATOR ', ') as produk
    FROM pesanan ps
    JOIN pengguna u ON ps.id_pengguna = u.id_pengguna
    JOIN detail_pesanan dp ON ps.id_pesanan = dp.id_pesanan
    JOIN produk p ON dp.id_produk = p.id_produk
    WHERE p.id_penjual = ?
    GROUP BY ps.id_pesanan
    ORDER BY ps.tanggal_pesanan DESC
    LIMIT 5
");
$stmt->execute([$penjual['id_penjual']]);
$pesanan_terbaru = $stmt->fetchAll();

// Produk Terlaris
$stmt = $pdo->prepare("
    SELECT 
        p.nama_produk,
        p.harga,
        COALESCE(SUM(dp.jumlah), 0) as total_terjual
    FROM produk p
    LEFT JOIN detail_pesanan dp ON p.id_produk = dp.id_produk
    WHERE p.id_penjual = ?
    GROUP BY p.id_produk
    ORDER BY total_terjual DESC
    LIMIT 5
");
$stmt->execute([$penjual['id_penjual']]);
$produk_terlaris = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-2">Dashboard Penjual</h2>
            <p class="text-muted mb-0">Selamat datang, <strong><?= htmlspecialchars($penjual['nama_toko']) ?></strong></p>
        </div>
        <a href="produk_tambah.php" class="btn btn-ngajual">
            <i class="bi bi-plus-circle"></i> Tambah Produk
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Statistik Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Total Produk</h6>
                            <h2 class="mb-0"><?= $total_produk ?></h2>
                        </div>
                        <i class="bi bi-box-seam fs-1"></i>
                    </div>
                </div>
                <div class="card-footer bg-white bg-opacity-25">
                    <a href="produk_list.php" class="text-white text-decoration-none small">
                        Lihat Detail <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Total Pesanan</h6>
                            <h2 class="mb-0"><?= $stats['total_pesanan'] ?></h2>
                        </div>
                        <i class="bi bi-cart-check fs-1"></i>
                    </div>
                </div>
                <div class="card-footer bg-white bg-opacity-25">
                    <a href="laporan.php" class="text-white text-decoration-none small">
                        Lihat Detail <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Total Pendapatan</h6>
                            <h2 class="mb-0">Rp <?= number_format($stats['total_pendapatan'], 0, ',', '.') ?></h2>
                        </div>
                        <i class="bi bi-currency-dollar fs-1"></i>
                    </div>
                </div>
                <div class="card-footer bg-white bg-opacity-25">
                    <a href="laporan.php" class="text-white text-decoration-none small">
                        Lihat Detail <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Stok Menipis</h6>
                            <h2 class="mb-0"><?= $stok_menipis ?></h2>
                        </div>
                        <i class="bi bi-exclamation-triangle fs-1"></i>
                    </div>
                </div>
                <div class="card-footer bg-white bg-opacity-25">
                    <a href="produk_list.php" class="text-white text-decoration-none small">
                        Lihat Detail <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Pesanan Terbaru -->
        <div class="col-md-7 mb-4">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Pesanan Terbaru</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($pesanan_terbaru)): ?>
                        <p class="text-muted text-center py-3">Belum ada pesanan</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tanggal</th>
                                        <th>Pembeli</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pesanan_terbaru as $pesanan): ?>
                                    <tr>
                                        <td>#<?= $pesanan['id_pesanan'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($pesanan['tanggal_pesanan'])) ?></td>
                                        <td><?= htmlspecialchars($pesanan['nama_pembeli']) ?></td>
                                        <td>Rp <?= number_format($pesanan['total'], 0, ',', '.') ?></td>
                                        <td>
                                            <?php
                                            $status_class = [
                                                'menunggu' => 'warning',
                                                'diproses' => 'info',
                                                'selesai' => 'success',
                                                'dibatalkan' => 'danger'
                                            ];
                                            $class = $status_class[$pesanan['status_pesanan']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $class ?>"><?= ucfirst($pesanan['status_pesanan']) ?></span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Produk Terlaris -->
        <div class="col-md-5 mb-4">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-trophy"></i> Produk Terlaris</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($produk_terlaris)): ?>
                        <p class="text-muted text-center py-3">Belum ada data penjualan</p>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($produk_terlaris as $index => $produk): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <span class="badge bg-primary me-2">#<?= $index + 1 ?></span>
                                    <strong><?= htmlspecialchars($produk['nama_produk']) ?></strong>
                                    <br>
                                    <small class="text-muted">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></small>
                                </div>
                                <span class="badge bg-success rounded-pill"><?= $produk['total_terjual'] ?> Terjual</span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <!-- Quick Actions -->
<div class="card shadow">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-lightning"></i> Aksi Cepat</h5>
    </div>
    <div class="card-body">
        <div class="row text-center justify-content-center">
            <div class="col-md-2 col-sm-4 col-6 mb-3">
                <a href="produk_tambah.php" class="text-decoration-none">
                    <div class="p-4 bg-light rounded">
                        <i class="bi bi-plus-circle fs-1 text-primary"></i>
                        <p class="mb-0 mt-2 fw-semibold">Tambah Produk</p>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-sm-4 col-6 mb-3">
                <a href="produk_list.php" class="text-decoration-none">
                    <div class="p-4 bg-light rounded">
                        <i class="bi bi-list-ul fs-1 text-success"></i>
                        <p class="mb-0 mt-2 fw-semibold">Kelola Produk</p>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-sm-4 col-6 mb-3">
                <a href="pesanan_masuk.php" class="text-decoration-none">
                    <div class="p-4 bg-light rounded">
                        <i class="bi bi-bell-fill fs-1 text-warning"></i>
                        <p class="mb-0 mt-2 fw-semibold">Pesanan Masuk</p>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-sm-4 col-6 mb-3">
                <a href="laporan.php" class="text-decoration-none">
                    <div class="p-4 bg-light rounded">
                        <i class="bi bi-graph-up fs-1 text-info"></i>
                        <p class="mb-0 mt-2 fw-semibold">Laporan Penjualan</p>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-sm-4 col-6 mb-3">
                <a href="profil_toko.php" class="text-decoration-none">
                    <div class="p-4 bg-light rounded">
                        <i class="bi bi-shop fs-1 text-warning"></i>
                        <p class="mb-0 mt-2 fw-semibold">Profil Toko</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
</div>

<?php include '../includes/footer.php'; ?>
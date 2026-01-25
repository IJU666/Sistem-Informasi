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

// Filter tanggal
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

// Laporan Penjualan
$stmt = $pdo->prepare("
    SELECT 
        ps.id_pesanan,
        ps.tanggal_pesanan,
        ps.status_pesanan,
        u.nama as nama_pembeli,
        p.nama_produk,
        dp.jumlah,
        dp.harga_satuan,
        (dp.jumlah * dp.harga_satuan) as subtotal
    FROM pesanan ps
    JOIN pengguna u ON ps.id_pengguna = u.id_pengguna
    JOIN detail_pesanan dp ON ps.id_pesanan = dp.id_pesanan
    JOIN produk p ON dp.id_produk = p.id_produk
    WHERE p.id_penjual = ?
    AND DATE(ps.tanggal_pesanan) BETWEEN ? AND ?
    ORDER BY ps.tanggal_pesanan DESC
");
$stmt->execute([$penjual['id_penjual'], $start_date, $end_date]);
$laporan = $stmt->fetchAll();

// Total Pendapatan
$total_pendapatan = array_sum(array_column($laporan, 'subtotal'));
$total_transaksi = count(array_unique(array_column($laporan, 'id_pesanan')));
$total_produk_terjual = array_sum(array_column($laporan, 'jumlah'));

// Produk Terlaris dalam periode
$stmt = $pdo->prepare("
    SELECT 
        p.nama_produk,
        SUM(dp.jumlah) as total_terjual,
        SUM(dp.jumlah * dp.harga_satuan) as total_pendapatan
    FROM produk p
    JOIN detail_pesanan dp ON p.id_produk = dp.id_produk
    JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan
    WHERE p.id_penjual = ?
    AND DATE(ps.tanggal_pesanan) BETWEEN ? AND ?
    GROUP BY p.id_produk
    ORDER BY total_terjual DESC
    LIMIT 10
");
$stmt->execute([$penjual['id_penjual'], $start_date, $end_date]);
$produk_terlaris = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container py-5">
    <h2 class="fw-bold mb-4">Laporan Penjualan</h2>

    <!-- Filter Form -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?= $start_date ?>">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">Tanggal Akhir</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $end_date ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-ngajual w-100">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistik Summary -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    <h6 class="text-uppercase">Total Pendapatan</h6>
                    <h3 class="mb-0">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></h3>
                    <small>Periode: <?= date('d/m/Y', strtotime($start_date)) ?> - <?= date('d/m/Y', strtotime($end_date)) ?></small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-success text-white shadow">
                <div class="card-body">
                    <h6 class="text-uppercase">Total Transaksi</h6>
                    <h3 class="mb-0"><?= $total_transaksi ?></h3>
                    <small>Pesanan berhasil</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-info text-white shadow">
                <div class="card-body">
                    <h6 class="text-uppercase">Produk Terjual</h6>
                    <h3 class="mb-0"><?= $total_produk_terjual ?></h3>
                    <small>Total unit</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Produk Terlaris -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="bi bi-trophy"></i> Produk Terlaris</h5>
        </div>
        <div class="card-body">
            <?php if (empty($produk_terlaris)): ?>
                <p class="text-muted text-center py-3">Belum ada data penjualan dalam periode ini</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Rank</th>
                                <th>Nama Produk</th>
                                <th>Total Terjual</th>
                                <th>Total Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produk_terlaris as $index => $item): ?>
                            <tr>
                                <td>
                                    <?php if ($index < 3): ?>
                                        <span class="badge bg-warning">#<?= $index + 1 ?></span>
                                    <?php else: ?>
                                        #<?= $index + 1 ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($item['nama_produk']) ?></td>
                                <td><strong><?= $item['total_terjual'] ?></strong> unit</td>
                                <td>Rp <?= number_format($item['total_pendapatan'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Detail Transaksi -->
    <div class="card shadow">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-list-check"></i> Detail Transaksi</h5>
            <button onclick="window.print()" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-printer"></i> Cetak
            </button>
        </div>
        <div class="card-body">
            <?php if (empty($laporan)): ?>
                <p class="text-muted text-center py-3">Tidak ada transaksi dalam periode ini</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>ID Pesanan</th>
                                <th>Tanggal</th>
                                <th>Pembeli</th>
                                <th>Produk</th>
                                <th>Qty</th>
                                <th>Harga</th>
                                <th>Subtotal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($laporan as $item): ?>
                            <tr>
                                <td>#<?= $item['id_pesanan'] ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($item['tanggal_pesanan'])) ?></td>
                                <td><?= htmlspecialchars($item['nama_pembeli']) ?></td>
                                <td><?= htmlspecialchars($item['nama_produk']) ?></td>
                                <td><?= $item['jumlah'] ?></td>
                                <td>Rp <?= number_format($item['harga_satuan'], 0, ',', '.') ?></td>
                                <td><strong>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></strong></td>
                                <td>
                                    <?php
                                    $status_class = [
                                        'menunggu' => 'warning',
                                        'diproses' => 'info',
                                        'selesai' => 'success',
                                        'dibatalkan' => 'danger'
                                    ];
                                    $class = $status_class[$item['status_pesanan']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $class ?>"><?= ucfirst($item['status_pesanan']) ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="6" class="text-end">TOTAL:</th>
                                <th colspan="2">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
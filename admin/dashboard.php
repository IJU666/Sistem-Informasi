<?php
// admin/dashboard.php
require_once '../config/session.php';
require_once '../config/database.php';

requireRole('admin');

$page_title = 'Dashboard Admin - Ngalapak';

// Ambil statistik
$total_users = query("SELECT COUNT(*) as total FROM pengguna WHERE status = 'aktif'")->fetch_assoc()['total'];
$total_penjual = query("SELECT COUNT(*) as total FROM penjual")->fetch_assoc()['total'];
$total_produk = query("SELECT COUNT(*) as total FROM produk WHERE status = 'aktif'")->fetch_assoc()['total'];
$total_pesanan = query("SELECT COUNT(*) as total FROM pesanan")->fetch_assoc()['total'];

// Produk terbaru
$produk_result = query("SELECT p.*, k.nama_kategori, pj.nama_toko 
                        FROM produk p
                        JOIN kategori k ON p.id_kategori = k.id_kategori
                        JOIN penjual pj ON p.id_penjual = pj.id_penjual
                        ORDER BY p.tanggal_tambah DESC
                        LIMIT 5");

// User terbaru
$user_result = query("SELECT * FROM pengguna ORDER BY tanggal_daftar DESC LIMIT 5");

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="dashboard-container">
    <div class="container">
        <h2 class="mb-4" style="color: #1e3a8a; font-weight: 700;">
            <i class="bi bi-speedometer2"></i> Dashboard Admin
        </h2>
        
        <!-- Statistik Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="bi bi-people" style="font-size: 2rem;"></i>
                    <h3><?php echo $total_users; ?></h3>
                    <p>Total Pengguna Aktif</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);">
                    <i class="bi bi-shop" style="font-size: 2rem;"></i>
                    <h3><?php echo $total_penjual; ?></h3>
                    <p>Total Penjual</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);">
                    <i class="bi bi-box-seam" style="font-size: 2rem;"></i>
                    <h3><?php echo $total_produk; ?></h3>
                    <p>Total Produk Aktif</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);">
                    <i class="bi bi-cart-check" style="font-size: 2rem;"></i>
                    <h3><?php echo $total_pesanan; ?></h3>
                    <p>Total Pesanan</p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Produk Terbaru -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-box-seam"></i> Produk Terbaru
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Toko</th>
                                        <th>Harga</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($produk = $produk_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $produk['nama_produk']; ?></td>
                                            <td><?php echo $produk['nama_toko']; ?></td>
                                            <td>Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?></td>
                                            <td>
                                                <span class="badge <?php echo $produk['status'] === 'aktif' ? 'badge-success' : 'badge-danger'; ?>">
                                                    <?php echo ucfirst($produk['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- User Terbaru -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-people"></i> Pengguna Terbaru
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($user = $user_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $user['nama_lengkap']; ?></td>
                                            <td><?php echo $user['email']; ?></td>
                                            <td>
                                                <span class="badge badge-primary">
                                                    <?php echo ucfirst($user['role']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo $user['status'] === 'aktif' ? 'badge-success' : 'badge-danger'; ?>">
                                                    <?php echo ucfirst($user['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Menu Akses Cepat -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-grid"></i> Menu Admin
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <a href="kelola_pengguna.php" class="btn btn-outline w-100">
                                    <i class="bi bi-people"></i><br>Kelola Pengguna
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="kelola_kategori.php" class="btn btn-outline w-100">
                                    <i class="bi bi-tags"></i><br>Kelola Kategori
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="laporan_global.php" class="btn btn-outline w-100">
                                    <i class="bi bi-file-earmark-bar-graph"></i><br>Laporan Global
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="../produk/katalog.php" class="btn btn-outline w-100">
                                    <i class="bi bi-box-seam"></i><br>Lihat Semua Produk
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
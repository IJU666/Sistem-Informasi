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

// Get all products
$stmt = $pdo->prepare("
    SELECT 
        p.*,
        k.nama_kategori,
        COALESCE(SUM(dp.jumlah), 0) as total_terjual
    FROM produk p
    JOIN kategori k ON p.id_kategori = k.id_kategori
    LEFT JOIN detail_pesanan dp ON p.id_produk = dp.id_produk
    WHERE p.id_penjual = ?
    GROUP BY p.id_produk
    ORDER BY p.created_at DESC
");
$stmt->execute([$penjual['id_penjual']]);
$produks = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-2">Kelola Produk</h2>
            <p class="text-muted mb-0">Daftar semua produk Anda</p>
        </div>
        <a href="produk_tambah.php" class="btn btn-ngajual">
            <i class="bi bi-plus-circle"></i> Tambah Produk Baru
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-body">
            <?php if (empty($produks)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="text-muted mt-3 mb-4">Anda belum memiliki produk</p>
                    <a href="produk_tambah.php" class="btn btn-ngajual">
                        <i class="bi bi-plus-circle"></i> Tambah Produk Pertama
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Terjual</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produks as $produk): ?>
                            <tr>
                                <td><?= $produk['id_produk'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($produk['nama_produk']) ?></strong>
                                    <br>
                                    <small class="text-muted"><?= substr(htmlspecialchars($produk['deskripsi']), 0, 50) ?>...</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($produk['nama_kategori']) ?></span>
                                </td>
                                <td>Rp <?= number_format($produk['harga'], 0, ',', '.') ?></td>
                                <td>
                                    <?php if ($produk['stok'] < 10): ?>
                                        <span class="badge bg-warning text-dark"><?= $produk['stok'] ?></span>
                                    <?php else: ?>
                                        <?= $produk['stok'] ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= $produk['total_terjual'] ?></td>
                                <td>
                                    <?php if ($produk['status_produk'] == 'aktif'): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="produk_edit.php?id=<?= $produk['id_produk'] ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="../produk/detail.php?id=<?= $produk['id_produk'] ?>" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="Lihat"
                                           target="_blank">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="produk_hapus.php?id=<?= $produk['id_produk'] ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           title="Hapus"
                                           onclick="return confirmDelete('Apakah Anda yakin ingin menghapus produk ini?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <p class="text-muted mb-0">Total: <strong><?= count($produks) ?></strong> produk</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
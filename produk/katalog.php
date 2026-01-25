<?php
require_once '../config/database.php';
require_once '../config/session.php';
include '../includes/header.php';
include '../includes/navbar.php';

// Filter kategori
$kategori_filter = $_GET['kategori'] ?? '';
$search = $_GET['q'] ?? '';

// Build query
$query = "
    SELECT 
        p.id_produk,
        p.nama_produk,
        p.harga,
        p.stok,
        pj.nama_toko,
        k.nama_kategori,
        COALESCE(AVG(r.rating), 0) as avg_rating,
        COUNT(DISTINCT r.id_rating) as total_rating
    FROM produk p
    JOIN penjual pj ON p.id_penjual = pj.id_penjual
    JOIN kategori k ON p.id_kategori = k.id_kategori
    LEFT JOIN rating r ON p.id_produk = r.id_produk
    WHERE p.status_produk = 'aktif'
";

$params = [];

if ($kategori_filter) {
    $query .= " AND p.id_kategori = ?";
    $params[] = $kategori_filter;
}

if ($search) {
    $query .= " AND (p.nama_produk LIKE ? OR p.deskripsi LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " GROUP BY p.id_produk ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$produks = $stmt->fetchAll();

// Get all categories
$stmt_kat = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori");
$kategoris = $stmt_kat->fetchAll();
?>

<div class="container py-5">
    <h2 class="fw-bold mb-4">Katalog Produk</h2>
    
    <div class="row">
        <!-- Sidebar Filter -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-funnel"></i> Filter</h5>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Kategori</h6>
                    <div class="list-group">
                        <a href="katalog.php" class="list-group-item list-group-item-action <?= !$kategori_filter ? 'active' : '' ?>">
                            Semua Kategori
                        </a>
                        <?php foreach ($kategoris as $kat): ?>
                        <a href="katalog.php?kategori=<?= $kat['id_kategori'] ?>" 
                           class="list-group-item list-group-item-action <?= $kategori_filter == $kat['id_kategori'] ? 'active' : '' ?>">
                            <?= htmlspecialchars($kat['nama_kategori']) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Product Grid -->
        <div class="col-md-9">
            <?php if ($search): ?>
            <div class="alert alert-info">
                Hasil pencarian untuk: <strong><?= htmlspecialchars($search) ?></strong>
                (<?= count($produks) ?> produk ditemukan)
            </div>
            <?php endif; ?>
            
            <?php if (empty($produks)): ?>
            <div class="alert alert-warning text-center">
                <i class="bi bi-inbox fs-1"></i>
                <p class="mb-0 mt-3">Tidak ada produk ditemukan</p>
            </div>
            <?php else: ?>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($produks as $produk): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="bg-light text-center" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-image" style="font-size: 80px; color: #ccc;"></i>
                        </div>
                        
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($produk['nama_produk']) ?></h5>
                            <p class="text-muted small mb-2">
                                <i class="bi bi-shop"></i> <?= htmlspecialchars($produk['nama_toko']) ?>
                            </p>
                            <p class="text-muted small mb-2">
                                <i class="bi bi-tag"></i> <?= htmlspecialchars($produk['nama_kategori']) ?>
                            </p>
                            
                            <?php if ($produk['total_rating'] > 0): ?>
                            <div class="d-flex align-items-center small text-warning mb-2">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi bi-star<?= $i <= round($produk['avg_rating']) ? '-fill' : '' ?>"></i>
                                <?php endfor; ?>
                                <span class="text-muted ms-2">(<?= $produk['total_rating'] ?>)</span>
                            </div>
                            <?php endif; ?>
                            
                            <h5 class="text-primary mb-2">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></h5>
                            <p class="text-muted small mb-0">Stok: <?= $produk['stok'] ?></p>
                        </div>
                        
                        <div class="card-footer bg-transparent">
                            <a href="detail.php?id=<?= $produk['id_produk'] ?>" class="btn btn-outline-primary w-100">
                                <i class="bi bi-eye"></i> Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
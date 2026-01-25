<?php
require_once 'config/database.php';
require_once 'config/session.php';
include 'includes/header.php';
include 'includes/navbar.php';

// Ambil data kategori
$stmt_kategori = $pdo->query("SELECT * FROM kategori LIMIT 5");
$kategoris = $stmt_kategori->fetchAll();

// Ambil produk terlaris (berdasarkan jumlah penjualan)
$stmt_produk = $pdo->query("
    SELECT 
        p.id_produk,
        p.nama_produk,
        p.harga,
        p.stok,
        pj.nama_toko,
        k.nama_kategori,
        COALESCE(SUM(dp.jumlah), 0) as total_terjual
    FROM produk p
    JOIN penjual pj ON p.id_penjual = pj.id_penjual
    JOIN kategori k ON p.id_kategori = k.id_kategori
    LEFT JOIN detail_pesanan dp ON p.id_produk = dp.id_produk
    WHERE p.status_produk = 'aktif'
    GROUP BY p.id_produk
    ORDER BY total_terjual DESC
    LIMIT 8
");
$produks = $stmt_produk->fetchAll();

// Cek rating produk
function getRating($pdo, $id_produk) {
    $stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total_rating FROM rating WHERE id_produk = ?");
    $stmt->execute([$id_produk]);
    return $stmt->fetch();
}
?>

<!-- Hero Section -->
<header class="text-white d-flex align-items-center hero-banner" style="background: linear-gradient(135deg, #243796 0%, #475ED2 100%); min-height: 50vh;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">Platform Mahasiswa: Jualan Gampang, Belanja Nyaman</h1>
                <p class="lead mb-4">Hanya dengan satu website</p>
                <a href="produk/katalog.php" class="btn btn-light btn-lg">Lihat Produk</a>
            </div>
        </div>
    </div>
</header>

<!-- Kategori Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h3 class="fw-bold mb-4">Kategori Atas</h3>
        <div class="row justify-content-center text-center">
            <?php foreach ($kategoris as $kategori): ?>
            <div class="col-6 col-md-2 mb-4">
                <a href="produk/katalog.php?kategori=<?= $kategori['id_kategori'] ?>" class="text-decoration-none text-dark">
                    <div class="kategori-icon bg-white rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 120px; height: 120px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <i class="bi bi-box-seam fs-1 text-primary"></i>
                    </div>
                    <p class="small fw-semibold mb-0 mt-3"><?= htmlspecialchars($kategori['nama_kategori']) ?></p>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Promo Banner -->
<header class="text-dark d-flex align-items-center" style="background:  url('assets/promo.png'); background-size: cover; background-position: center; min-height:40vh">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-5 fw-bold mb-3 ngajual " style="color: #243796;">Dapatkan Barang<br>Berkualitas<br>dengan Aman</h1>
                <a href="produk/katalog.php" class="btn text-light btn-lg btn-ngajual" style="background-color: #243796; !important">Lihat Produk</a>
            </div>
        </div>
    </div>
</header>

<!-- Produk Teratas Section -->
<section class="py-5">
    <div class="container px-4 px-lg-5 mt-5">
        <h3 class="fw-bold mb-4">Penjualan Teratas</h3>
        <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-start">
            <?php foreach ($produks as $produk): 
                $rating_data = getRating($pdo, $produk['id_produk']);
                $avg_rating = $rating_data['avg_rating'] ?? 0;
                $total_rating = $rating_data['total_rating'] ?? 0;
            ?>
            <div class="col mb-5">
                <div class="card h-100 shadow-sm">
                    <!-- Sale Badge (jika ada diskon) -->
                    <?php if ($produk['total_terjual'] > 2): ?>
                    <div class="badge bg-danger text-white position-absolute" style="top: 0.5rem; right: 0.5rem">Terlaris</div>
                    <?php endif; ?>
                    
                    <!-- Product image-->
                    <div class="bg-light text-center" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-image" style="font-size: 80px; color: #ccc;"></i>
                    </div>
                    
                    <!-- Product details-->
                    <div class="card-body p-4">
                        <div class="text-center">
                            <!-- Product name-->
                            <h5 class="fw-bolder"><?= htmlspecialchars($produk['nama_produk']) ?></h5>
                            <p class="text-muted small mb-2"><?= htmlspecialchars($produk['nama_toko']) ?></p>
                            
                            <!-- Product reviews-->
                            <?php if ($total_rating > 0): ?>
                            <div class="d-flex justify-content-center small text-warning mb-2">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= round($avg_rating)): ?>
                                        <div class="bi-star-fill"></div>
                                    <?php else: ?>
                                        <div class="bi-star"></div>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                <span class="text-muted ms-1">(<?= $total_rating ?>)</span>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Product price-->
                            <span class="fw-bold text-primary">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></span>
                            <p class="text-muted small mb-0">Stok: <?= $produk['stok'] ?></p>
                        </div>
                    </div>
                    
                    <!-- Product actions-->
                    <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                        <div class="text-center">
                            <a class="btn btn-outline-primary mt-auto w-100" href="produk/detail.php?id=<?= $produk['id_produk'] ?>">
                                <i class="bi bi-eye"></i> Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="produk/katalog.php" class="btn btn-primary btn-lg" style="background-color: #243796; border: 0px; !important">Lihat Semua Produk</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
<!-- balergefefuyhefhuehfiuehfjei -->
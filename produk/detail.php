<?php
require_once '../config/database.php';
require_once '../config/session.php';

// Get product ID
$id_produk = $_GET['id'] ?? 0;

// Get product data with seller info
$stmt = $pdo->prepare("
    SELECT 
        p.*,
        k.nama_kategori,
        pj.nama_toko,
        pj.deskripsi_toko,
        p.foto_produk,
        u.nama as nama_penjual,
        COALESCE(AVG(r.rating), 0) as avg_rating,
        COUNT(DISTINCT r.id_rating) as total_rating,
        COALESCE(SUM(dp.jumlah), 0) as total_terjual
    FROM produk p
    JOIN kategori k ON p.id_kategori = k.id_kategori
    JOIN penjual pj ON p.id_penjual = pj.id_penjual
    JOIN pengguna u ON pj.id_pengguna = u.id_pengguna
    LEFT JOIN rating r ON p.id_produk = r.id_produk
    LEFT JOIN detail_pesanan dp ON p.id_produk = dp.id_produk
    WHERE p.id_produk = ? AND p.status_produk = 'aktif'
    GROUP BY p.id_produk
");
$stmt->execute([$id_produk]);
$produk = $stmt->fetch();

if (!$produk) {
    header('Location: katalog.php');
    exit;
}

// Prepare product images (support up to 3 images)
$product_images = [];
if (!empty($produk['foto_produk'])) {
    // Main image
    $product_images[] = $produk['foto_produk'];
    
    // Look for additional images with pattern: produk_timestamp_uniqid_1.ext, produk_timestamp_uniqid_2.ext
    $main_image_info = pathinfo($produk['foto_produk']);
    $base_name = preg_replace('/_\d+\./', '_', $main_image_info['basename']); // Remove the index number
    
    // Try to find up to 2 more images
    for ($i = 1; $i <= 2; $i++) {
        $additional_image = str_replace('.' . $main_image_info['extension'], "_$i." . $main_image_info['extension'], $produk['foto_produk']);
        if (file_exists('../assets/img/uploads/' . $additional_image)) {
            $product_images[] = $additional_image;
        }
    }
}

// Get reviews
$stmt = $pdo->prepare("
    SELECT r.*, u.nama 
    FROM rating r
    JOIN pengguna u ON r.id_pengguna = u.id_pengguna
    WHERE r.id_produk = ?
    ORDER BY r.tanggal DESC
    LIMIT 4
");
$stmt->execute([$id_produk]);
$reviews = $stmt->fetchAll();

// Get related products (same category)
$stmt = $pdo->prepare("
    SELECT 
        p.*,
        pj.nama_toko,
        COALESCE(AVG(r.rating), 0) as avg_rating,
        COUNT(DISTINCT r.id_rating) as total_rating
    FROM produk p
    JOIN penjual pj ON p.id_penjual = pj.id_penjual
    LEFT JOIN rating r ON p.id_produk = r.id_produk
    WHERE p.id_kategori = ? AND p.id_produk != ? AND p.status_produk = 'aktif'
    GROUP BY p.id_produk
    LIMIT 6
");
$stmt->execute([$produk['id_kategori'], $id_produk]);
$related_products = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<style>
.breadcrumb {
    background: transparent;
    padding: 1rem 0;
}
.product-image-container {
    position: relative;
    background: #f8f9fa;
    border-radius: 10px;
    overflow: hidden;
    height: 450px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.product-main-image {
    max-width: 100%;
    max-height: 450px;
    object-fit: contain;
    background: white;
    transition: opacity 0.5s ease-in-out;
}
.product-main-image.fade-out {
    opacity: 0;
}
.product-thumbnails {
    display: flex;
    gap: 10px;
    margin-top: 15px;
    justify-content: center;
}
.thumbnail {
    width: 100px;
    height: 100px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    cursor: pointer;
    overflow: hidden;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
}
.thumbnail:hover, .thumbnail.active {
    border-color: #243797;
    box-shadow: 0 2px 8px rgba(36, 55, 150, 0.3);
}
.thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.rating-stars {
    color: #ffc107;
}
.product-price {
    font-size: 2rem;
    font-weight: bold;
    color: #e74c3c;
}
.qty-control {
    display: flex;
    align-items: center;
    gap: 10px;
}
.qty-btn {
    width: 35px;
    height: 35px;
    border: 1px solid #ddd;
    background: white;
    border-radius: 5px;
    cursor: pointer;
}
.qty-input {
    width: 60px;
    text-align: center;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 5px;
}
.seller-card {
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 15px;
    background: #f8f9fa;
}
.review-card {
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 15px;
}
.verified-badge {
    color: #28a745;
    font-size: 1.2rem;
}
.related-product-card {
    transition: transform 0.3s;
}
.related-product-card:hover {
    transform: translateY(-5px);
}
.discount-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #e74c3c;
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
    font-weight: bold;
    font-size: 0.85rem;
}
.stock-badge {
    background: #28a745;
    color: white;
    padding: 3px 10px;
    border-radius: 5px;
    font-size: 0.85rem;
}
.image-placeholder {
    font-size: 150px;
    color: #ddd;
}
</style>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php">Beranda</a></li>
            <li class="breadcrumb-item"><a href="katalog.php"><?= htmlspecialchars($produk['nama_kategori']) ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($produk['nama_produk']) ?></li>
        </ol>
    </nav>

    <div class="row">
        <!-- Product Images -->
        <div class="col-md-5 mb-4">
            <div class="product-image-container" id="mainImageContainer">
                <?php if (!empty($product_images)): ?>
                    <img src="../assets/img/uploads/<?= htmlspecialchars($product_images[0]) ?>" 
                         alt="<?= htmlspecialchars($produk['nama_produk']) ?>" 
                         class="product-main-image" 
                         id="mainImage">
                <?php else: ?>
                    <i class="bi bi-image image-placeholder"></i>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($product_images)): ?>
            <div class="product-thumbnails">
                <?php foreach ($product_images as $index => $image): ?>
                <div class="thumbnail <?= $index === 0 ? 'active' : '' ?>" onclick="changeImage(<?= $index ?>)" data-index="<?= $index ?>">
                    <img src="../assets/img/uploads/<?= htmlspecialchars($image) ?>" alt="Thumbnail <?= $index + 1 ?>">
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Product Info -->
        <div class="col-md-7 mb-4">
            <h2 class="fw-bold mb-3"><?= htmlspecialchars($produk['nama_produk']) ?></h2>
            
            <!-- Rating -->
            <div class="d-flex align-items-center mb-3">
                <div class="rating-stars me-2">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="bi bi-star<?= $i <= round($produk['avg_rating']) ? '-fill' : '' ?>"></i>
                    <?php endfor; ?>
                </div>
                <span class="fw-bold"><?= number_format($produk['avg_rating'], 1) ?></span>
                <span class="text-muted ms-2">(<?= $produk['total_rating'] ?>)</span>
                <span class="text-muted ms-3">SKU: <?= strtoupper(substr(md5($produk['id_produk']), 0, 10)) ?></span>
            </div>

            <!-- Description Preview -->
            <p class="text-muted mb-3">
                <?= substr(htmlspecialchars($produk['deskripsi']), 0, 150) ?>...
            </p>

            <!-- Price -->
            <div class="product-price mb-4">
                Rp <?= number_format($produk['harga'], 0, ',', '.') ?>
            </div>

            <!-- Quantity Control -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Jumlah:</label>
                <div class="qty-control">
                    <button class="qty-btn" onclick="decreaseQty()">-</button>
                    <input type="number" class="qty-input" id="qty" value="1" min="1" max="<?= $produk['stok'] ?>">
                    <button class="qty-btn" onclick="increaseQty()">+</button>
                    <span class="text-muted ms-2">Stok tersedia: <?= $produk['stok'] ?></span>
                </div>
            </div>

            <!-- Add to Cart Button -->
            <button class="btn btn-lg w-100 mb-3" style="background-color: #243796; color: white;" onclick="addToCart(<?= $produk['id_produk'] ?>)">
                <i class="bi bi-cart-plus"></i> Tambah Ke Keranjang
            </button>

            <!-- Seller Info -->
            <div class="seller-card mt-4">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #243796; color: white;">
                            <i class="bi bi-person fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold"><?= htmlspecialchars($produk['nama_toko']) ?></h6>
                        <small class="text-muted"><?= htmlspecialchars($produk['nama_penjual']) ?></small>
                        <div class="d-flex align-items-center">
                            <span class="rating-stars me-1">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi bi-star-fill small"></i>
                                <?php endfor; ?>
                            </span>
                            <span class="small"><?= number_format($produk['avg_rating'], 1) ?></span>
                        </div>
                    </div>
                    <button class="btn btn-outline-primary btn-sm">Follow</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs: Description & Reviews -->
    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button">
                        Deskripsi
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button">
                        Ulasan (<?= $produk['total_rating'] ?>)
                    </button>
                </li>
            </ul>
            <div class="tab-content p-4 border border-top-0" id="productTabsContent">
                <!-- Description Tab -->
                <div class="tab-pane fade show active" id="description" role="tabpanel">
                    <p><?= nl2br(htmlspecialchars($produk['deskripsi'])) ?></p>
                </div>

                <!-- Reviews Tab -->
                <div class="tab-pane fade" id="reviews" role="tabpanel">
                    <?php if (empty($reviews)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-chat-quote fs-1 text-muted"></i>
                            <p class="text-muted mt-3">Belum ada ulasan untuk produk ini</p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($reviews as $review): ?>
                            <div class="col-md-6 mb-3">
                                <div class="review-card">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <div class="rating-stars mb-1">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="bi bi-star<?= $i <= $review['rating'] ? '-fill' : '' ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <strong><?= htmlspecialchars($review['nama']) ?></strong>
                                                <i class="bi bi-check-circle-fill verified-badge ms-2"></i>
                                            </div>
                                        </div>
                                        <i class="bi bi-three-dots"></i>
                                    </div>
                                    <p class="mb-2">"<?= htmlspecialchars($review['komentar']) ?>"</p>
                                    <small class="text-muted">Posted on <?= date('F d, Y', strtotime($review['tanggal'])) ?></small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($related_products)): ?>
    <div class="mt-5">
        <h4 class="fw-bold mb-4">Lainnya untukmu</h4>
        <div class="row row-cols-2 row-cols-md-6 g-3">
            <?php foreach ($related_products as $related): ?>
            <div class="col">
                <a href="detail.php?id=<?= $related['id_produk'] ?>" class="text-decoration-none">
                    <div class="card h-100 related-product-card shadow-sm">
                        <div class="position-relative">
                            <div class="bg-light text-center" style="height: 150px; display: flex; align-items: center; justify-content: center;">
                                <?php if (!empty($related['foto_produk'])): ?>
                                    <img src="../assets/img/uploads/<?= htmlspecialchars($related['foto_produk']) ?>" 
                                         alt="<?= htmlspecialchars($related['nama_produk']) ?>" 
                                         style="max-width: 100%; max-height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <i class="bi bi-image" style="font-size: 60px; color: #ddd;"></i>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body p-2">
                            <p class="small mb-1 text-muted"><?= htmlspecialchars($related['nama_toko']) ?></p>
                            <h6 class="mb-1 text-dark"><?= htmlspecialchars($related['nama_produk']) ?></h6>
                            <div class="rating-stars small mb-1">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi bi-star<?= $i <= round($related['avg_rating']) ? '-fill' : '' ?>"></i>
                                <?php endfor; ?>
                                <span class="text-muted">(<?= $related['total_rating'] ?>)</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-danger fw-bold">Rp <?= number_format($related['harga'], 0, ',', '.') ?></span>
                            </div>
                            <div class="mt-2">
                                <span class="stock-badge"><i class="bi bi-check-circle"></i> IN STOCK</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// Image gallery variables
const productImages = <?= json_encode($product_images) ?>;
let currentImageIndex = 0;
let autoSlideInterval;

// Change image function
function changeImage(index) {
    const mainImage = document.getElementById('mainImage');
    const thumbnails = document.querySelectorAll('.thumbnail');
    
    if (!mainImage || index >= productImages.length) return;
    
    // Fade out effect
    mainImage.classList.add('fade-out');
    
    setTimeout(() => {
        mainImage.src = '../assets/img/uploads/' + productImages[index];
        mainImage.classList.remove('fade-out');
    }, 250);
    
    // Update active thumbnail
    thumbnails.forEach((thumb, i) => {
        if (i === index) {
            thumb.classList.add('active');
        } else {
            thumb.classList.remove('active');
        }
    });
    
    currentImageIndex = index;
}

// Auto slide images
function startAutoSlide() {
    if (productImages.length <= 1) return;
    
    autoSlideInterval = setInterval(() => {
        currentImageIndex = (currentImageIndex + 1) % productImages.length;
        changeImage(currentImageIndex);
    }, 3000); // Change image every 3 seconds
}

// Stop auto slide when user interacts
function stopAutoSlide() {
    if (autoSlideInterval) {
        clearInterval(autoSlideInterval);
    }
}

// Start auto slide on page load
if (productImages.length > 1) {
    startAutoSlide();
}

// Stop auto slide when user clicks thumbnail
document.querySelectorAll('.thumbnail').forEach(thumb => {
    thumb.addEventListener('click', () => {
        stopAutoSlide();
        // Restart after 10 seconds of inactivity
        setTimeout(startAutoSlide, 10000);
    });
});

// Quantity controls
function increaseQty() {
    const qtyInput = document.getElementById('qty');
    const max = parseInt(qtyInput.max);
    const current = parseInt(qtyInput.value);
    if (current < max) {
        qtyInput.value = current + 1;
    }
}

function decreaseQty() {
    const qtyInput = document.getElementById('qty');
    const current = parseInt(qtyInput.value);
    if (current > 1) {
        qtyInput.value = current - 1;
    }
}

// Add to cart function
function addToCart(productId) {
    <?php if (!isLoggedIn()): ?>
        alert('Silakan login terlebih dahulu!');
        window.location.href = '../auth/login.php';
        return;
    <?php endif; ?>

    const qty = document.getElementById('qty').value;
    const button = event.target;
    
    // Disable button and show loading
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menambahkan...';
    
    // Send to server
    fetch('../keranjang/tambah.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id_produk=${productId}&jumlah=${qty}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.innerHTML = '<i class="bi bi-check-circle"></i> Ditambahkan!';
            button.style.backgroundColor = '#28a745';
            
            setTimeout(() => {
                button.innerHTML = '<i class="bi bi-cart-plus"></i> Tambah Ke Keranjang';
                button.style.backgroundColor = '#243796';
                button.disabled = false;
            }, 2000);
            
            // Update cart counter in navbar
            location.reload();
        } else {
            alert(data.message || 'Gagal menambahkan ke keranjang');
            button.innerHTML = '<i class="bi bi-cart-plus"></i> Tambah Ke Keranjang';
            button.disabled = false;
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan');
        button.innerHTML = '<i class="bi bi-cart-plus"></i> Tambah Ke Keranjang';
        button.disabled = false;
    });
}
</script>

<?php include '../includes/footer.php'; ?>
<?php
require_once '../config/database.php';
require_once '../config/session.php';

if (!isLoggedIn()) {
    header('Location: ../auth/login.php');
    exit;
}

// Get user data
$stmt = $pdo->prepare("SELECT * FROM pengguna WHERE id_pengguna = ?");
$stmt->execute([getUserId()]);
$user = $stmt->fetch();

// Get cart items
$stmt = $pdo->prepare("
    SELECT 
        ik.id_isi,
        ik.jumlah,
        p.id_produk,
        p.nama_produk,
        p.harga,
        p.stok,
        pj.nama_toko,
        (ik.jumlah * p.harga) as subtotal
    FROM keranjang k
    JOIN isi_keranjang ik ON k.id_keranjang = ik.id_keranjang
    JOIN produk p ON ik.id_produk = p.id_produk
    JOIN penjual pj ON p.id_penjual = pj.id_penjual
    WHERE k.id_pengguna = ?
");
$stmt->execute([getUserId()]);
$cart_items = $stmt->fetchAll();

if (empty($cart_items)) {
    $_SESSION['error'] = 'Keranjang Anda kosong!';
    header('Location: index.php');
    exit;
}

// Calculate totals
$total_items = count($cart_items);
$subtotal = array_sum(array_column($cart_items, 'subtotal'));
$ongkir = $subtotal >= 100000 ? 0 : 10000;
$total = $subtotal + $ongkir;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_penerima = trim($_POST['nama_penerima']);
    $no_hp = trim($_POST['no_hp']);
    $alamat_pengiriman = trim($_POST['alamat_pengiriman']);
    $metode_pembayaran = $_POST['metode_pembayaran'];
    $catatan = trim($_POST['catatan'] ?? '');
    
    if (empty($nama_penerima) || empty($no_hp) || empty($alamat_pengiriman) || empty($metode_pembayaran)) {
        $_SESSION['error'] = 'Semua field wajib diisi!';
    } else {
        try {
            $pdo->beginTransaction();
            
            // Create pesanan
            $stmt = $pdo->prepare("
                INSERT INTO pesanan (id_pengguna, tanggal_pesanan, total, status_pesanan)
                VALUES (?, NOW(), ?, 'menunggu')
            ");
            $stmt->execute([getUserId(), $total]);
            $id_pesanan = $pdo->lastInsertId();
            
            // Insert detail pesanan
            foreach ($cart_items as $item) {
                $stmt = $pdo->prepare("
                    INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, harga_satuan)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$id_pesanan, $item['id_produk'], $item['jumlah'], $item['harga']]);
                
                // Update stok produk
                $stmt = $pdo->prepare("UPDATE produk SET stok = stok - ? WHERE id_produk = ?");
                $stmt->execute([$item['jumlah'], $item['id_produk']]);
            }
            
            // Clear cart
            $stmt = $pdo->prepare("
                DELETE FROM isi_keranjang 
                WHERE id_keranjang = (SELECT id_keranjang FROM keranjang WHERE id_pengguna = ?)
            ");
            $stmt->execute([getUserId()]);
            
            $pdo->commit();
            
            $_SESSION['success'] = 'Pesanan berhasil dibuat! ID Pesanan: #' . $id_pesanan;
            header('Location: ../pembeli/riwayat_pesanan.php');
            exit;
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container py-5">
    <h2 class="fw-bold mb-4">Checkout</h2>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="row">
            <!-- Left Column: Shipping & Payment -->
            <div class="col-lg-8 mb-4">
                <!-- Shipping Address -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Alamat Pengiriman</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="nama_penerima" class="form-label">Nama Penerima <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_penerima" name="nama_penerima" value="<?= htmlspecialchars($user['nama']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="no_hp" class="form-label">No. HP <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="no_hp" name="no_hp" value="<?= htmlspecialchars($user['no_hp']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="alamat_pengiriman" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="alamat_pengiriman" name="alamat_pengiriman" rows="3" required><?= htmlspecialchars($user['alamat']) ?></textarea>
                        </div>
                        <div class="mb-0">
                            <label for="catatan" class="form-label">Catatan untuk Penjual (Opsional)</label>
                            <textarea class="form-control" id="catatan" name="catatan" rows="2" placeholder="Contoh: Jangan dibunyikan bel, langsung telpon saja"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-credit-card"></i> Metode Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="metode_pembayaran" id="transfer" value="transfer" required>
                            <label class="form-check-label w-100" for="transfer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Transfer Bank</strong>
                                        <p class="text-muted small mb-0">BCA, Mandiri, BRI, BNI</p>
                                    </div>
                                    <i class="bi bi-bank fs-4 text-primary"></i>
                                </div>
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="metode_pembayaran" id="cod" value="cod">
                            <label class="form-check-label w-100" for="cod">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>COD (Cash on Delivery)</strong>
                                        <p class="text-muted small mb-0">Bayar di tempat saat barang tiba</p>
                                    </div>
                                    <i class="bi bi-cash-coin fs-4 text-success"></i>
                                </div>
                            </label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="radio" name="metode_pembayaran" id="ewallet" value="ewallet">
                            <label class="form-check-label w-100" for="ewallet">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>E-Wallet</strong>
                                        <p class="text-muted small mb-0">GoPay, OVO, Dana, ShopeePay</p>
                                    </div>
                                    <i class="bi bi-wallet2 fs-4 text-warning"></i>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Products Summary -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-box-seam"></i> Produk Dipesan (<?= $total_items ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($cart_items as $item): ?>
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="bi bi-image fs-4 text-muted"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1"><?= htmlspecialchars($item['nama_produk']) ?></h6>
                                <small class="text-muted"><?= htmlspecialchars($item['nama_toko']) ?></small>
                            </div>
                            <div class="text-end">
                                <p class="mb-0 fw-bold">Rp <?= number_format($item['harga'], 0, ',', '.') ?></p>
                                <small class="text-muted">x<?= $item['jumlah'] ?></small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-receipt"></i> Ringkasan Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span>Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Ongkos Kirim</span>
                            <span class="<?= $ongkir == 0 ? 'text-success' : '' ?>">
                                <?= $ongkir == 0 ? 'GRATIS' : 'Rp ' . number_format($ongkir, 0, ',', '.') ?>
                            </span>
                        </div>
                        
                        <?php if ($subtotal >= 100000): ?>
                        <div class="alert alert-success small mt-2 mb-3" role="alert">
                            <i class="bi bi-check-circle"></i> Anda mendapat gratis ongkir!
                        </div>
                        <?php endif; ?>
                        
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="h5 fw-bold">Total Pembayaran</span>
                            <span class="h5 fw-bold text-primary">Rp <?= number_format($total, 0, ',', '.') ?></span>
                        </div>
                        
                        <button type="submit" class="btn btn-ngajual btn-lg w-100 mb-3">
                            <i class="bi bi-check-circle"></i> Buat Pesanan
                        </button>
                        
                        <a href="index.php" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-left"></i> Kembali ke Keranjang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
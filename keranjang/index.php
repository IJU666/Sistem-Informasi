<?php
require_once '../config/database.php';
require_once '../config/session.php';

if (!isLoggedIn()) {
    header('Location: ../auth/login.php');
    exit;
}

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

// Calculate totals
$total_items = count($cart_items);
$subtotal = array_sum(array_column($cart_items, 'subtotal'));
$ongkir = 10000; // Fixed shipping cost
$total = $subtotal + $ongkir;

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h2 class="fw-bold mb-4">Keranjang Belanja</h2>
        </div>
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

    <?php if (empty($cart_items)): ?>
        <!-- Empty Cart State -->
        <div class="text-center py-5">
            <i class="bi bi-cart-x" style="font-size: 120px; color: #ddd;"></i>
            <h3 class="mt-4 mb-3">Keranjang Belanja Kosong</h3>
            <p class="text-muted mb-4">Yuk, mulai belanja dan temukan produk favorit Anda!</p>
            <a href="../produk/katalog.php" class="btn btn-ngajual btn-lg">
                <i class="bi bi-shop"></i> Mulai Belanja
            </a>
        </div>
    <?php else: ?>
        <!-- Cart Items -->
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-cart3"></i> Produk (<?= $total_items ?>)
                            </h5>
                            <button class="btn btn-sm btn-outline-danger" onclick="clearCart()">
                                <i class="bi bi-trash"></i> Kosongkan Keranjang
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Produk</th>
                                        <th>Harga</th>
                                        <th style="width: 150px;">Jumlah</th>
                                        <th>Subtotal</th>
                                        <th style="width: 80px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart_items as $item): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                                    <i class="bi bi-image fs-3 text-muted"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 fw-bold">
                                                        <a href="../produk/detail.php?id=<?= $item['id_produk'] ?>" class="text-dark text-decoration-none">
                                                            <?= htmlspecialchars($item['nama_produk']) ?>
                                                        </a>
                                                    </h6>
                                                    <small class="text-muted">
                                                        <i class="bi bi-shop"></i> <?= htmlspecialchars($item['nama_toko']) ?>
                                                    </small>
                                                    <br>
                                                    <small class="text-muted">Stok: <?= $item['stok'] ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold">Rp <?= number_format($item['harga'], 0, ',', '.') ?></span>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm" style="width: 120px;">
                                                <button class="btn btn-outline-secondary" type="button" onclick="updateQty(<?= $item['id_isi'] ?>, -1, <?= $item['stok'] ?>)">
                                                    <i class="bi bi-dash"></i>
                                                </button>
                                                <input type="number" class="form-control text-center" id="qty-<?= $item['id_isi'] ?>" value="<?= $item['jumlah'] ?>" min="1" max="<?= $item['stok'] ?>" readonly>
                                                <button class="btn btn-outline-secondary" type="button" onclick="updateQty(<?= $item['id_isi'] ?>, 1, <?= $item['stok'] ?>)">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-primary">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-danger" onclick="removeItem(<?= $item['id_isi'] ?>)" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Continue Shopping Button -->
                <div class="mt-3">
                    <a href="../produk/katalog.php" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> Lanjut Belanja
                    </a>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-receipt"></i> Ringkasan Belanja</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Subtotal (<?= $total_items ?> produk)</span>
                            <span class="fw-bold">Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Ongkos Kirim</span>
                            <span class="fw-bold">Rp <?= number_format($ongkir, 0, ',', '.') ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="h5 fw-bold">Total</span>
                            <span class="h5 fw-bold text-primary">Rp <?= number_format($total, 0, ',', '.') ?></span>
                        </div>
                        
                        <a href="checkout.php" class="btn btn-ngajual btn-lg w-100 mb-3">
                            <i class="bi bi-credit-card"></i> Lanjut ke Pembayaran
                        </a>

                        <div class="alert alert-info mb-0" role="alert">
                            <i class="bi bi-info-circle"></i>
                            <small>Gratis ongkir untuk pembelian di atas Rp 100.000</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function updateQty(id_isi, change, maxStock) {
    const qtyInput = document.getElementById('qty-' + id_isi);
    let currentQty = parseInt(qtyInput.value);
    let newQty = currentQty + change;
    
    if (newQty < 1) newQty = 1;
    if (newQty > maxStock) {
        alert('Stok tidak mencukupi! Maksimal: ' + maxStock);
        return;
    }
    
    // Update via AJAX
    fetch('ubah.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id_isi=${id_isi}&jumlah=${newQty}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Gagal mengubah jumlah');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

function removeItem(id_isi) {
    if (!confirm('Apakah Anda yakin ingin menghapus produk ini dari keranjang?')) {
        return;
    }
    
    fetch('hapus.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id_isi=${id_isi}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Gagal menghapus produk');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

function clearCart() {
    if (!confirm('Apakah Anda yakin ingin mengosongkan keranjang?')) {
        return;
    }
    
    window.location.href = 'hapus.php?clear=all';
}
</script>

<?php include '../includes/footer.php'; ?>
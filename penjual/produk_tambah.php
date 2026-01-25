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

// Get all categories
$stmt_kat = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori");
$kategoris = $stmt_kat->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_produk = trim($_POST['nama_produk']);
    $id_kategori = (int)$_POST['id_kategori'];
    $deskripsi = trim($_POST['deskripsi']);
    $harga = (float)$_POST['harga'];
    $stok = (int)$_POST['stok'];
    $status_produk = $_POST['status_produk'];

    // Validasi
    if (empty($nama_produk) || empty($id_kategori) || $harga <= 0 || $stok < 0) {
        $_SESSION['error'] = 'Semua field wajib diisi dengan benar!';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO produk (id_penjual, id_kategori, nama_produk, deskripsi, harga, stok, status_produk, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$penjual['id_penjual'], $id_kategori, $nama_produk, $deskripsi, $harga, $stok, $status_produk]);
            
            $_SESSION['success'] = 'Produk berhasil ditambahkan!';
            header('Location: produk_list.php');
            exit;
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Tambah Produk Baru</h4>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($_SESSION['error']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="nama_produk" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_produk" name="nama_produk" required>
                        </div>

                        <div class="mb-3">
                            <label for="id_kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select" id="id_kategori" name="id_kategori" required>
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($kategoris as $kat): ?>
                                <option value="<?= $kat['id_kategori'] ?>"><?= htmlspecialchars($kat['nama_kategori']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi Produk</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" placeholder="Jelaskan detail produk Anda..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="harga" class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="harga" name="harga" min="0" step="0.01" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="stok" class="form-label">Stok <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="stok" name="stok" min="0" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status_produk" class="form-label">Status Produk</label>
                            <select class="form-select" id="status_produk" name="status_produk">
                                <option value="aktif" selected>Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                            <small class="text-muted">Produk nonaktif tidak akan ditampilkan di katalog</small>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="produk_list.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-ngajual">
                                <i class="bi bi-save"></i> Simpan Produk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
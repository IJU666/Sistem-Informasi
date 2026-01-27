<?php
require_once '../config/database.php';
require_once '../config/session.php';

if (!isLoggedIn() || !isPenjual()) {
    header('Location: ../auth/login.php');
    exit;
}

// Get penjual data with user info
$stmt = $pdo->prepare("
    SELECT pj.*, u.nama, u.email, u.no_hp, u.alamat 
    FROM penjual pj 
    JOIN pengguna u ON pj.id_pengguna = u.id_pengguna
    WHERE pj.id_pengguna = ?
");
$stmt->execute([getUserId()]);
$penjual = $stmt->fetch();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $no_hp = trim($_POST['no_hp']);
    $alamat = trim($_POST['alamat']);
    $nama_toko = trim($_POST['nama_toko']);
    $deskripsi_toko = trim($_POST['deskripsi_toko']);
    $password_baru = trim($_POST['password_baru'] ?? '');
    $konfirmasi_password = trim($_POST['konfirmasi_password'] ?? '');

    try {
        // Update data pengguna
        if (!empty($password_baru)) {
            if ($password_baru !== $konfirmasi_password) {
                throw new Exception('Password baru dan konfirmasi password tidak sama!');
            }
            if (strlen($password_baru) < 6) {
                throw new Exception('Password minimal 6 karakter!');
            }
            
            // Update dengan password baru
            $stmt = $pdo->prepare("
                UPDATE pengguna 
                SET nama = ?, email = ?, no_hp = ?, alamat = ?, password = ?
                WHERE id_pengguna = ?
            ");
            $stmt->execute([$nama, $email, $no_hp, $alamat, $password_baru, getUserId()]);
        } else {
            // Update tanpa password
            $stmt = $pdo->prepare("
                UPDATE pengguna 
                SET nama = ?, email = ?, no_hp = ?, alamat = ?
                WHERE id_pengguna = ?
            ");
            $stmt->execute([$nama, $email, $no_hp, $alamat, getUserId()]);
        }

        // Update data toko
        $stmt = $pdo->prepare("
            UPDATE penjual 
            SET nama_toko = ?, deskripsi_toko = ?
            WHERE id_pengguna = ?
        ");
        $stmt->execute([$nama_toko, $deskripsi_toko, getUserId()]);

        // Update session
        $_SESSION['nama'] = $nama;
        $_SESSION['success'] = 'Profil berhasil diupdate!';
        header('Location: profil_toko.php');
        exit;

    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

// Get statistik toko
$stmt = $pdo->prepare("SELECT COUNT(*) FROM produk WHERE id_penjual = ?");
$stmt->execute([$penjual['id_penjual']]);
$total_produk = $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT COUNT(DISTINCT ps.id_pesanan) as total
    FROM produk p
    JOIN detail_pesanan dp ON p.id_produk = dp.id_produk
    JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan
    WHERE p.id_penjual = ? AND ps.status_pesanan = 'selesai'
");
$stmt->execute([$penjual['id_penjual']]);
$total_penjualan = $stmt->fetchColumn();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container py-5">
    <h2 class="fw-bold mb-4">Profil Toko</h2>

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

    <div class="row">
        <!-- Sidebar Info -->
        <div class="col-md-4 mb-4">
            <div class="card shadow">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-shop" style="font-size: 80px; color: #243797;"></i>
                    </div>
                    <h4 class="fw-bold"><?= htmlspecialchars($penjual['nama_toko']) ?></h4>
                    <p class="text-muted mb-3"><?= htmlspecialchars($penjual['nama']) ?></p>
                    
                    <div class="badge bg-<?= $penjual['status_verifikasi'] == 'diterima' ? 'success' : 'warning' ?> mb-3">
                        <?= ucfirst($penjual['status_verifikasi']) ?>
                    </div>

                    <hr>

                    <div class="row text-center">
                        <div class="col-6 mb-2">
                            <h5 class="mb-0"><?= $total_produk ?></h5>
                            <small class="text-muted">Produk</small>
                        </div>
                        <div class="col-6 mb-2">
                            <h5 class="mb-0"><?= $total_penjualan ?></h5>
                            <small class="text-muted">Penjualan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Edit Profil -->
        <div class="col-md-8 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-pencil"></i> Edit Profil & Toko</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <h6 class="fw-bold mb-3">Informasi Pemilik</h6>
                        
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($penjual['nama']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($penjual['email']) ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="no_hp" class="form-label">No. HP</label>
                                <input type="tel" class="form-control" id="no_hp" name="no_hp" value="<?= htmlspecialchars($penjual['no_hp']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <input type="text" class="form-control" id="alamat" name="alamat" value="<?= htmlspecialchars($penjual['alamat']) ?>">
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="fw-bold mb-3">Informasi Toko</h6>

                        <div class="mb-3">
                            <label for="nama_toko" class="form-label">Nama Toko</label>
                            <input type="text" class="form-control" id="nama_toko" name="nama_toko" value="<?= htmlspecialchars($penjual['nama_toko']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi_toko" class="form-label">Deskripsi Toko</label>
                            <textarea class="form-control" id="deskripsi_toko" name="deskripsi_toko" rows="3"><?= htmlspecialchars($penjual['deskripsi_toko']) ?></textarea>
                        </div>

                        <hr class="my-4">

                        <h6 class="fw-bold mb-3">Ubah Password (Opsional)</h6>

                        <div class="mb-3">
                            <label for="password_baru" class="form-label">Password Baru</label>
                            <input type="password" class="form-control" id="password_baru" name="password_baru" minlength="6">
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                        </div>

                        <div class="mb-3">
                            <label for="konfirmasi_password" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password">
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="dashboard.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-ngajual">
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


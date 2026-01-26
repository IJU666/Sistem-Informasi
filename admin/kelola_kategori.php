<?php
require_once '../config/database.php';
require_once '../config/session.php';

// Cek apakah admin sudah login
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../auth/login.php');
    exit;
}

$message = '';
$message_type = '';

// Proses Tambah Kategori
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_kategori'])) {
    $nama_kategori = trim($_POST['nama_kategori']);
    $gambar_kategori = null;

    // Proses upload gambar
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $file = $_FILES['gambar'];
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $file['name'];
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed)) {
            if ($file['size'] <= 2000000) { // Max 2MB
                // Buat nama file unik
                $new_filename = 'kategori_' . time() . '_' . uniqid() . '.' . $file_ext;
                $upload_path = '../assets/kategori/' . $new_filename;

                // Buat folder jika belum ada
                if (!file_exists('../assets/kategori/')) {
                    mkdir('../assets/kategori/', 0777, true);
                }

                // Upload file
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    $gambar_kategori = $new_filename;
                } else {
                    $message = 'Gagal upload gambar!';
                    $message_type = 'danger';
                }
            } else {
                $message = 'Ukuran file terlalu besar (max 2MB)!';
                $message_type = 'danger';
            }
        } else {
            $message = 'Format file tidak diizinkan! Gunakan JPG, PNG, atau GIF.';
            $message_type = 'danger';
        }
    }
    // wdhjuww

    // Insert ke database jika tidak ada error
    if ($message_type != 'danger') {
        try {
            $stmt = $pdo->prepare("INSERT INTO kategori (nama_kategori, gambar_kategori) VALUES (?, ?)");
            $stmt->execute([$nama_kategori, $gambar_kategori]);
            $message = 'Kategori berhasil ditambahkan!';
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = 'Gagal menambahkan kategori: ' . $e->getMessage();
            $message_type = 'danger';
        }
    }
}

// Proses Edit Kategori
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_kategori'])) {
    $id_kategori = $_POST['id_kategori'];
    $nama_kategori = trim($_POST['nama_kategori']);


    // Ambil gambar lama
    $stmt = $pdo->prepare("SELECT gambar_kategori FROM kategori WHERE id_kategori = ?");
    $stmt->execute([$id_kategori]);
    $kategori_lama = $stmt->fetch();
    $gambar_kategori = $kategori_lama['gambar_kategori'];

    // Proses upload gambar baru
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $file = $_FILES['gambar'];
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $file['name'];
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed)) {
            if ($file['size'] <= 2000000) {
                $new_filename = 'kategori_' . time() . '_' . uniqid() . '.' . $file_ext;
                $upload_path = '../assets/kategori/' . $new_filename;

                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    // Hapus gambar lama jika ada
                    if ($gambar_kategori && file_exists('../assets/kategori/' . $gambar_kategori)) {
                        unlink('../assets/kategori/' . $gambar_kategori);
                    }
                    $gambar_kategori = $new_filename;
                }
            }
        }
    }

    // Update database
    try {
        $stmt = $pdo->prepare("UPDATE kategori SET nama_kategori = ?, gambar_kategori = ? WHERE id_kategori = ?");
        $stmt->execute([$nama_kategori, $gambar_kategori, $id_kategori]);
        $message = 'Kategori berhasil diupdate!';
        $message_type = 'success';
    } catch (PDOException $e) {
        $message = 'Gagal update kategori: ' . $e->getMessage();
        $message_type = 'danger';
    }
}

// Proses Hapus Kategori
if (isset($_GET['hapus'])) {
    $id_kategori = $_GET['hapus'];

    // Ambil data kategori
    $stmt = $pdo->prepare("SELECT gambar_kategori FROM kategori WHERE id_kategori = ?");
    $stmt->execute([$id_kategori]);
    $kategori = $stmt->fetch();

    // Hapus gambar jika ada
    if ($kategori && $kategori['gambar_kategori']) {
        $file_path = '../assets/kategori/' . $kategori['gambar_kategori'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    // Hapus dari database
    try {
        $stmt = $pdo->prepare("DELETE FROM kategori WHERE id_kategori = ?");
        $stmt->execute([$id_kategori]);
        $message = 'Kategori berhasil dihapus!';
        $message_type = 'success';
    } catch (PDOException $e) {
        $message = 'Gagal menghapus kategori: ' . $e->getMessage();
        $message_type = 'danger';
    }
}

// Ambil semua kategori
$stmt = $pdo->query("SELECT * FROM kategori ORDER BY id_kategori DESC");
$kategoris = $stmt->fetchAll();

// Ambil data untuk edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM kategori WHERE id_kategori = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_data = $stmt->fetch();
}

// Hitung pesanan pending untuk notifikasi
$stmt = $pdo->query("SELECT COUNT(*) FROM pesanan WHERE status_pesanan = 'menunggu'");
$pesanan_pending = $stmt->fetchColumn();

include '../includes/header.php';
?>

<style>
    .preview-image {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="dashboard.php">
            NGAJUAL - Pusat Admin
        </a>
        <div class="d-flex gap-3">
            <span class="badge bg-light text-primary px-3 py-2">admin</span>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-bell"></i> Notifikasi
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"><?= $pesanan_pending ?> Pesanan Pending</a></li>
                </ul>
            </div>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person"></i> Masuk
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="../auth/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 bg-dark text-white p-3" style="min-height: 100vh;">
            <h6 class="text-muted small mb-3">Menu Utama</h6>
            <nav class="nav flex-column">
                <a class="nav-link text-white-50" href="dashboard.php">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a class="nav-link text-white-50 mb-2" href="kelola_pengguna.php">
                    <i class="bi bi-people"></i> Kelola Pengguna
                </a>
                <a class="nav-link text-white-50" href="laporan_global.php">
                    <i class="bi bi-graph-up"></i> Laporan
                </a>
            </nav>
            <h6 class="text-muted small mb-3 mt-4">Alat</h6>
            <nav class="nav flex-column">
                <a class="nav-link text-white bg-secondary rounded" href="kelola_kategori.php">
                    <i class="bi bi-tags"></i> Kategori
                </a>
                <a class="nav-link text-white-50" href="#">
                    <i class="bi bi-gear"></i> Pengaturan
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="col-md-10 p-4" style="background: #f5f5f5;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-tags"></i> Kelola Kategori</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus-circle"></i> Tambah Kategori
                </button>
            </div>

            <!-- Alert Message -->
            <?php if ($message): ?>
                <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
                    <?= $message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Tabel Kategori -->
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Gambar</th>
                                    <th>Nama Kategori</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($kategoris as $kategori): ?>
                                    <tr>
                                        <td><?= $kategori['id_kategori'] ?></td>
                                        <td>
                                            <?php if ($kategori['gambar_kategori']): ?>
                                                <img src="../assets/kategori/<?= htmlspecialchars($kategori['gambar_kategori']) ?>"
                                                    alt="<?= htmlspecialchars($kategori['nama_kategori']) ?>"
                                                    class="preview-image">
                                            <?php else: ?>
                                                <div
                                                    class="preview-image bg-secondary d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-image text-white fs-3"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><strong><?= htmlspecialchars($kategori['nama_kategori']) ?></strong></td>
                                        <td>
                                            <a href="?edit=<?= $kategori['id_kategori'] ?>" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <a href="?hapus=<?= $kategori['id_kategori'] ?>" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                                <i class="bi bi-trash"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Kategori -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kategori Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori *</label>
                        <input type="text" name="nama_kategori" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gambar Kategori</label>
                        <input type="file" name="gambar" class="form-control" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG, GIF. Max 2MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah_kategori" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Kategori -->
<?php if ($edit_data): ?>
    <div class="modal fade show" id="modalEdit" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kategori</h5>
                    <a href="kelola_kategori.php" class="btn-close"></a>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_kategori" value="<?= $edit_data['id_kategori'] ?>">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Kategori *</label>
                            <input type="text" name="nama_kategori" class="form-control"
                                value="<?= htmlspecialchars($edit_data['nama_kategori']) ?>" required>
                        </div>

                        <?php if ($edit_data['gambar_kategori']): ?>
                            <div class="mb-3">
                                <label class="form-label">Gambar Saat Ini</label><br>
                                <img src="../assets/kategori/<?= htmlspecialchars($edit_data['gambar_kategori']) ?>"
                                    alt="Current" class="preview-image mb-2">
                            </div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label">Ganti Gambar (Opsional)</label>
                            <input type="file" name="gambar" class="form-control" accept="image/*">
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah gambar</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="kelola_kategori.php" class="btn btn-secondary">Batal</a>
                        <button type="submit" name="edit_kategori" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
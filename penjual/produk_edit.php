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

// Get product ID
$id_produk = $_GET['id'] ?? 0;

// Get product data
$stmt = $pdo->prepare("
    SELECT * FROM produk 
    WHERE id_produk = ? AND id_penjual = ?
");
$stmt->execute([$id_produk, $penjual['id_penjual']]);
$produk = $stmt->fetch();

if (!$produk) {
    $_SESSION['error'] = 'Produk tidak ditemukan!';
    header('Location: produk_list.php');
    exit;
}

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
    
    // Handle file upload
    $foto_produk = $produk['foto_produk']; // Keep old photo by default
    
    if (isset($_FILES['foto_produk']) && $_FILES['foto_produk']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['foto_produk'];
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        // Validate file type
        if (!in_array($file['type'], $allowed_types)) {
            $_SESSION['error'] = 'Tipe file tidak valid! Hanya JPG, PNG, GIF yang diperbolehkan.';
        }
        // Validate file size
        elseif ($file['size'] > $max_size) {
            $_SESSION['error'] = 'Ukuran file terlalu besar! Maksimal 2MB.';
        }
        else {
            // Create upload directory if not exists
            $upload_dir = '../assets/img/uploads/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_foto = 'produk_' . time() . '_' . uniqid() . '.' . $extension;
            $upload_path = $upload_dir . $new_foto;
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                // Delete old photo if exists
                if ($produk['foto_produk'] && file_exists($upload_dir . $produk['foto_produk'])) {
                    unlink($upload_dir . $produk['foto_produk']);
                }
                $foto_produk = $new_foto;
            } else {
                $_SESSION['error'] = 'Gagal mengupload foto!';
            }
        }
    }

    // Validasi
    if (empty($nama_produk) || empty($id_kategori) || $harga <= 0 || $stok < 0) {
        $_SESSION['error'] = 'Semua field wajib diisi dengan benar!';
    } 
    elseif (!isset($_SESSION['error'])) {
        try {
            $stmt = $pdo->prepare("
                UPDATE produk 
                SET nama_produk = ?, id_kategori = ?, deskripsi = ?, harga = ?, stok = ?, foto_produk = ?, status_produk = ?
                WHERE id_produk = ? AND id_penjual = ?
            ");
            $stmt->execute([$nama_produk, $id_kategori, $deskripsi, $harga, $stok, $foto_produk, $status_produk, $id_produk, $penjual['id_penjual']]);
            
            $_SESSION['success'] = 'Produk berhasil diupdate!';
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
                    <h4 class="mb-0"><i class="bi bi-pencil"></i> Edit Produk</h4>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($_SESSION['error']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="foto_produk" class="form-label">Foto Produk</label>
                            
                            <?php if ($produk['foto_produk']): ?>
                            <div class="mb-2">
                                <img src="../assets/img/uploads/<?= htmlspecialchars($produk['foto_produk']) ?>" 
                                     alt="Foto Produk" 
                                     class="img-thumbnail" 
                                     style="max-width: 200px; max-height: 200px;"
                                     id="currentPhoto">
                                <p class="small text-muted mt-1">Foto saat ini</p>
                            </div>
                            <?php endif; ?>
                            
                            <input type="file" class="form-control" id="foto_produk" name="foto_produk" accept="image/*" onchange="previewImage(this)">
                            <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 2MB. Biarkan kosong jika tidak ingin mengubah foto.</small>
                            
                            <!-- Preview New Image -->
                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <p class="small fw-bold">Preview foto baru:</p>
                                <img id="preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 300px; max-height: 300px;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nama_produk" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_produk" name="nama_produk" value="<?= htmlspecialchars($produk['nama_produk']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="id_kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select" id="id_kategori" name="id_kategori" required>
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($kategoris as $kat): ?>
                                <option value="<?= $kat['id_kategori'] ?>" <?= $kat['id_kategori'] == $produk['id_kategori'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($kat['nama_kategori']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi Produk</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4"><?= htmlspecialchars($produk['deskripsi']) ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="harga" class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="harga" name="harga" value="<?= $produk['harga'] ?>" min="0" step="0.01" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="stok" class="form-label">Stok <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="stok" name="stok" value="<?= $produk['stok'] ?>" min="0" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status_produk" class="form-label">Status Produk</label>
                            <select class="form-select" id="status_produk" name="status_produk">
                                <option value="aktif" <?= $produk['status_produk'] == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="nonaktif" <?= $produk['status_produk'] == 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> <strong>Info:</strong> Produk ini dibuat pada <?= date('d/m/Y H:i', strtotime($produk['created_at'])) ?>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="produk_list.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-ngajual">
                                <i class="bi bi-save"></i> Update Produk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('preview');
    const previewContainer = document.getElementById('imagePreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        previewContainer.style.display = 'none';
    }
}
</script>


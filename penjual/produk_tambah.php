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
    
    // Handle multiple file uploads
    $foto_produk = null;
    $uploaded_files = [];
    
    if (isset($_FILES['foto_produk'])) {
        $files = $_FILES['foto_produk'];
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        $max_files = 3;
        
        // Create upload directory if not exists
        $upload_dir = '../assets/img/uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Process each file
        $file_count = count($files['name']);
        for ($i = 0; $i < min($file_count, $max_files); $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                // Validate file type
                if (!in_array($files['type'][$i], $allowed_types)) {
                    $_SESSION['error'] = 'Tipe file tidak valid! Hanya JPG, PNG, GIF yang diperbolehkan.';
                    continue;
                }
                
                // Validate file size
                if ($files['size'][$i] > $max_size) {
                    $_SESSION['error'] = 'Ukuran file terlalu besar! Maksimal 2MB.';
                    continue;
                }
                
                // Generate unique filename
                $extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                $filename = 'produk_' . time() . '_' . uniqid() . '_' . $i . '.' . $extension;
                $upload_path = $upload_dir . $filename;
                
                // Move uploaded file
                if (move_uploaded_file($files['tmp_name'][$i], $upload_path)) {
                    $uploaded_files[] = $filename;
                }
            }
        }
        
        // Use first uploaded file as main photo
        if (!empty($uploaded_files)) {
            $foto_produk = $uploaded_files[0];
        }
    }

    // Validasi
    if (empty($nama_produk) || empty($id_kategori) || $harga <= 0 || $stok < 0) {
        $_SESSION['error'] = 'Semua field wajib diisi dengan benar!';
    } 
    elseif (!isset($_SESSION['error'])) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO produk (id_penjual, id_kategori, nama_produk, deskripsi, harga, stok, foto_produk, status_produk, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$penjual['id_penjual'], $id_kategori, $nama_produk, $deskripsi, $harga, $stok, $foto_produk, $status_produk]);
            
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

<style>
.upload-area {
    border: 2px dashed #ccc;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    background: #f9f9f9;
    cursor: pointer;
    transition: all 0.3s;
}

.upload-area:hover {
    border-color: #243796;
    background: #f0f4ff;
}

.preview-container {
    display: flex;
    gap: 15px;
    margin-top: 15px;
    flex-wrap: wrap;
}

.preview-item {
    position: relative;
    width: 120px;
    height: 120px;
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid #ddd;
}

.preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.preview-item .remove-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(255, 0, 0, 0.8);
    color: white;
    border: none;
    border-radius: 50%;
    width: 25px;
    height: 25px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

.preview-item .photo-number {
    position: absolute;
    bottom: 5px;
    left: 5px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 12px;
}

.form-section {
    background: white;
    border-radius: 12px;
    padding: 30px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.form-section h5 {
    color: #243796;
    font-weight: 600;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e9ecef;
}
</style>

<div class="container py-5">
    <div class="row">
        <!-- Left Side - Upload Gambar -->
        <div class="col-md-4">
            <div class="form-section">
                <h5><i class="bi bi-images"></i> Unggah Gambar</h5>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data" id="productForm">
                    <div class="upload-area" onclick="document.getElementById('foto_produk').click()">
                        <i class="bi bi-cloud-upload" style="font-size: 48px; color: #ccc;"></i>
                        <p class="mb-0 mt-2">Klik untuk upload foto</p>
                        <small class="text-muted">Maksimal 3 foto (JPG, PNG, GIF)</small>
                    </div>
                    
                    <input type="file" class="d-none" id="foto_produk" name="foto_produk[]" accept="image/*" multiple onchange="previewImages(this)">
                    
                    <!-- Preview Container -->
                    <div id="previewContainer" class="preview-container"></div>

                    <div class="mt-4">
                        <h6>Kategori</h6>
                        <div class="mb-3">
                            <label for="id_kategori" class="form-label">Kategori Produk <span class="text-danger">*</span></label>
                            <select class="form-select" id="id_kategori" name="id_kategori" required>
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($kategoris as $kat): ?>
                                <option value="<?= $kat['id_kategori'] ?>"><?= htmlspecialchars($kat['nama_kategori']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Right Side - Informasi Produk -->
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0"><i class="bi bi-bag-plus"></i> Tambah Produk</h4>
                <button type="submit" class="btn btn-primary" style="background-color: #243796; border: none;">
                    <i class="bi bi-save"></i> Simpan
                </button>
            </div>

            <div class="form-section">
                <h5>Informasi Umum</h5>
                
                <div class="mb-3">
                    <label for="nama_produk" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_produk" name="nama_produk" placeholder="Contoh: Pisang Asli Jawa" required>
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi Produk</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5" placeholder="Jelaskan detail produk Anda..."></textarea>
                </div>
            </div>

            <div class="form-section">
                <h5>Harga dan Stok</h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="harga" class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="harga" name="harga" min="0" step="1" placeholder="10.000" required>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="stok" class="form-label">Stok <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="stok" name="stok" min="0" placeholder="77" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="status_produk" class="form-label">Status Produk</label>
                    <select class="form-select" id="status_produk" name="status_produk">
                        <option value="aktif" selected>Aktif (Tampil di katalog)</option>
                        <option value="nonaktif">Nonaktif (Tersembunyi)</option>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="produk_list.php" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary" style="background-color: #243796; border: none;">
                    <i class="bi bi-save"></i> Simpan Produk
                </button>
            </div>
            </form>
        </div>
    </div>
</div>

<script>
let selectedFiles = [];

function previewImages(input) {
    const previewContainer = document.getElementById('previewContainer');
    const files = input.files;
    const maxFiles = 3;
    
    // Clear previous previews if new files selected
    if (selectedFiles.length === 0) {
        previewContainer.innerHTML = '';
    }
    
    // Limit to 3 files
    const filesToProcess = Math.min(files.length, maxFiles);
    
    for (let i = 0; i < filesToProcess; i++) {
        if (selectedFiles.length >= maxFiles) break;
        
        const file = files[i];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const index = selectedFiles.length;
            selectedFiles.push(file);
            
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item';
            previewItem.innerHTML = `
                <img src="${e.target.result}" alt="Preview">
                <button type="button" class="remove-btn" onclick="removeImage(${index})">×</button>
                <span class="photo-number">Foto ${index + 1}</span>
            `;
            
            previewContainer.appendChild(previewItem);
        };
        
        reader.readAsDataURL(file);
    }
    
    // Reset input for re-selection
    input.value = '';
}

function removeImage(index) {
    selectedFiles.splice(index, 1);
    updatePreviews();
}

function updatePreviews() {
    const previewContainer = document.getElementById('previewContainer');
    previewContainer.innerHTML = '';
    
    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item';
            previewItem.innerHTML = `
                <img src="${e.target.result}" alt="Preview">
                <button type="button" class="remove-btn" onclick="removeImage(${index})">×</button>
                <span class="photo-number">Foto ${index + 1}</span>
            `;
            previewContainer.appendChild(previewItem);
        };
        reader.readAsDataURL(file);
    });
}

// Update form submission to use selected files
document.getElementById('productForm').addEventListener('submit', function(e) {
    const input = document.getElementById('foto_produk');
    const dataTransfer = new DataTransfer();
    
    selectedFiles.forEach(file => {
        dataTransfer.items.add(file);
    });
    
    input.files = dataTransfer.files;
});
</script>

<?php include '../includes/footer.php'; ?>
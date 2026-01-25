<?php
require_once '../config/session.php';

// Jika sudah login, redirect
if (isLoggedIn()) {
    header('Location: ../index.php');
    exit;
}

include '../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4 fw-bold ngajual">Daftar Akun NGAJUAL</h2>
                    
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($_SESSION['error']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                    
                    <form action="process_register.php" method="POST">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <small class="text-muted">Gunakan email @umbandung.ac.id untuk mahasiswa</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="no_hp" class="form-label">No. HP</label>
                            <input type="tel" class="form-control" id="no_hp" name="no_hp" placeholder="08xxxxxxxxxx">
                        </div>
                        
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="2"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="6">
                            <small class="text-muted">Minimal 6 karakter</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="role" class="form-label">Daftar Sebagai</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">Pilih Role</option>
                                <option value="pembeli">Pembeli</option>
                                <option value="penjual">Penjual</option>
                            </select>
                        </div>
                        
                        <div id="toko-fields" style="display: none;">
                            <div class="mb-3">
                                <label for="nama_toko" class="form-label">Nama Toko</label>
                                <input type="text" class="form-control" id="nama_toko" name="nama_toko">
                            </div>
                            
                            <div class="mb-3">
                                <label for="deskripsi_toko" class="form-label">Deskripsi Toko</label>
                                <textarea class="form-control" id="deskripsi_toko" name="deskripsi_toko" rows="2"></textarea>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="agree" required>
                            <label class="form-check-label" for="agree">
                                Saya setuju dengan <a href="#">syarat dan ketentuan</a>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-ngajual w-100 py-2 mb-3">Daftar</button>
                        
                        <p class="text-center mb-0">
                            Sudah punya akun? <a href="login.php" class="text-decoration-none">Masuk Sekarang</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Show/hide toko fields based on role selection
document.getElementById('role').addEventListener('change', function() {
    const tokoFields = document.getElementById('toko-fields');
    const namaToko = document.getElementById('nama_toko');
    const deskripsiToko = document.getElementById('deskripsi_toko');
    
    if (this.value === 'penjual') {
        tokoFields.style.display = 'block';
        namaToko.required = true;
    } else {
        tokoFields.style.display = 'none';
        namaToko.required = false;
        namaToko.value = '';
        deskripsiToko.value = '';
    }
});
</script>

<?php include '../includes/footer.php'; ?>
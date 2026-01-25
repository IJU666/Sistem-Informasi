<?php
require_once '../config/session.php';

// Jika sudah login, redirect
if (isLoggedIn()) {
    header('Location: ../index.php');
    exit;
}

include '../includes/header.php';
?>

    <div class="container ">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-6 d-none d-lg-block bg-register-image">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Daftar Akun</h1>
                            </div>
                            <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($_SESSION['error']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                            <form class="user" action="process_register.php" method="POST">
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
                                <a href="login.html" style="background-color: #243796;" class="btn col-12 text-light">
                                    Daftar Akun
                                </a>
                                <hr>
                                <div class="col-12 justify-content-center">
                                    <center>
                                <a href="index.html" class="btn btn-google btn-user btn-block">
                                    <i class="fab fa-google fa-fw"></i> Register with 
                                </a>
                                <a href="index.html" class="btn btn-facebook btn-user btn-block">
                                    <i class="fab fa-facebook-f fa-fw"></i> Register with Facebook
                                </a>
                                </center>
                                </div>
                            </form>
                            <hr>
                            <div class="text-center">
                             
                            </div>
                            <div class="text-center">
                                <a class="small" href="login.html">Already have an account? Login!</a>
                            </div>
                        </div></div>
                    <div class="col-lg-6 position-relative overflow-hidden">
                        <img src="../assets/login.png" alt="" srcset="" class=" end-0 position-absolute" style="object-fit: cover; z-index: 0;" width="">
                        <div class="p-5 position-relative align-items-center justify-content-center d-flex h-100" style="z-index: 1;">
                            <div class=" text-light">
                                <p class="fs-1" style="margin-bottom: -10px;">Hallo,</p>
                                <p class="fs-1 fw-bold">Selamat Datang!</p>
                               <p style="margin-bottom: -5px;"><small>Masuk untuk melanjutkan berbelanja.</small> </p>
                               <p><small>Nikmati penawaran menarik lainnya di <b class="text-warning">NGAJUAL!</b></small> </p>
                            </div>
                                
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

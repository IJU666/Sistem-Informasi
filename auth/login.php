<?php
require_once '../config/session.php';

// Jika sudah login, redirect ke dashboard sesuai role
if (isLoggedIn()) {
    if (isAdmin()) {
        header('Location: ../admin/dashboard.php');
    } elseif (isPenjual()) {
        header('Location: ../penjual/dashboard.php');
    } else {
        header('Location: ../pembeli/dashboard.php');
    }
    exit;
}

include '../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4 fw-bold ngajual">Masuk ke NGAJUAL</h2>
                    
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($_SESSION['error']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($_SESSION['success']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>
                    
                    <form action="process_login.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Ingat Saya</label>
                        </div>
                        
                        <button type="submit" class="btn btn-ngajual w-100 py-2 mb-3">Masuk</button>
                        
                        <p class="text-center mb-0">
                            Belum punya akun? <a href="register.php" class="text-decoration-none">Daftar Sekarang</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
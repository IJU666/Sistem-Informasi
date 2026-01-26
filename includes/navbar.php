<?php
// includes/navbar.php
?>
<!-- Top Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand text-secondary fs-6" href="auth/register.php">Daftar Akun</a>
        <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTop"
            aria-controls="navbarTop" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarTop">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="#tentang">Tentang Kami</a></li>
                <li class="nav-item"><a class="nav-link" href="../auth/register.php">Ayo Berjualan</a></li>
                <li class="nav-item"><a class="nav-link" href="#layanan">Layanan Pengguna</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand fw-bolder ngajual fs-4" href="../index.php">NGAJUAL</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain"
            aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <!-- Search + Cart -->
            <div class="d-flex align-items-center gap-3 flex-grow-1 mx-lg-5 my-3 my-lg-0">
                <form action="produk/pencarian.php" method="GET" class="d-flex w-100">
                    <input class="form-control" type="search" name="q" placeholder="Cari Produk">
                    <button class="btn btn-outline-primary ms-2" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>

            <div class="d-flex align-items-center gap-3">
                <?php if (isLoggedIn()): ?>
                    <div class="dropdown">
                        <a href="#" class="text-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-person"></i> <?= htmlspecialchars(getUserName()) ?>
                        </a>
                        <ul class="dropdown-menu">
                            <?php if (isAdmin()): ?>
                                <li><a class="dropdown-item" href="../admin/dashboard.php">Dashboard Admin</a></li>
                            <?php elseif (isPenjual()): ?>
                                <li><a class="dropdown-item" href="../penjual/dashboard.php">Dashboard Penjual</a></li>
                            <?php elseif (isPembeli()): ?>
                                <li><a class="dropdown-item" href="../pembeli/dashboard.php">Riwayat Pemesanan</a></li>
                            <?php endif; ?>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="../auth/logout.php">Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="auth/login.php" class="text-dark text-decoration-none">
                        <i class="bi bi-person"></i> Masuk
                    </a>
                <?php endif; ?>

                <a href="../keranjang/index.php" class="text-dark position-relative">
                    <i class="bi bi-cart3 fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?php
                        if (isLoggedIn()) {
                            require_once __DIR__ . '/../config/database.php';
                            $stmt = $pdo->prepare("
                                SELECT COUNT(*) as total 
                                FROM keranjang k
                                JOIN isi_keranjang ik ON k.id_keranjang = ik.id_keranjang
                                WHERE k.id_pengguna = ?
                            ");
                            $stmt->execute([getUserId()]);
                            echo $stmt->fetchColumn();
                        } else {
                            echo '0';
                        }
                        ?>
                    </span>
                </a>
                <a href="#wishlist" class="text-dark">
                    <i class="bi bi-heart fs-5"></i>
                </a>
            </div>
        </div>
    </div>
</nav>
<?php
require_once '../config/database.php';
require_once '../config/session.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../auth/login.php');
    exit;
}

// Handle Delete User
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        // Check if user is admin
        $stmt = $pdo->prepare("SELECT role FROM pengguna WHERE id_pengguna = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        
        if ($user && $user['role'] !== 'admin') {
            $stmt = $pdo->prepare("DELETE FROM pengguna WHERE id_pengguna = ?");
            $stmt->execute([$id]);
            $_SESSION['success'] = 'User berhasil dihapus!';
        } else {
            $_SESSION['error'] = 'Admin tidak bisa dihapus!';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Gagal menghapus user: ' . $e->getMessage();
    }
    header('Location: kelola_pengguna.php');
    exit;
}

// Handle Update Status
if (isset($_GET['toggle_status'])) {
    $id = (int)$_GET['toggle_status'];
    try {
        $stmt = $pdo->prepare("UPDATE pengguna SET status_akun = IF(status_akun = 'aktif', 'nonaktif', 'aktif') WHERE id_pengguna = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = 'Status user berhasil diubah!';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Gagal mengubah status: ' . $e->getMessage();
    }
    header('Location: kelola_pengguna.php');
    exit;
}

// Filter
$role_filter = $_GET['role'] ?? '';
$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$query = "SELECT * FROM pengguna WHERE 1=1";
$params = [];

if ($role_filter) {
    $query .= " AND role = ?";
    $params[] = $role_filter;
}

if ($status_filter) {
    $query .= " AND status_akun = ?";
    $params[] = $status_filter;
}

if ($search) {
    $query .= " AND (nama LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll();

// Statistics
$stmt = $pdo->query("SELECT COUNT(*) FROM pengguna WHERE role = 'admin'");
$total_admin = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM pengguna WHERE role = 'penjual'");
$total_penjual = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM pengguna WHERE role = 'pembeli'");
$total_pembeli = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM pengguna WHERE status_akun = 'aktif'");
$total_aktif = $stmt->fetchColumn();

include '../includes/header.php';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="dashboard.php">
            NGAJUAL - Pusat Admin
        </a>
        <div class="d-flex gap-3">
            <span class="badge bg-light text-primary px-3 py-2">admin</span>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person"></i> <?= getUserName() ?>
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
                <a class="nav-link text-white bg-secondary rounded mb-2" href="kelola_pengguna.php">
                    <i class="bi bi-people"></i> Kelola Pengguna
                </a>
                <a class="nav-link text-white-50" href="laporan_global.php">
                    <i class="bi bi-graph-up"></i> Laporan
                </a>
            </nav>
            <h6 class="text-muted small mb-3 mt-4">Alat</h6>
            <nav class="nav flex-column">
                <a class="nav-link text-white-50" href="kelola_kategori.php">
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
                <div>
                    <h2 class="fw-bold mb-1">Kelola Pengguna</h2>
                    <p class="text-muted mb-0">Manajemen semua pengguna sistem</p>
                </div>
                <a href="tambah_pengguna.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Pengguna
                </a>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small class="text-muted">Total Admin</small>
                                    <h3 class="mb-0"><?= number_format($total_admin) ?></h3>
                                </div>
                                <div class="bg-danger bg-opacity-10 text-danger rounded p-3">
                                    <i class="bi bi-shield-check fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small class="text-muted">Total Penjual</small>
                                    <h3 class="mb-0"><?= number_format($total_penjual) ?></h3>
                                </div>
                                <div class="bg-warning bg-opacity-10 text-warning rounded p-3">
                                    <i class="bi bi-shop fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small class="text-muted">Total Pembeli</small>
                                    <h3 class="mb-0"><?= number_format($total_pembeli) ?></h3>
                                </div>
                                <div class="bg-primary bg-opacity-10 text-primary rounded p-3">
                                    <i class="bi bi-person fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small class="text-muted">User Aktif</small>
                                    <h3 class="mb-0"><?= number_format($total_aktif) ?></h3>
                                </div>
                                <div class="bg-success bg-opacity-10 text-success rounded p-3">
                                    <i class="bi bi-check-circle fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter & Search -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <form method="GET" action="" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small">Filter Role</label>
                            <select name="role" class="form-select">
                                <option value="">Semua Role</option>
                                <option value="admin" <?= $role_filter == 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="penjual" <?= $role_filter == 'penjual' ? 'selected' : '' ?>>Penjual</option>
                                <option value="pembeli" <?= $role_filter == 'pembeli' ? 'selected' : '' ?>>Pembeli</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Filter Status</label>
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="aktif" <?= $status_filter == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="nonaktif" <?= $status_filter == 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Cari User</label>
                            <input type="text" name="search" class="form-control" placeholder="Nama atau Email" value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Daftar Pengguna (<?= count($users) ?>)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>No. HP</th>
                                    <th>Status</th>
                                    <th>Terdaftar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-1"></i>
                                        <p class="mb-0">Tidak ada user ditemukan</p>
                                    </td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= $user['id_pengguna'] ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($user['nama']) ?></strong>
                                        </td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td>
                                            <?php
                                            $badge_color = [
                                                'admin' => 'danger',
                                                'penjual' => 'warning',
                                                'pembeli' => 'primary'
                                            ];
                                            $color = $badge_color[$user['role']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $color ?>"><?= ucfirst($user['role']) ?></span>
                                        </td>
                                        <td><?= htmlspecialchars($user['no_hp'] ?? '-') ?></td>
                                        <td>
                                            <span class="badge bg-<?= $user['status_akun'] == 'aktif' ? 'success' : 'secondary' ?>">
                                                <?= ucfirst($user['status_akun']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="?toggle_status=<?= $user['id_pengguna'] ?>" 
                                                   class="btn btn-outline-<?= $user['status_akun'] == 'aktif' ? 'warning' : 'success' ?>" 
                                                   title="Toggle Status"
                                                   onclick="return confirm('Ubah status user ini?')">
                                                    <i class="bi bi-<?= $user['status_akun'] == 'aktif' ? 'x-circle' : 'check-circle' ?>"></i>
                                                </a>
                                                <?php if ($user['role'] !== 'admin'): ?>
                                                <a href="?delete=<?= $user['id_pengguna'] ?>" 
                                                   class="btn btn-outline-danger" 
                                                   title="Hapus"
                                                   onclick="return confirm('Yakin ingin menghapus user ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
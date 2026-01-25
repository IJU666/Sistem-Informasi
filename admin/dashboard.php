<?php
require_once '../config/database.php';
require_once '../config/session.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../auth/login.php');
    exit;
}

// Total User
$stmt = $pdo->query("SELECT COUNT(*) as total FROM pengguna");
$total_user = $stmt->fetchColumn();

// Total Order
$stmt = $pdo->query("SELECT COUNT(*) as total FROM pesanan");
$total_order = $stmt->fetchColumn();

// Total Sales
$stmt = $pdo->query("SELECT COALESCE(SUM(total), 0) as total FROM pesanan WHERE status_pesanan = 'selesai'");
$total_sales = $stmt->fetchColumn();

// Total Pending
$stmt = $pdo->query("SELECT COUNT(*) as total FROM pesanan WHERE status_pesanan = 'menunggu'");
$total_pending = $stmt->fetchColumn();

// Total Produk
$stmt = $pdo->query("SELECT COUNT(*) as total FROM produk WHERE status_produk = 'aktif'");
$total_produk = $stmt->fetchColumn();

// Total Penjual
$stmt = $pdo->query("SELECT COUNT(*) as total FROM penjual WHERE status_verifikasi = 'diterima'");
$total_penjual = $stmt->fetchColumn();

include '../includes/header.php';
?>

<style>
.admin-sidebar {
    min-height: 100vh;
    background: #2c3e50;
    color: white;
}
.admin-sidebar .nav-link {
    color: rgba(255,255,255,0.8);
    padding: 12px 20px;
    margin: 5px 10px;
    border-radius: 8px;
    transition: all 0.3s;
}
.admin-sidebar .nav-link:hover,
.admin-sidebar .nav-link.active {
    background: #34495e;
    color: white;
}
.stat-card {
    border-radius: 15px;
    border: none;
    transition: transform 0.3s;
}
.stat-card:hover {
    transform: translateY(-5px);
}
.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}
.icon-blue { background: #e3f2fd; color: #2196F3; }
.icon-yellow { background: #fff8e1; color: #FFC107; }
.icon-green { background: #e8f5e9; color: #4CAF50; }
.icon-pink { background: #fce4ec; color: #E91E63; }
.trend-up { color: #4CAF50; }
.trend-down { color: #f44336; }
</style>

<!-- Admin Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark" style="background: #243797;">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="dashboard.php">
            <i class="bi bi-speedometer2"></i> NGAJUAL - Pusat Admin
        </a>
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-primary">admin</button>
            <div class="dropdown">
                <button class="btn btn-link text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-bell"></i> Notifikasi
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#">5 Pesanan Baru</a></li>
                    <li><a class="dropdown-item" href="#">2 User Baru</a></li>
                </ul>
            </div>
            <div class="dropdown">
                <button class="btn btn-link text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle"></i> Masuk
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#">Profil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="../auth/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 p-0 admin-sidebar">
            <div class="p-3">
                <h6 class="text-white-50 small mb-3">Menu Utama</h6>
                <nav class="nav flex-column">
                    <a class="nav-link active" href="dashboard.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a class="nav-link" href="kelola_pengguna.php">
                        <i class="bi bi-people"></i> Pesanan
                    </a>
                    <a class="nav-link" href="#">
                        <i class="bi bi-truck"></i> Obrolan
                    </a>
                </nav>

                <h6 class="text-white-50 small mb-3 mt-4">Alat</h6>
                <nav class="nav flex-column">
                    <a class="nav-link" href="kelola_produk.php">
                        <i class="bi bi-box-seam"></i> Produk
                    </a>
                    <a class="nav-link" href="#">
                        <i class="bi bi-bar-chart"></i> Analisis
                    </a>
                    <a class="nav-link" href="#">
                        <i class="bi bi-credit-card"></i> Pembayaran
                    </a>
                </nav>

                <h6 class="text-white-50 small mb-3 mt-4">Lainnya</h6>
                <nav class="nav flex-column">
                    <a class="nav-link" href="#">
                        <i class="bi bi-gear"></i> Pengaturan
                    </a>
                    <a class="nav-link" href="#">
                        <i class="bi bi-question-circle"></i> Bantuan
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-10 p-4" style="background: #f5f5f5;">
            <!-- Search Bar -->
            <div class="mb-4">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" placeholder="Search or type a command">
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card stat-card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1 small">Total User</p>
                                    <h3 class="fw-bold mb-2"><?= number_format($total_user) ?></h3>
                                    <p class="small mb-0 trend-up">
                                        <i class="bi bi-arrow-up"></i> 8.5% Up from yesterday
                                    </p>
                                </div>
                                <div class="stat-icon icon-blue">
                                    <i class="bi bi-people"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card stat-card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1 small">Total Order</p>
                                    <h3 class="fw-bold mb-2"><?= number_format($total_order) ?></h3>
                                    <p class="small mb-0 trend-up">
                                        <i class="bi bi-arrow-up"></i> 1.3% Up from past week
                                    </p>
                                </div>
                                <div class="stat-icon icon-yellow">
                                    <i class="bi bi-box-seam"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card stat-card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1 small">Total Sales</p>
                                    <h3 class="fw-bold mb-2">Rp<?= number_format($total_sales/1000) ?>K</h3>
                                    <p class="small mb-0 trend-down">
                                        <i class="bi bi-arrow-down"></i> 4.3% Down from yesterday
                                    </p>
                                </div>
                                <div class="stat-icon icon-green">
                                    <i class="bi bi-graph-up"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card stat-card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1 small">Total Pending</p>
                                    <h3 class="fw-bold mb-2"><?= number_format($total_pending) ?></h3>
                                    <p class="small mb-0 trend-up">
                                        <i class="bi bi-arrow-up"></i> 1.8% Up from yesterday
                                    </p>
                                </div>
                                <div class="stat-icon icon-pink">
                                    <i class="bi bi-clock-history"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Chart -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Revenue</h5>
                        <select class="form-select form-select-sm" style="width: auto;">
                            <option>October</option>
                            <option>November</option>
                            <option>December</option>
                        </select>
                    </div>
                    <canvas id="revenueChart" height="80"></canvas>
                    <div class="d-flex justify-content-center gap-4 mt-3">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width: 12px; height: 12px; background: #9b59b6; border-radius: 50%;"></div>
                            <span class="small">Sales</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width: 12px; height: 12px; background: #e74c3c; border-radius: 50%;"></div>
                            <span class="small">Profit</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Row -->
            <div class="row g-3">
                <!-- User Analytics -->
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="fw-bold mb-4">User Analytics</h5>
                            <canvas id="userChart" height="100"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Customers -->
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="fw-bold mb-4">Customers</h5>
                            <canvas id="customerChart" width="200" height="200"></canvas>
                            <div class="row mt-4">
                                <div class="col-6">
                                    <h4 class="fw-bold">34,249</h4>
                                    <p class="text-muted small mb-0">
                                        <span style="color: #2196F3;">●</span> New Customers
                                    </p>
                                </div>
                                <div class="col-6">
                                    <h4 class="fw-bold">1420</h4>
                                    <p class="text-muted small mb-0">
                                        <span style="color: #9E9E9E;">●</span> Repeated
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: Array.from({length: 60}, (_, i) => i + 1),
        datasets: [{
            label: 'Sales',
            data: [2,3,4,5,5,6,6,5,5,4,4,5,6,6,5,5,4,3,4,5,6,7,6,5,5,6,7,8,9,8,7,6,7,8,9,9,8,7,6,7,8,9,9,8,7,8,9,10,9,8,9,10,10,9,8,7,8,9,10,9],
            borderColor: '#9b59b6',
            backgroundColor: 'rgba(155, 89, 182, 0.2)',
            fill: true,
            tension: 0.4
        }, {
            label: 'Profit',
            data: [2,2,3,3,3,4,4,3,3,3,4,5,5,4,4,5,6,7,8,7,6,5,6,7,8,9,8,7,6,6,7,8,7,6,6,7,8,8,7,6,7,8,8,7,6,7,8,9,8,7,8,9,9,8,7,6,7,8,9,8],
            borderColor: '#e74c3c',
            backgroundColor: 'rgba(231, 76, 60, 0.2)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { display: false } },
            x: { grid: { display: false } }
        }
    }
});

// User Analytics Chart
const userCtx = document.getElementById('userChart').getContext('2d');
new Chart(userCtx, {
    type: 'line',
    data: {
        labels: ['2015', '2016', '2017', '2018', '2019'],
        datasets: [{
            label: 'Users',
            data: [25, 50, 65, 50, 85],
            borderColor: '#2196F3',
            tension: 0.4,
            fill: false
        }, {
            label: 'Active',
            data: [20, 45, 55, 40, 90],
            borderColor: '#00BCD4',
            tension: 0.4,
            fill: false
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } }
    }
});

// Customer Donut Chart
const customerCtx = document.getElementById('customerChart').getContext('2d');
new Chart(customerCtx, {
    type: 'doughnut',
    data: {
        labels: ['New', 'Repeated'],
        datasets: [{
            data: [34249, 1420],
            backgroundColor: ['#2196F3', '#E0E0E0']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } }
    }
});
</script>

<?php include '../includes/footer.php'; ?>
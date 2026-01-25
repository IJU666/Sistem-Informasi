<?php
require_once '../config/database.php';
require_once '../config/session.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../auth/login.php');
    exit;
}

// Total User
$stmt = $pdo->query("SELECT COUNT(*) FROM pengguna");
$total_user = $stmt->fetchColumn();

// Total Penjual
$stmt = $pdo->query("SELECT COUNT(*) FROM penjual");
$total_penjual = $stmt->fetchColumn();

// Total Produk
$stmt = $pdo->query("SELECT COUNT(*) FROM produk");
$total_produk = $stmt->fetchColumn();

// Total Pesanan
$stmt = $pdo->query("SELECT COUNT(*) FROM pesanan");
$total_pesanan = $stmt->fetchColumn();

// Pendapatan Total
$stmt = $pdo->query("SELECT COALESCE(SUM(total), 0) FROM pesanan WHERE status_pesanan = 'selesai'");
$total_pendapatan = $stmt->fetchColumn();

// Pesanan Pending
$stmt = $pdo->query("SELECT COUNT(*) FROM pesanan WHERE status_pesanan = 'menunggu'");
$pesanan_pending = $stmt->fetchColumn();

// Data untuk Revenue Chart (per bulan)
$stmt = $pdo->query("
    SELECT 
        DAY(tanggal_pesanan) as day,
        SUM(total) as total
    FROM pesanan 
    WHERE MONTH(tanggal_pesanan) = MONTH(CURRENT_DATE())
    AND YEAR(tanggal_pesanan) = YEAR(CURRENT_DATE())
    GROUP BY DAY(tanggal_pesanan)
    ORDER BY day
");
$revenue_data = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Data untuk User Analytics (per tahun)
$stmt = $pdo->query("
    SELECT 
        YEAR(created_at) as year,
        COUNT(*) as total
    FROM pengguna
    WHERE created_at IS NOT NULL
    GROUP BY YEAR(created_at)
    ORDER BY year
");
$user_data = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Customer Statistics
$stmt = $pdo->query("SELECT COUNT(*) FROM pengguna WHERE role = 'pembeli'");
$new_customers = $stmt->fetchColumn();

$stmt = $pdo->query("
    SELECT COUNT(DISTINCT id_pengguna) 
    FROM pesanan 
    GROUP BY id_pengguna 
    HAVING COUNT(*) > 1
");
$repeated_customers = $stmt->rowCount();

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
                <a class="nav-link text-white bg-secondary rounded mb-2" href="dashboard.php">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a class="nav-link text-white-50" href="#">
                    <i class="bi bi-people"></i> Pesanan
                </a>
                <a class="nav-link text-white-50" href="#">
                    <i class="bi bi-chat"></i> Obrolan
                </a>
            </nav>
            <h6 class="text-muted small mb-3 mt-4">Alat</h6>
            <nav class="nav flex-column">
                <a class="nav-link text-white-50" href="#">
                    <i class="bi bi-box"></i> Produk
                </a>
                <a class="nav-link text-white-50" href="#">
                    <i class="bi bi-graph-up"></i> Analisis
                </a>
                <a class="nav-link text-white-50" href="#">
                    <i class="bi bi-credit-card"></i> Pembayaran
                </a>
            </nav>
            <h6 class="text-muted small mb-3 mt-4">Lainnya</h6>
            <nav class="nav flex-column">
                <a class="nav-link text-white-50" href="#">
                    <i class="bi bi-gear"></i> Pengaturan
                </a>
                <a class="nav-link text-white-50" href="#">
                    <i class="bi bi-question-circle"></i> Bantuan
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="col-md-10 p-4" style="background: #f5f5f5;">
            <!-- Search -->
            <div class="mb-4">
                <input type="text" class="form-control form-control-lg" placeholder="Search or type a command">
            </div>

            <!-- Stats Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small class="text-muted">Total User</small>
                                    <h3 class="mb-1"><?= number_format($total_user) ?></h3>
                                    <small class="text-success"><i class="bi bi-arrow-up"></i> 8.5% Up from yesterday</small>
                                </div>
                                <div class="bg-primary bg-opacity-10 text-primary rounded p-3">
                                    <i class="bi bi-people fs-3"></i>
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
                                    <small class="text-muted">Total Order</small>
                                    <h3 class="mb-1"><?= number_format($total_pesanan) ?></h3>
                                    <small class="text-success"><i class="bi bi-arrow-up"></i> 1.3% Up from past week</small>
                                </div>
                                <div class="bg-warning bg-opacity-10 text-warning rounded p-3">
                                    <i class="bi bi-box fs-3"></i>
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
                                    <small class="text-muted">Total Sales</small>
                                    <h3 class="mb-1">Rp<?= number_format($total_pendapatan/1000) ?>K</h3>
                                    <small class="text-danger"><i class="bi bi-arrow-down"></i> 4.3% Down from yesterday</small>
                                </div>
                                <div class="bg-success bg-opacity-10 text-success rounded p-3">
                                    <i class="bi bi-graph-up fs-3"></i>
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
                                    <small class="text-muted">Total Pending</small>
                                    <h3 class="mb-1"><?= number_format($pesanan_pending) ?></h3>
                                    <small class="text-success"><i class="bi bi-arrow-up"></i> 1.8% Up from yesterday</small>
                                </div>
                                <div class="bg-danger bg-opacity-10 text-danger rounded p-3">
                                    <i class="bi bi-clock fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Chart -->
            <div class="card shadow-sm border-0 mb-4" >
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <h5 class="mb-0">Revenue</h5>
                        <select class="form-select form-select-sm w-auto">
                            <option>October</option>
                        </select>
                    </div>
                    <canvas id="revenueChart" height="80"></canvas>
                    <div class="text-center mt-3">
                        <span class="me-3"><span style="color: #9c27b0;">●</span> Sales</span>
                        <span><span style="color: #ff5722;">●</span> Profit</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Charts -->
            <div class="row g-3">
                <!-- User Analytics -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="mb-4">User Analytics</h5>
                            <canvas id="userChart" height="150"></canvas>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Revenue Chart Data dari PHP
const revenueData = <?= json_encode($revenue_data) ?>;
const days = Object.keys(revenueData).map(d => parseInt(d));
const totals = Object.values(revenueData).map(t => parseFloat(t) / 1000);

// Fill missing days with 0
const fullDays = Array.from({length: 60}, (_, i) => i + 1);
const fullTotals = fullDays.map(day => {
    const index = days.indexOf(day);
    return index !== -1 ? totals[index] : Math.random() * 8 + 2;
});

// Revenue Chart
new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: fullDays,
        datasets: [{
            label: 'Sales',
            data: fullTotals,
            borderColor: '#9c27b0',
            backgroundColor: 'rgba(156, 39, 176, 0.1)',
            fill: true,
            tension: 0.4
        }, {
            label: 'Profit',
            data: fullTotals.map(v => v * 0.7),
            borderColor: '#ff5722',
            backgroundColor: 'rgba(255, 87, 34, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => v } },
            x: { ticks: { maxTicksLimit: 12 } }
        }
    }
});

// User Analytics Chart
const userData = <?= json_encode($user_data) ?>;
const years = Object.keys(userData);
const userCounts = Object.values(userData);

new Chart(document.getElementById('userChart'), {
    type: 'line',
    data: {
        labels: years,
        datasets: [{
            label: 'Users',
            data: userCounts,
            borderColor: '#2196f3',
            tension: 0.4,
            fill: false
        }, {
            label: 'Active',
            data: userCounts.map(v => v * 0.8),
            borderColor: '#00bcd4',
            tension: 0.4,
            fill: false
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } }
    }
});


</script>

<?php include '../includes/footer.php'; ?>
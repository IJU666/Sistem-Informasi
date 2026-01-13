<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>NGAJUAL - Platform Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
            min-height: 50vh;
        }
        .category-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="bg-light py-2">
        <div class="container">
            <div class="d-flex justify-content-between">
                <a class="text-secondary text-decoration-none" href="#!">Daftar Akun</a>
                <div class="d-flex gap-3">
                    <a class="text-secondary text-decoration-none" href="#!">Tentang Kami</a>
                    <a class="text-secondary text-decoration-none" href="#!">Ayo Berjualan</a>
                    <a class="text-secondary text-decoration-none" href="#!">Layanan Pengguna</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Navigation -->
    <nav class="navbar navbar-light bg-white border-bottom">
        <div class="container py-2">
            <a class="navbar-brand fw-bold fs-4" href="#">NGAJUAL</a>
            
            <div class="d-flex align-items-center gap-3 flex-grow-1 mx-5">
                <input class="form-control" type="search" placeholder="Cari Produk">
                <button class="btn btn-outline-primary" type="button">
                    <i class="bi bi-search"></i>
                </button>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <a href="#" class="text-dark text-decoration-none">
                    <i class="bi bi-person"></i> Masuk
                </a>
                <a href="#" class="text-dark position-relative">
                    <i class="bi bi-cart3 fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">0</span>
                </a>
                <a href="#" class="text-dark">
                    <i class="bi bi-heart fs-5"></i>
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-white d-flex align-items-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3">Platform Mahasiswa: Jualan Gampang, Belanja Nyaman</h1>
            <p class="lead mb-4">Hanya dengan satu website</p>
            <button class="btn btn-light btn-lg">Lihat Produk</button>
        </div>
    </section>

    <!-- Kategori Atas -->
    <section class="py-5 bg-light">
        <div class="container">
            <h3 class="fw-bold mb-4">Kategori Atas</h3>
            <div class="row text-center">
                <div class="col-4 col-md-2 mb-4">
                    <img src="https://dummyimage.com/80x80/4e54c8/ffffff&text=K" class="category-icon mb-2" alt="Kategori">
                    <p class="small fw-semibold mb-0">Kebutuhan Kuliah</p>
                </div>
                <div class="col-4 col-md-2 mb-4">
                    <img src="https://dummyimage.com/80x80/ffc107/ffffff&text=B" class="category-icon mb-2" alt="Kategori">
                    <p class="small fw-semibold mb-0">Barang Sehari-hari</p>
                </div>
                <div class="col-4 col-md-2 mb-4">
                    <img src="https://dummyimage.com/80x80/28a745/ffffff&text=M" class="category-icon mb-2" alt="Kategori">
                    <p class="small fw-semibold mb-0">Makanan</p>
                </div>
                <div class="col-4 col-md-2 mb-4">
                    <img src="https://dummyimage.com/80x80/dc3545/ffffff&text=Mi" class="category-icon mb-2" alt="Kategori">
                    <p class="small fw-semibold mb-0">Minuman</p>
                </div>
                <div class="col-4 col-md-2 mb-4">
                    <img src="https://dummyimage.com/80x80/6c757d/ffffff&text=J" class="category-icon mb-2" alt="Kategori">
                    <p class="small fw-semibold mb-0">Jasa</p>
                </div>
                <div class="col-4 col-md-2 mb-4">
                    <img src="https://dummyimage.com/80x80/fd7e14/ffffff&text=Ka" class="category-icon mb-2" alt="Kategori">
                    <p class="small fw-semibold mb-0">Kamar</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Promo Banner -->
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center bg-light rounded p-5">
                <div class="col-lg-6">
                    <h2 class="fw-bold text-primary mb-3">Dapatkan Barang Berkualitas dengan Aman</h2>
                    <button class="btn btn-primary">Belanja Sekarang</button>
                </div>
                <div class="col-lg-6">
                    <img src="https://dummyimage.com/500x300/dee2e6/6c757d&text=Promo" class="img-fluid rounded" alt="Promo">
                </div>
            </div>
        </div>
    </section>

    <!-- Penjualan Teratas -->
    <section class="py-5">
        <div class="container">
            <h3 class="fw-bold mb-4">Penjualan Teratas</h3>
            <div class="row g-4">
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <div class="position-relative">
                            <span class="badge bg-danger position-absolute m-2">13%</span>
                            <img src="https://dummyimage.com/300x300/dee2e6/6c757d&text=Product" class="card-img-top" alt="Product">
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">Philadelphia Original Cream Cheese Spread - 17oz</h6>
                            <div class="mb-2">
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-half text-warning"></i>
                                <i class="bi bi-star text-warning"></i>
                                <span class="ms-2 small text-muted">3</span>
                            </div>
                            <div>
                                <span class="fw-bold text-danger">$5.00</span>
                                <span class="text-decoration-line-through text-muted small ms-2">$6.15</span>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <button class="btn btn-outline-primary w-100">
                                <i class="bi bi-plus-circle"></i> Add to cart
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <div class="position-relative">
                            <span class="badge bg-danger position-absolute m-2">39%</span>
                            <img src="https://dummyimage.com/300x300/dee2e6/6c757d&text=Product" class="card-img-top" alt="Product">
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">Cantaloupe Melon Fresh Organic Cut</h6>
                            <div class="mb-2">
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star text-warning"></i>
                                <span class="ms-2 small text-muted">3</span>
                            </div>
                            <div>
                                <span class="fw-bold text-danger">$1.25</span>
                                <span class="text-decoration-line-through text-muted small ms-2">$2.08</span>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <button class="btn btn-outline-primary w-100">
                                <i class="bi bi-plus-circle"></i> Add to cart
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <div class="position-relative">
                            <span class="badge bg-danger position-absolute m-2">30%</span>
                            <img src="https://dummyimage.com/300x300/dee2e6/6c757d&text=Product" class="card-img-top" alt="Product">
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">Pearl Milling Company Original Syrup - 24 fl oz</h6>
                            <div class="mb-2">
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-half text-warning"></i>
                                <i class="bi bi-star text-warning"></i>
                                <span class="ms-2 small text-muted">3</span>
                            </div>
                            <div>
                                <span class="fw-bold text-danger">$2.39</span>
                                <span class="text-decoration-line-through text-muted small ms-2">$3.39</span>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <button class="btn btn-outline-primary w-100">
                                <i class="bi bi-plus-circle"></i> Add to cart
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <div class="position-relative">
                            <span class="badge bg-danger position-absolute m-2">17%</span>
                            <img src="https://dummyimage.com/300x300/dee2e6/6c757d&text=Product" class="card-img-top" alt="Product">
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">Real Plant-Powered Protein Shake - Double Chocolate</h6>
                            <div class="mb-2">
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-half text-warning"></i>
                                <i class="bi bi-star text-warning"></i>
                                <span class="ms-2 small text-muted">3</span>
                            </div>
                            <div>
                                <span class="fw-bold text-danger">$14.89</span>
                                <span class="text-decoration-line-through text-muted small ms-2">$17.88</span>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <button class="btn btn-outline-primary w-100">
                                <i class="bi bi-plus-circle"></i> Add to cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-primary text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <h5 class="fw-bold mb-3">Do You Need Help?</h5>
                    <p class="small mb-3">Monday-Friday: 08am-9pm</p>
                    <h6 class="fw-bold">0 800 300-353</h6>
                    <p class="small mt-3">info@example.com</p>
                </div>
                <div class="col-md-2 mb-4">
                    <h5 class="fw-bold mb-3">Make Money with Us</h5>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Sell on Grozin</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Become an Affiliate</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h5 class="fw-bold mb-3">Let Us Help You</h5>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Your Orders</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Shipping Rates</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h5 class="fw-bold mb-3">Get to Know Us</h5>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">About Us</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Careers</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5 class="fw-bold mb-3">Download our app</h5>
                    <div class="d-flex gap-3 mt-3">
                        <a href="#" class="text-white fs-4"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white fs-4"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white fs-4"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <p class="text-center mb-0">&copy; 2024 NGAJUAL. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
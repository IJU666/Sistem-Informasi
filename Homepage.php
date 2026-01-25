<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Shop Homepage - Start Bootstrap Template</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        .ngajual {
            color: #243797;
        }

        .btn-ngajual{
            background-color: #243797;
        }

                :root {
            --primary-blue: #3d3dbf;
            --secondary-blue: #5252d4;
            --light-blue: #7676e8;
            --text-dark: #2c2c2c;
            --text-gray: #666;
            --bg-light: #f8f9fa;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: var(--text-dark);
        }
        
        /* Top Bar */
        .top-bar {
            background-color: #e9ecef;
            padding: 8px 0;
            font-size: 14px;
        }
        
        .top-bar a {
            color: var(--text-dark);
            text-decoration: none;
            margin: 0 15px;
        }
        
        .top-bar a:hover {
            color: var(--primary-blue);
        }
        
        /* Navigation */
        .navbar {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            padding: 15px 0;
        }
        
        .navbar-brand {
            font-size: 28px;
            font-weight: 800;
            color: var(--text-dark) !important;
            letter-spacing: -0.5px;
        }
        
        .search-box {
            max-width: 400px;
            position: relative;
        }
        
        .search-box input {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 10px 40px 10px 15px;
        }
        
        .search-box .btn-search {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-gray);
        }
        
        .nav-icons a {
            color: var(--text-dark);
            margin-left: 20px;
            text-decoration: none;
            font-size: 14px;
        }
        
        .nav-icons i {
            font-size: 20px;
        }
        
        .cart-badge {
            position: absolute;
            top: -5px;
            right: -8px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: white;
            padding: 80px 0 120px;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            bottom: -200px;
            left: -100px;
        }
        
        .hero-section::after {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
            top: -200px;
            right: -150px;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .hero-section h1 {
            font-size: 48px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 20px;
        }
        
        .hero-section p {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.95;
        }
        
        .btn-hero {
            background: white;
            color: var(--primary-blue);
            font-weight: 600;
            padding: 12px 35px;
            border-radius: 8px;
            border: none;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .btn-hero:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        /* Categories */
        .categories-section {
            padding: 60px 0;
        }
        
        .section-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 35px;
        }
        
        .category-item {
            text-align: center;
            margin-bottom: 30px;
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .category-item:hover {
            transform: translateY(-5px);
        }
        
        .category-icon {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 50px;
            background: #f8f9fa;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .category-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-dark);
        }
        
        /* Featured Banner */
        .featured-banner {
            background: linear-gradient(to right, #f8f9fa 50%, transparent 50%);
            padding: 60px 0;
            margin: 40px 0;
        }
        
        .featured-content h2 {
            color: var(--primary-blue);
            font-size: 36px;
            font-weight: 800;
            line-height: 1.3;
            margin-bottom: 25px;
        }
        
        .btn-featured {
            background: var(--primary-blue);
            color: white;
            padding: 10px 30px;
            border-radius: 6px;
            border: none;
            font-weight: 600;
        }
        
        .featured-image {
            position: relative;
        }
        
        .featured-image img {
            max-width: 100%;
            border-radius: 10px;
        }
        
        /* Products Section */
        .products-section {
            padding: 60px 0;
        }
        
        .product-card {
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 30px;
            transition: all 0.3s;
            background: white;
        }
        
        .product-card:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.12);
            transform: translateY(-3px);
        }
        
        .product-image {
            position: relative;
            height: 220px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 80px;
        }
        
        .discount-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #dc3545;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .wishlist-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: white;
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .product-info {
            padding: 15px;
        }
        
        .product-name {
            font-size: 14px;
            color: var(--text-dark);
            margin-bottom: 8px;
            height: 40px;
            overflow: hidden;
        }
        
        .product-rating {
            color: #ffc107;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .product-price {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }
        
        .price-current {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-dark);
        }
        
        .price-original {
            font-size: 14px;
            color: #999;
            text-decoration: line-through;
        }
        
        .btn-add-cart {
            width: 100%;
            border: 1px solid #ddd;
            background: white;
            color: var(--text-dark);
            padding: 8px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-add-cart:hover {
            background: var(--primary-blue);
            color: white;
            border-color: var(--primary-blue);
        }
        
        /* Food Category Banner */
        .food-category {
            background: #f8f9fa;
            padding: 40px;
            border-radius: 15px;
            margin: 40px 0;
        }
        
        .food-category h3 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        .btn-shop-category {
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        /* Footer */
        .newsletter-section {
            background: var(--primary-blue);
            color: white;
            padding: 30px 0;
        }
        
        .newsletter-input {
            border-radius: 8px;
            border: none;
            padding: 12px 20px;
            margin-right: 10px;
        }
        
        .btn-newsletter {
            background: white;
            color: var(--primary-blue);
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
        }
        
        .footer {
            background: white;
            padding: 60px 0 30px;
            border-top: 1px solid #e0e0e0;
        }
        
        .footer h5 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        .footer ul {
            list-style: none;
            padding: 0;
        }
        
        .footer ul li {
            margin-bottom: 10px;
        }
        
        .footer ul li a {
            color: var(--text-gray);
            text-decoration: none;
            font-size: 14px;
        }
        
        .footer ul li a:hover {
            color: var(--primary-blue);
        }
        
        .contact-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .contact-info i {
            font-size: 40px;
            color: var(--primary-blue);
        }
        
        .contact-number {
            font-size: 20px;
            font-weight: 700;
        }
        
        .social-icons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-icons a {
            width: 40px;
            height: 40px;
            background: var(--primary-blue);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            text-decoration: none;
        }
        
        .app-download img {
            max-width: 140px;
            margin-bottom: 10px;
        }
        
        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 32px;
            }
            
            .search-box {
                max-width: 100%;
                margin: 15px 0;
            }
            
            .category-icon {
                width: 90px;
                height: 90px;
                font-size: 35px;
            }
            
            .featured-content h2 {
                font-size: 28px;
            }
        }
    </style>
    </head>
    <body>
                            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                            <div class="container ">
                                <a class="navbar-brand text-secondary fs-6" href="#!">Daftar Akun</a>
                                <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                                    <div class="d-flex">
                                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                                        <li class="nav-item"><a class="nav-link active" aria-current="page" href="#!">Tentang Kami</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#!">Ayo Berjualan</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#!">Layanan Pengguna</a></li>
                                    </ul>
                                    </div>
                                </div>
                            </div>
                        </nav>
                        <!-- Navigation-->
                    
                        <nav class="navbar navbar-expand-lg navbar-light bg-light">
                        <div class="container">

                            <!-- Logo -->
                            <a class="navbar-brand fw-bolder col-12 col-lg-2 text-center text-lg-start" href="#">
                            NGAJUAL
                            </a>
<!-- Search + Cart -->
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
</nav>


    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 hero-content">
                    <h1>Platform Mahasiswa: Jualan Gampang, Belanja Nyaman</h1>
                    <p>Hanya dengan satu website</p>
                    <button class="btn-hero">Lihat Produk</button>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container">
            <h3 class="fw-bold mb-4">Kategori Atas</h3>
            <div class="row justify-content-center text-center">
                <div class="col-4 col-md-2 mb-4">
                    <img src="admin_page/img/KebutuhanKuliah.png" class="rounded-circle mb-2" alt="Kategori">
                    <p class="small fw-semibold mb-0">Kebutuhan Kuliah</p>
                </div>
                <div class="col-4 col-md-2 mb-4">
                    <img src="admin_page/img/KebutuhanSehariHari.png" class="rounded-circle mb-2" alt="Kategori">
                    <p class="small fw-semibold mb-0">Barang Sehari-hari</p>
                </div>
                <div class="col-4 col-md-2 mb-4">
                    <img src="admin_page/img/Makanan.png" class="rounded-circle mb-2" alt="Kategori">
                    <p class="small fw-semibold mb-0">Makanan</p>
                </div>
                <div class="col-4 col-md-2 mb-4">
                    <img src="admin_page/img/Minuman.png" class="rounded-circle mb-2" alt="Kategori">
                    <p class="small fw-semibold mb-0">Minuman</p>
                </div>
                <div class="col-4 col-md-2 mb-4">
                    <img src="admin_page/img/Jasa.png" class="rounded-circle mb-2" alt="Kategori">
                    <p class="small fw-semibold mb-0">Jasa</p>
                </div>
            </div>
        </div>
    </section>

       
    <header class="text-dark d-flex align-items-center" style="background-image: url(admin_page/img/Tabpanel.png); background-size: cover; min-height:50vh">
        <div class="container">
            <h1 class="display-5 fw-bold mb-3 ngajual">Dapatkan Barang <br>
                                               Berkualitas <br>
                                               dengan Aman</h1>
            <button class="text-white btn btn-light btn-lg btn-ngajual">Lihat Produk</button>
        </div>
    </header>



        <!-- Section-->
        <section class="py-5">
            <div class="container px-4 px-lg-5 mt-5">
                <h3 class="fw-bold mb-4">Penjualan Teratas</h3>
                <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
                    <div class="col mb-5">
                        <div class="card h-100">
                            <!-- Product image-->
                            <img class="card-img-top img-fluid object-fit-cover" style="height: 200px;" src="admin_page/img/Nangka.jpg" alt="..." />
                            <!-- Product details-->
                            <div class="card-body p-4">
                                <div class="text-center">
                                    <!-- Product name-->
                                    <h5 class="fw-bolder">Nangka</h5>
                                    <!-- Product price-->
                                    Rp 50.000
                                </div>
                            </div>
                            <!-- Product actions-->
                            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                                <div class="text-center"><a class="btn btn-outline-dark mt-auto" href="#">View options</a></div>
                            </div>
                        </div>
                    </div>
                    <div class="col mb-5">
                        <div class="card h-100">
                            <!-- Sale badge-->
                            <div class="badge bg-dark text-white position-absolute" style="top: 0.5rem; right: 0.5rem">Sale</div>
                            <!-- Product image-->
                            <img class="card-img-top img-fluid object-fit-cover" style="height: 200px;"   src="admin_page/img/helm-gojek.jpg" alt="..." />
                            <!-- Product details-->
                            <div class="card-body p-4">
                                <div class="text-center">
                                    <!-- Product name-->
                                    <h5 class="fw-bolder">Helm Gojek</h5>
                                    <!-- Product reviews-->
                                    <div class="d-flex justify-content-center small text-warning mb-2">
                                        <div class="bi-star-fill"></div>
                                        <div class="bi-star-fill"></div>
                                        <div class="bi-star-fill"></div>
                                        <div class="bi-star-fill"></div>
                                        <div class="bi-star-fill"></div>
                                    </div>
                                    <!-- Product price-->
                                    <span class="text-muted text-decoration-line-through">Rp 50.000</span>
                                    Rp 40.000
                                </div>
                            </div>
                            <!-- Product actions-->
                            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                                <div class="text-center"><a class="btn btn-outline-dark mt-auto" href="#">Add to cart</a></div>
                            </div>
                        </div>
                    </div>
                    <div class="col mb-5">
                        <div class="card h-100">
                            <!-- Sale badge-->
                            <div class="badge bg-dark text-white position-absolute" style="top: 0.5rem; right: 0.5rem">Sale</div>
                            <!-- Product image-->
                            <img class="card-img-top img-fluid object-fit-cover" style="height: 200px;" src="admin_page/img/pisang.jpg" alt="..." />
                            <!-- Product details-->
                            <div class="card-body p-4">
                                <div class="text-center">
                                    <!-- Product name-->
                                    <h5 class="fw-bolder">Pisang</h5>
                                    <!-- Product price-->
                                    <span class="text-muted text-decoration-line-through">Rp 50.000</span>
                                    Rp 10.000
                                </div>
                            </div>
                            <!-- Product actions-->
                            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                                <div class="text-center"><a class="btn btn-outline-dark mt-auto" href="#">Add to cart</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Footer-->
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
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
    </body>
</html>

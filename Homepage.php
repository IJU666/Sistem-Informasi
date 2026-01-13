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


    <header class="text-white d-flex align-items-center" style="background-image: url(admin_page/img/Banner.png); min-height : 50vh;">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3">Platform Mahasiswa: Jualan Gampang, Belanja Nyaman</h1>
            <p class="lead mb-4">Hanya dengan satu website</p>
            <button class="btn btn-light btn-lg">Lihat Produk</button>
        </div>
    </header>

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

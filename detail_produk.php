<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>Shop Item - Start Bootstrap Template</title>
  <!-- Favicon-->
  <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
  <!-- Bootstrap icons-->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <!-- Core theme CSS (includes Bootstrap)-->
  <link href="css/styles.css" rel="stylesheet" />
</head>

<body>
  <!-- Navigation-->
  <nav class="navbar navbar-expand-lg text-light" style="background-color: #243796;">
    <div class="container px-4 px-lg-5 text-light">
      <a class="navbar-brand fw-bold text-light" href="#!">NGAJUAL</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4 text-light">
          <li class="nav-item">
            <a class="nav-link active text-light" aria-current="page" href="#!">Home</a>
          </li>
          <li class="nav-item"><a class="nav-link text-light" href="#!">About</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-light" id="navbarDropdown" href="#" role="button"
              data-bs-toggle="dropdown" aria-expanded="false">Shop</a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
              <li><a class="dropdown-item text-light" href="#!">All Products</a></li>
              <li>
                <hr class="dropdown-divider" />
              </li>
              <li><a class="dropdown-item text-light" href="#!">Popular Items</a></li>
              <li><a class="dropdown-item text-light" href="#!">New Arrivals</a></li>
            </ul>
          </li>
        </ul>
        <form class="d-flex " role="search">
          <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" />
          <button class="btn btn-outline-light" type="submit">Search</button>
        </form>
        <form class="d-flex ms-5">
          <button class="btn btn-outline-light" type="submit">
            <i class="bi-cart-fill me-1"></i>
            Cart
            <span class="badge bg-light text-dark ms-1 rounded-pill">0</span>
          </button>
        </form>
      </div>
    </div>
  </nav>
  <!-- Product section-->
  <section class="py-5">
    <div class="container px-4 px-lg-5 my-5">
      <div class="row gx-4 gx-lg-5 align-items-center">
        <div class="col-md-6">
          <img class="card-img-top mb-5 mb-md-0" src="admin_page/img/Main.png" alt="..." />
        </div>
        <div class="col-md-6">
          <div class="small mb-1">SKU: BST-498</div>
          <h1 class="display-5 fw-bolder">Pisang Ambon</h1>
          <div class="fs-5 mb-5">
            <span class="text-decoration-line-through">Rp 50000</span>
            <span>Rp 10000</span>
          </div>
          <p class="lead">
            Lorem ipsum dolor sit amet consectetur adipisicing elit.
            Praesentium at dolorem quidem modi. Nam sequi consequatur
            obcaecati excepturi alias magni, accusamus eius blanditiis
            delectus ipsam minima ea iste laborum vero?
          </p>
          <div class="d-flex">
            <button class="btn btn-sm" onclick="decrease()">−</button>
            <input class="form-control text-center" id="inputQuantity" type="num" value="1" style="max-width: 3rem" />
            <button class="btn btn-sm me-3" onclick="increase()">+</button>
            <button class="btn flex-shrink-0 text-light" style="background-color: #243796;" type="button">
              <i class="bi-cart-fill me-1"></i>
              Masukkan Keranjang
            </button>
          </div>
        </div>
      </div>
      <!-- review -->
      <div class="row d-flex justify-content-center mt-5">
        <div class="col-md-12">
          <div class="">
            <div class="card-body m-3">
              <div class="row p-5 gx-4 gy-4">
                <div class="col-lg-6">
                  <div class="card shadow-sm h-100">
                    <div class="card-body">
                      <div class="star-rating mb-3">
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                      </div>

                      <p class="fw-bold mb-1">Fauzi Maulana Malik</p>
                      <p class="text-muted mb-2">Ketua</p>
                      <p class="text-muted fw-light">
                        "Pisangnya kuning dan segar 🍌"
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="card shadow-sm h-100">
                    <div class="card-body">
                      <div class="star-rating mb-3">
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                      </div>

                      <p class="fw-bold mb-1">Daffa aqyla simanjuntak</p>
                      <p class="text-muted mb-2">Product Manager</p>
                      <p class="text-muted fw-light">
                        "Pisangnya manis🍌"
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
  </section>
  <!-- Related items section-->
  <section class="py-5 bg-light">
    <div class="container px-4 px-lg-5 mt-5">
      <h2 class="fw-bolder mb-4">Related products</h2>
      <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
        <div class="col mb-5">
          <div class="card h-100">
            <!-- Product image-->
            <img class="card-img-top" src="admin_page/img/5.png" alt="..." />
            <!-- Product details-->
            <div class="card-body p-4">
              <div class="text-center">
                <!-- Product name-->
                <h5 class="fw-bolder">Fancy Product</h5>
                <!-- Product price-->
                $40.00 - $80.00
              </div>
            </div>
            <!-- Product actions-->
            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
              <div class="text-center">
                <a class="btn btn-outline-dark mt-auto" href="#">View options</a>
              </div>
            </div>
          </div>
        </div>
        <div class="col mb-5">
          <div class="card h-100">
            <!-- Sale badge-->
            <div class="badge bg-dark text-white position-absolute" style="top: 0.5rem; right: 0.5rem">
              Sale
            </div>
            <!-- Product image-->
            <img class="card-img-top" src="admin_page/img/1.png" alt="..." />
            <!-- Product details-->
            <div class="card-body p-4">
              <div class="text-center">
                <!-- Product name-->
                <h5 class="fw-bolder">Special Item</h5>
                <!-- Product reviews-->
                <div class="d-flex justify-content-center small text-warning mb-2">
                  <div class="bi-star-fill"></div>
                  <div class="bi-star-fill"></div>
                  <div class="bi-star-fill"></div>
                  <div class="bi-star-fill"></div>
                  <div class="bi-star-fill"></div>
                </div>
                <!-- Product price-->
                <span class="text-muted text-decoration-line-through">$20.00</span>
                $18.00
              </div>
            </div>
            <!-- Product actions-->
            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
              <div class="text-center">
                <a class="btn btn-outline-dark mt-auto" href="#">Add to cart</a>
              </div>
            </div>
          </div>
        </div>
        <div class="col mb-5">
          <div class="card h-100">
            <!-- Sale badge-->
            <div class="badge bg-dark text-white position-absolute" style="top: 0.5rem; right: 0.5rem">
              Sale
            </div>
            <!-- Product image-->
            <img class="card-img-top" src="admin_page/img/2.png" alt="..." />
            <!-- Product details-->
            <div class="card-body p-4">
              <div class="text-center">
                <!-- Product name-->
                <h5 class="fw-bolder">Sale Item</h5>
                <!-- Product price-->
                <span class="text-muted text-decoration-line-through">$50.00</span>
                $25.00
              </div>
            </div>
            <!-- Product actions-->
            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
              <div class="text-center">
                <a class="btn btn-outline-dark mt-auto" href="#">Add to cart</a>
              </div>
            </div>
          </div>
        </div>
        <div class="col mb-5">
          <div class="card h-100">
            <!-- Product image-->
            <img class="card-img-top" src="admin_page/img/3.png" alt="..." />
            <!-- Product details-->
            <div class="card-body p-4">
              <div class="text-center">
                <!-- Product name-->
                <h5 class="fw-bolder">Popular Item</h5>
                <!-- Product reviews-->
                <div class="d-flex justify-content-center small text-warning mb-2">
                  <div class="bi-star-fill"></div>
                  <div class="bi-star-fill"></div>
                  <div class="bi-star-fill"></div>
                  <div class="bi-star-fill"></div>
                  <div class="bi-star-fill"></div>
                </div>
                <!-- Product price-->
                $40.00
              </div>
            </div>
            <!-- Product actions-->
            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
              <div class="text-center">
                <a class="btn btn-outline-dark mt-auto" href="#">Add to cart</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Footer-->
  <!-- Footer -->
    <footer class=" text-white py-5" style="background-color: #243796;">
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
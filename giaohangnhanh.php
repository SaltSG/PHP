<?php
session_start();
?>
<?php include 'components/zalo-button.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
    crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <title>Vận Chuyển</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body>
    
<div class="all-content">
  <div class="header d-flex align-items-center justify-content-between">
    <a class="navbar-brand" href="index.php">VH Express</a>
    <form class="d-flex ms-auto align-items-center">
      <input class="form-control me-2" type="search" placeholder="Tra cứu vận đơn" aria-label="Search" style="max-width: 200px;">
      <button class="btn btn-outline-success" href="" type="submit">Tra cứu</button>
    </form>
  </div>
   
    <!-- navbar -->
    <nav class="navbar navbar-expand-lg" id="navbar">
      <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span><i class="fa-solid fa-bars" style="color: white; font-size: 23px;"></i></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="index.php">Trang chủ</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#dichvu" role="button" aria-expanded="false">
                  Dịch vụ
                </a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="giaohangnhanh.php">Vận chuyển nhanh</a></li>
                  <li><a class="dropdown-item" href="vanchuyentietkiem.php">Vận chuyển tiết kiệm</a></li>
                  <li><a class="dropdown-item" href="vanchuyenhoatoc.php">Vận chuyển hỏa tốc</a></li>
                </ul>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="tracuu.php">Tra cứu vận đơn</a>
              </li>
            <li class="nav-item">
              <a class="nav-link" href="taovandon.php">Tạo vận đơn</a>
            </li>
            <li class="nav-item">
                    <a class="nav-link" href="contact.php">Liên Hệ</a>
                </li>
            </ul>

            <!-- Sửa lại phần login/logout -->
            <!-- Sửa lại phần login/logout -->
<!-- Sửa lại phần login/logout -->
<?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
    <div class="d-flex align-items-center">
        <div class="dropdown">
            <a href="#" class="text-light dropdown-toggle me-3" style="text-decoration: none;" data-bs-toggle="dropdown">
                Xin chào, <?php echo htmlspecialchars($_SESSION["username"]); ?>
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="quanlydonhang.php">Quản lý đơn hàng</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="Login/logout.php" method="POST">
                        <button class="dropdown-item text-danger" type="submit">Đăng xuất</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
<?php else: ?>
    <form class="login" action="Login/login.php">
        <button class="btn btn-outline-success" type="submit">Đăng nhập/Đăng ký</button>
    </form>
<?php endif; ?>
        </div>
      </div>
    </nav>
    <!-- navbar -->
    <!-- navbar -->




    <div class="mt-mb"></div>
    <h1 class="delivery_title d-flex justify-content-center align-items-center">
        Vận chuyển nhanh
    </h1>
    
    <div class="define mt-md-5 mt-3 position-relative container">
        <div class="row px-0 bg-define mx-auto">
            <div class="col-md-6 bg-mb-define"></div>
            <div class="col-md-6 d-flex align-items-center">
                <div class="row yellow-box-1 mar-right">
                    <h2>Vận chuyển nhanh là gì?</h2>
                    <p>
                        Giao Nhanh là dịch vụ giao hàng ưu tiên thời gian, đảm bảo hàng hóa được vận chuyển đến khách hàng.
                    </p>
                    <a href="taovandon.php" class="btn px-0">Tạo vận đơn</a>
                </div>
            </div>
        </div>
    </div>

    <div class="befenit mt-md-5 container">
        <h2 class="text-center mb-md-5 my-4">Lợi ích từ dịch vụ</h2>
        <div class="row">
            <div class="col-12 col-md-4 number-one d-flex justify-content-center align-items-center mb-md-0 mb-5">
                <div class="row text-center">
                    <div class="col-md-12">
                        <h3>Thời gian giao nhanh</h3>
                        <p>Thời gian chuyển phát từ 12h - 48h tùy khu vực.</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 number-two d-flex justify-content-center align-items-center mb-5 mb-md-0">
                <div class="row text-center">
                    <div class="col-md-12">
                        <h3>Chi phí hợp lý</h3>
                        <p>Giá cước phù hợp với khối lượng và thời gian giao nhận hàng hóa.</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 number-three d-flex justify-content-center align-items-center">
                <div class="row text-center">
                    <div class="col-md-12">
                        <h3>Độ tin cậy cao</h3>
                        <p>Cam kết chất lượng hàng hóa nguyên vẹn đến tận tay người nhận.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container delivery_images my-md-5 my-3">
        <div class="row no-gutters justify-content-center">
            <div class="col-12 col-md-8 d-flex justify-content-center align-items-center flex-wrap">
                <div class="img-container col-6 col-md-3 p-2">
                    <img class="img-fluid lazyload" src="image/4.1 copy.png" alt="Dịch vụ giao nhanh" data-original="4.1.png">
                </div>
                <div class="img-container col-6 col-md-3 p-2">
                    <img class="img-fluid lazyload" src="image/4.2.png" alt="Dịch vụ vận chuyển" data-original="4.2.png">
                </div>
                <div class="img-container col-6 col-md-3 p-2">
                    <img class="img-fluid lazyload" src="image/4.3.png" alt="Tra cứu chuyển phát nhanh" data-original="4.3.png">
                </div>
                <div class="img-container col-6 col-md-3 p-2">
                    <img class="img-fluid lazyload" src="image/4.4.png" alt="Giao hàng nhanh giá rẻ" data-original="4.4.png">
                </div>
            </div>
        </div>
    </div>
</div>

  <!-- Footer Start -->
  <footer id="footer" class="text-center py-4 bg-dark text-light">
    <div class="container">
        <h1 class="mb-3">VH Express</h1>
        <p class="mb-3">Niềm vui của các bạn là sự tự hào của chúng tôi</p>
  
        <div class="social-links mb-4">
            <a href="#" class="me-3"><i class="fab fa-facebook fa-2x"></i></a>
            <a href="#" class="me-3"><i class="fab fa-instagram fa-2x"></i></a>
            <a href="#" class="me-3"><i class="fab fa-youtube fa-2x"></i></a>
        </div>
  
        <div class="row">
            <div class="col-md-4 mb-3">
                <h5>Liên hệ</h5>
                <p>Hotline: 0859921126</p>
                <p>Email: support@vhexpress.com</p>
            </div>
            <div class="col-md-4 mb-3">
                <h5>Thông tin</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-light">Về chúng tôi</a></li>
                    <li><a href="#" class="text-light">Điều khoản dịch vụ</a></li>
                    <li><a href="#" class="text-light">Chính sách bảo mật</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-3">
                <h5>Dịch vụ</h5>
                <ul class="list-unstyled">
                    <li><a href="giaohangnhanh.html" class="text-light">Vận chuyển nhanh</a></li>
                    <li><a href="vanchuyentietkiem.html" class="text-light">Vận chuyển tiết kiệm</a></li>
                    <li><a href="vanchuyenhoatoc.html" class="text-light">Vận chuyển hỏa tốc</a></li>
                </ul>
            </div>
        </div>
  
        <div class="credit mt-3">
            <p>Designed By <a href="#" class="text-light">Việt Hưng</a></p>
        </div>
  
        <div class="copyright">
            <p>&copy; 2024 Việt Hưng. All Rights Reserved</p>
        </div>
    </div>
  </footer>
  <!-- Footer End -->
  
  

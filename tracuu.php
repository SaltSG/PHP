
<?php
session_start();
include 'admin/config.php';

// Thêm code xử lý tra cứu
$searchResult = null;
$error = null;

if(isset($_GET['listDoCode']) && !empty($_GET['listDoCode'])) {
    $trackingCode = trim($_GET['listDoCode']);
    
    $sql = "SELECT * FROM orders WHERE tracking_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $trackingCode);
    $stmt->execute();
    $result = $stmt->get_result();
    $searchResult = $result->fetch_assoc();
    
    if(!$searchResult) {
        $error = "Không tìm thấy đơn hàng với mã vận đơn này";
    }
}
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

    

    <div class="w-100 position-relative mx-auto py-5 bg_tra_cuu_van_don">
        <div class="search_order mx-auto">
            <h1 class="title d-flex justify-content-center">Tra cứu vận đơn</h1>
            <form id="form_search_order" method="GET" action="#">
                <div class="row mx-auto px-0 py-3">
                    <div class="col-md-10 col-9">
                        <input type="text" class="form-control" id="listDoCode" name="listDoCode" placeholder="Nhập mã vận đơn" fdprocessedid="zjdgna">
                    </div>
                    <div class="col-md-2 col-3">
                        <button type="submit" class="btn btn_search_order w-100 d-flex align-items-center justify-content-center" fdprocessedid="uh546">
                            Tìm
                        </button>
                    </div>
                    <div class="warning_text mt-2 pl-3">
                        Nhập mã tối đa 10 kí tự.
                    </div>
                </div>
            </form>

            <!-- Thêm phần hiển thị kết quả ngay sau form -->
            <?php if(isset($_GET['listDoCode'])): ?>
                <div class="container mt-4">
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php elseif($searchResult): ?>
                        <div class="card">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">Thông tin đơn hàng #<?php echo htmlspecialchars($searchResult['tracking_code']); ?></h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="mb-3">Thông tin người gửi:</h6>
                                        <p><strong>Tên:</strong> <?php echo htmlspecialchars($searchResult['sender_name']); ?></p>
                                        <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($searchResult['sender_phone']); ?></p>
                                        <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($searchResult['sender_address']); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-3">Thông tin người nhận:</h6>
                                        <p><strong>Tên:</strong> <?php echo htmlspecialchars($searchResult['receiver_name']); ?></p>
                                        <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($searchResult['receiver_phone']); ?></p>
                                        <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($searchResult['receiver_address']); ?></p>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <hr>
                                        <h6 class="mb-3">Thông tin hàng hóa:</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Chiều dài:</strong> <?php echo htmlspecialchars($searchResult['length']); ?> cm</p>
                                                <p><strong>Chiều rộng:</strong> <?php echo htmlspecialchars($searchResult['width']); ?> cm</p>
                                                <p><strong>Chiều cao:</strong> <?php echo htmlspecialchars($searchResult['height']); ?> cm</p>
                                                <p><strong>Cân nặng:</strong> <?php echo htmlspecialchars($searchResult['weight']); ?> kg</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Giá trị hàng:</strong> <?php echo number_format($searchResult['value'], 0, ',', '.'); ?> VNĐ</p>
                                                <p><strong>Phí vận chuyển:</strong> <?php echo number_format($searchResult['shipping_fee'], 0, ',', '.'); ?> VNĐ</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <hr>
                                        <p><strong>Phương thức thanh toán:</strong> <?php echo htmlspecialchars($searchResult['payment_type']); ?></p>
                                        <p><strong>Thời gian tạo đơn:</strong> <?php echo date('d/m/Y H:i', strtotime($searchResult['created_at'])); ?></p>
                                    </div>
                                </div>
                               
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
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
  
  

</body>
</html>

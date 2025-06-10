<?php
session_start();
include 'admin/config.php';
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

        /* Style cho nút scroll top đã có */
        #scrollTopBtn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: none;
            z-index: 99;
            border: none;
            outline: none;
            background-color: #000000c0;
            color: white;
            cursor: pointer;
            padding: 10px;
            border-radius: 50%;
        }

        #scrollTopBtn:hover {
            background-color: #555;
        }

        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
        }

        .modal {
            z-index: 1050;
        }

        body.modal-open {
            overflow: hidden;
        }

    </style>
</head>
<body>
    
<div class="all-content">
  <div class="header d-flex align-items-center justify-content-between">
    <a class="navbar-brand" href="index.php">VH Express</a>
    <form class="d-flex ms-auto align-items-center" id="searchForm" method="GET">
        <input class="form-control me-2" type="search" name="tracking_code" placeholder="Tra cứu vận đơn" aria-label="Search" style="max-width: 200px;">
        <button class="btn btn-outline-success" type="submit">Tra cứu</button>
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

    <!-- Home Section Start -->
    <div class="Trangchu">
        <div class="content">
            <h5>Đến với chúng tôi</h5>
            <h1><span class="changecontent"></span></h1>
            <p>Niềm vui của các bạn là sự tự hào của chúng tôi ( Hotline: 0859921126 )</p>
            <a href="taovandon.php">Tạo vận đơn</a>
        </div>
    </div>
    <!-- Home Section End -->

    <!-- Section DichVu Start -->
    <section class="dichvu" id="dichvu">
        <div class="container">
            <div class="main-txt">
                <h1><span>D</span>ịch vụ</h1>
            </div>

            <div class="row" style="margin-top: 30px;">
                <!-- Cards for services -->
                <div class="col-md-4 py-3 py-md-0">
                    <div class="card">
                        <img src="image/Screenshot 2024-10-12 214234.png" alt="">
                        <div class="card-body">
                            <h3>Vận chuyển nhanh</h3>
                            <p>Dịch vụ giao hàng đến người nhận. Phù hợp với chuyển phát nhanh thư từ,
                               bưu phẩm ngay trong ngày với mức phí hợp lý.</p>
                            <div class="star">
                                <i class="fa-solid fa-star checked"></i>
                                <i class="fa-solid fa-star checked"></i>
                                <i class="fa-solid fa-star checked"></i>
                                <i class="fa-solid fa-star "></i>
                                <i class="fa-solid fa-star "></i>
                            </div>
                            <h6><a href="giaohangnhanh.php" class="learn-more-link">Tìm hiểu thêm</a></h6>
                            <a href="taovandon.php" class="btn-taovandon">Tạo vận đơn</a> 
                        </div>
                    </div>
                </div>

                <div class="col-md-4 py-3 py-md-0">
                    <div class="card">
                        <img src="image/Screenshot 2024-10-12 214310.png" alt="">
                        <div class="card-body">
                            <h3>Vận chuyển tiết kiệm</h3>
                            <p>Dịch vụ giao hàng với cước phí vận chuyển rẻ, thời gian giao hàng hợp lý. Giải pháp vận chuyển tiết kiệm tối đa chi phí,
                               phù hợp với khách gửi hàng số lượng lớn mỗi ngày.</p>
                            <div class="star">
                                <i class="fa-solid fa-star checked"></i>
                                <i class="fa-solid fa-star checked"></i>
                                <i class="fa-solid fa-star checked"></i>
                                <i class="fa-solid fa-star checked "></i>
                                <i class="fa-solid fa-star "></i>
                            </div>
                            <h6><a href="vanchuyentietkiem.php" class="learn-more-link">Tìm hiểu thêm</a></h6>
                            <a href="taovandon.php" class="btn-taovandon">Tạo vận đơn</a> 
                        </div>
                    </div>
                </div>

                <div class="col-md-4 py-3 py-md-0">
                    <div class="card">
                        <img src="image/Screenshot 2024-10-12 214441.png" alt="">
                        <div class="card-body">
                            <h3>Vận chuyển hỏa tốc</h3>
                            <p>Dịch vụ giao hàng theo khung thời gian yêu cầu của khách hàng. 
                              Hình thức giao nhận chủ động, khách hàng không mất thời gian chờ đợi.</p>
                            <div class="star">
                                <i class="fa-solid fa-star checked"></i>
                                <i class="fa-solid fa-star checked"></i>
                                <i class="fa-solid fa-star checked"></i>
                                <i class="fa-solid fa-star "></i>
                                <i class="fa-solid fa-star "></i>
                            </div>
                            <h6><a href="vanchuyenhoatoc.php" class="learn-more-link">Tìm hiểu thêm</a></h6>
                            <a href="taovandon.php" class="btn-taovandon">Tạo vận đơn</a> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



 
    <div class="container">
    <div class="module-title__all text-center mb-11 wow fadeInUp" style="visibility: visible; animation-name: fadeInUp;">
        Điều gì tạo nên <span>VH Express</span> khác biệt
    </div>

        <div class="row mobule-different">
            <div class="col-lg-7">
                <div class="image text-center">
                    <img src="https://ship60.com/wp-content/uploads/2022/05/Asset-11-copy.png" class="img-fluid entered lazyloaded" alt="Dễ dàng tích hợp các nền tảng bán hàng" data-lazy-src="https://ship60.com/wp-content/uploads/2022/05/Asset-11-copy.png" data-ll-status="loaded">
                </div>
            </div>
            <div class="col-lg-5 ct-text wow fadeInUp" style="visibility: visible; animation-name: fadeInUp;">
                <h3 class="title-big__all bd-top f-bold cl-title mb-40">Dễ dàng tích hợp các nền tảng bán hàng</h3>
                <div class="sort_content fz-24 cl-title">
                    Hệ thống VH Express giúp bạn dễ dàng tích hợp với các kênh bán hàng nền tảng thương mại điện tử, quản lý bán hàng và các hệ thống khác giúp bạn quản lý đồng bộ đơn hàng từ nhiều kênh bán khác nhau.
                </div>
            </div>
        </div>

        <div class="row mobule-different">
          <div class="col-lg-5 ct-text wow fadeInUp" style="visibility: visible; animation-name: fadeInUp;">
              <h3 class="title-big__all bd-top f-bold cl-title mb-40">Tích hợp các dịch vụ giao hàng, giao ngay trong ngày</h3>
              <div class="sort_content fz-24 cl-title">
                  Các đơn hàng của bạn trên các kênh bán hàng khác nhau được quản lý tập trung, giao ngay trong ngày giúp tăng tỉ lệ giao hàng thành công và trải nghiệm khách hàng tốt nhất.
              </div>
          </div>
          <div class="col-lg-7">
              <div class="image text-center">
                  <img src="https://ship60.com/wp-content/uploads/2021/12/khac-biet-3.svg" class="img-fluid entered lazyloaded" alt="Tích hợp các dịch vụ giao hàng, giao ngay trong ngày" data-lazy-src="https://ship60.com/wp-content/uploads/2021/12/khac-biet-3.svg" data-ll-status="loaded">
              </div>
          </div>
      </div>
      

        <div class="row mobule-different">
            <div class="col-lg-7">
                <div class="image text-center">
                    <img src="https://ship60.com/wp-content/uploads/2021/12/khac-biet-4-1.svg" class="img-fluid entered lazyloaded" alt="Mở rộng hoạt động kinh doanh khắp Việt Nam" data-lazy-src="https://ship60.com/wp-content/uploads/2021/12/khac-biet-4-1.svg" data-ll-status="loaded">
                </div>
            </div>
            <div class="col-lg-5 ct-text wow fadeInUp" style="visibility: visible; animation-name: fadeInUp;">
                <h3 class="title-big__all bd-top f-bold cl-title mb-40">Mở rộng hoạt động kinh doanh khắp Việt Nam</h3>
                <div class="sort_content fz-24 cl-title">
                    Mang sản phẩm của bạn đến gần khách hàng ở bất kì đâu, không tốn chi phí thuê nhân viên và mặt bằng hàng tháng. Hệ thống kho hàng và giao vận của VH Express giúp bn quản lý và phân phối đn hàng nhanh nhất đến khách hàng trên toàn quốc.
                </div>
            </div>
        </div>
    </div>
</div>


<script>
  window.onscroll = function() {scrollFunction()};

  function scrollFunction() {
    if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
      document.getElementById("scrollTopBtn").style.display = "block";
    } else {
      document.getElementById("scrollTopBtn").style.display = "none";
    }
  }

  function topFunction() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
  }
</script>




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

<!-- Kết quả tra cứu vận ơn -->
<?php
if (isset($_GET['tracking_code']) && !empty($_GET['tracking_code'])) {
    $tracking_code = trim($_GET['tracking_code']);
    
    $sql = "SELECT * FROM orders WHERE tracking_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $tracking_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        ?>
        <div class="modal fade show" id="orderModal" tabindex="-1" style="display: block;">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Mã vận đơn: <?php echo htmlspecialchars($tracking_code); ?></h5>
                        <button type="button" class="btn-close btn-close-white" onclick="window.location.href='index.php'" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Thông tin người gửi:</h6>
                                <p><strong>Họ tên:</strong> <?php echo htmlspecialchars($order['sender_name']); ?></p>
                                <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['sender_phone']); ?></p>
                                <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['sender_address']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h6>Thông tin người nhận:</h6>
                                <p><strong>Họ tên:</strong> <?php echo htmlspecialchars($order['receiver_name']); ?></p>
                                <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['receiver_phone']); ?></p>
                                <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['receiver_address']); ?></p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Thông tin hàng hóa:</h6>
                                <p><strong>Kích thước:</strong> <?php echo $order['length'] . 'x' . $order['width'] . 'x' . $order['height']; ?> cm</p>
                                <p><strong>Cân nặng:</strong> <?php echo $order['weight']; ?> kg</p>
                                <p><strong>Giá trị hàng:</strong> <?php echo number_format($order['value'], 0, ',', '.'); ?> VNĐ</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Thông tin vận chuyển:</h6>
                                <p><strong>Phí vận chuyển:</strong> <?php echo number_format($order['shipping_fee'], 0, ',', '.'); ?> VNĐ</p>
                                <p><strong>Hình thức thanh toán:</strong> <?php echo htmlspecialchars($order['payment_type']); ?></p>
                                <p><strong>Ngày tạo đơn:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='index.php'">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
        <?php
    } else {
        echo '<div class="modal fade show" id="orderModal" tabindex="-1" style="display: block;">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">Không tìm thấy</h5>
                            <button type="button" class="btn-close btn-close-white" onclick="window.location.href=\'index.php\'" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Không tìm thấy vận đơn với mã ' . htmlspecialchars($tracking_code) . '</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="window.location.href=\'index.php\'">Đóng</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-backdrop fade show"></div>';
    }
}
?>
</body>
</html>
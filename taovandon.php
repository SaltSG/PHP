<?php include 'components/zalo-button.php'; ?>

<?php
session_start();
include 'admin/config.php';


// Hàm tạo mã vận đơn
function generateTrackingCode() {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = '';
    for ($i = 0; $i < 5; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $code;
}

// Tinh phi van chuyen
function calculateShippingFee($weight, $length, $width, $height, $value, $serviceType) {
    // Tính thể tích và trọng lượng quy đổi
    $volume = $length * $width * $height;
    $volumetricWeight = $volume / 6000; // Hệ số quy đổi 6000
    
    // Lấy trọng lượng tính phí (lấy giá trị lớn hơn giữa cân nặng thực và cân nặng quy đổi)
    $chargeableWeight = max((float)$weight, $volumetricWeight);
    
    // Xác định đơn giá cơ bản theo loại dịch vụ
    switch($serviceType) {
        case 'express':
            $basePrice = 50000; // Giá cơ bản cho hỏa tốc
            $pricePerExtraKg = 5000;
            $serviceName = 'Hỏa tốc';
            break;
        case 'fast':
            $basePrice = 40000; // Giá cơ bản cho nhanh
            $pricePerExtraKg = 4000;
            $serviceName = 'Nhanh';
            break;
        case 'economy':
            $basePrice = 30000; // Giá cơ bản cho tiết kiệm
            $pricePerExtraKg = 3000;
            $serviceName = 'Tiết kiệm';
            break;
        default:
            $basePrice = 15000;
            $pricePerExtraKg = 3000;
            $serviceName = 'Tiết kiệm';
    }
    
    // Tính cước chính
    if ($chargeableWeight <= 1) {
        $mainFee = $basePrice;
    } else {
        $extraWeight = ceil($chargeableWeight - 1); // Làm tròn lên số kg vượt quá
        $mainFee = $basePrice + ($extraWeight * $pricePerExtraKg);
    }
    
    // Tính VAT (10%)
    $vatFee = $mainFee * 0.1;
    
    // Tính tổng cước
    $totalFee = $mainFee + $vatFee;
    
    return [
        'mainFee' => $mainFee,
        'vatFee' => $vatFee,
        'totalFee' => $totalFee,
        'serviceName' => $serviceName
    ];
}

// Khởi tạo biến
$orderDetails = null;

// Xử lý khi form được submit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Lấy dữ liệu từ form
    $senderName = $_POST['senderName'] ?? '';
    $senderPhone = $_POST['senderPhone'] ?? '';
    $senderAddress = $_POST['senderAddress'] ?? '';
    $senderProvince = $_POST['senderProvince'] ?? '';
    $senderDistrict = $_POST['senderDistrict'] ?? '';
    $senderWard = $_POST['senderWard'] ?? '';
    
    $receiverName = $_POST['receiverName'] ?? '';
    $receiverPhone = $_POST['receiverPhone'] ?? '';
    $receiverAddress = $_POST['receiverAddress'] ?? '';
    $receiverProvince = $_POST['receiverProvince'] ?? '';
    $receiverDistrict = $_POST['receiverDistrict'] ?? '';
    $receiverWard = $_POST['receiverWard'] ?? '';
    
    $length = $_POST['length'] ?? '';
    $width = $_POST['width'] ?? '';
    $height = $_POST['height'] ?? '';
    $weight = $_POST['weight'] ?? '';
    $value = $_POST['value'] ?? '';
    $paymentType = $_POST['paymentType'] ?? '';
    

    // Tạo mã vận đơn
    $trackingCode = generateTrackingCode();
    
    // Lấy tên tỉnh/thành phố, quận/huyện, phường/xã từ database
    $senderProvinceQuery = $conn->query("SELECT name FROM province WHERE province_id = '$senderProvince'");
    $senderDistrictQuery = $conn->query("SELECT name FROM district WHERE district_id = '$senderDistrict'");
    $senderWardQuery = $conn->query("SELECT name FROM wards WHERE wards_id = '$senderWard'");

    $receiverProvinceQuery = $conn->query("SELECT name FROM province WHERE province_id = '$receiverProvince'");
    $receiverDistrictQuery = $conn->query("SELECT name FROM district WHERE district_id = '$receiverDistrict'");
    $receiverWardQuery = $conn->query("SELECT name FROM wards WHERE wards_id = '$receiverWard'");

    // Lấy tên từ kết quả truy vấn
    $senderProvinceName = $senderProvinceQuery->fetch_assoc()['name'] ?? '';
    $senderDistrictName = $senderDistrictQuery->fetch_assoc()['name'] ?? '';
    $senderWardName = $senderWardQuery->fetch_assoc()['name'] ?? '';

    $receiverProvinceName = $receiverProvinceQuery->fetch_assoc()['name'] ?? '';
    $receiverDistrictName = $receiverDistrictQuery->fetch_assoc()['name'] ?? '';
    $receiverWardName = $receiverWardQuery->fetch_assoc()['name'] ?? '';

    // Tạo địa chỉ đầy đủ
    $senderFullAddress = $senderAddress . ', ' . $senderWardName . ', ' . $senderDistrictName . ', ' . $senderProvinceName;
    $receiverFullAddress = $receiverAddress . ', ' . $receiverWardName . ', ' . $receiverDistrictName . ', ' . $receiverProvinceName;

    // Xử lý hình thức thanh toán
    $paymentTypeText = '';
    switch($paymentType) {
        case 'sender':
            $paymentTypeText = 'Người gửi thanh toán';
            break;
        case 'receiver':
            $paymentTypeText = 'Người nhận thanh toán';
            break;
        case 'cod':
            $paymentTypeText = 'Thanh toán COD';
            break;
        default:
            $paymentTypeText = $paymentType;
    }

    // Tính phí vận chuyển
    $shippingFees = calculateShippingFee(
        $_POST['weight'] ?? 0,
        $_POST['length'] ?? 0,
        $_POST['width'] ?? 0,
        $_POST['height'] ?? 0,
        $_POST['value'] ?? 0,
        $_POST['serviceType'] ?? 'economy'
    );

    // Lưu thông tin vào database
    $sql = "INSERT INTO orders (
        tracking_code, 
        sender_name, 
        sender_phone, 
        sender_address,
        receiver_name, 
        receiver_phone, 
        receiver_address, 
        payment_type,
        length,
        width,
        height,
        weight,
        value,
        shipping_fee,
        created_at,
        user_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssddddddi", 
        $trackingCode,
        $senderName,
        $senderPhone,
        $senderFullAddress,
        $receiverName,
        $receiverPhone,
        $receiverFullAddress,
        $paymentTypeText,
        $length,
        $width,
        $height,
        $weight,
        $value,
        $shippingFees['totalFee'],
        $_SESSION["id"]
    );
    
    if ($stmt->execute()) {
        // Lưu thông tin để hiển thị modal
        $orderDetails = [
            'trackingCode' => $trackingCode,
            'sender' => [
                'name' => $senderName,
                'phone' => $senderPhone,
                'address' => $senderFullAddress
            ],
            'receiver' => [
                'name' => $receiverName,
                'phone' => $receiverPhone,
                'address' => $receiverFullAddress
            ],
            'package' => [
                'dimensions' => $length . 'x' . $width . 'x' . $height . ' cm',
                'weight' => $weight . ' kg',
                'value' => number_format($value, 0, ',', '.') . ' VNĐ'
            ],
            'shipping' => [
                'mainFee' => number_format($shippingFees['mainFee'], 0, ',', '.') . ' VNĐ',
                'vatFee' => number_format($shippingFees['vatFee'], 0, ',', '.') . ' VNĐ',
                'totalFee' => number_format($shippingFees['totalFee'], 0, ',', '.') . ' VNĐ'
            ],
            'paymentType' => $paymentTypeText
        ];
        // Hiển thị modal
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                var orderModal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
                orderModal.show();
            });
        </script>";
    }
}

// Truy vấn danh sách tnh/thành phố, quận/huyện, phường/xã
$provinces = $conn->query("SELECT province_id, name FROM province");
$districts = $conn->query("SELECT district_id, province_id, name FROM district");
$wards = $conn->query("SELECT wards_id, district_id, name FROM wards");
?>
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

     
        .btn-custom-orange {
    background-color: #ffa500;
    color: white;
    padding: 8px 20px;
    border: none;
    transition: all 0.3s ease;
}

.btn-custom-orange:hover {
    background-color: #ffa500;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
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

    

<!-- Form Section Start -->
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <div class="container my-5">
        <div class="row">
            <!-- Sender Information -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-user"></i> Người gửi</h5>
                        <div class="mb-3">
                            <label for="senderName" class="form-label">Họ tên người gửi:</label>
                            <input type="text" class="form-control" id="senderName" name="senderName" 
                                   value="<?php echo isset($_POST['senderName']) ? htmlspecialchars($_POST['senderName']) : ''; ?>" placeholder="Họ tên">
                        </div>
                        <div class="mb-3">
                            <label for="senderPhone" class="form-label">Số điện thoại người gửi:</label>
                            <input type="text" class="form-control" id="senderPhone" name="senderPhone" 
                                   value="<?php echo isset($_POST['senderPhone']) ? htmlspecialchars($_POST['senderPhone']) : ''; ?>" placeholder="Số điện thoại">
                        </div>
                        <div class="mb-3">
                            <label for="senderAddress" class="form-label">Địa chỉ:</label>
                            <input type="text" class="form-control" id="senderAddress" name="senderAddress" 
                                   value="<?php echo isset($_POST['senderAddress']) ? htmlspecialchars($_POST['senderAddress']) : ''; ?>" placeholder="Nhập địa chỉ">
                        </div>
                        <div class="row">
                            <div class="col">
                                <select class="form-select" id="senderProvince" name="senderProvince" onchange="updateDistricts(this.value, 'senderDistrict', 'senderWard')">
                                    <option selected>Chọn Tỉnh/Thành phố</option>
                                    <?php while($row = $provinces->fetch_assoc()): ?>
                                        <option value="<?php echo $row['province_id']; ?>" 
                                                <?php echo (isset($_POST['senderProvince']) && $_POST['senderProvince'] == $row['province_id']) ? 'selected' : ''; ?>>
                                            <?php echo $row['name']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col">
                                <select class="form-select" id="senderDistrict" name="senderDistrict" onchange="updateWards(this.value, 'senderWard')">
                                    <option selected>Chọn Quận/Huyện</option>
                                    <?php 
                                    if(isset($_POST['senderProvince'])) {
                                        $districtQuery = $conn->query("SELECT district_id, name FROM district WHERE province_id = '{$_POST['senderProvince']}'");
                                        while($row = $districtQuery->fetch_assoc()): ?>
                                            <option value="<?php echo $row['district_id']; ?>" 
                                                    <?php echo (isset($_POST['senderDistrict']) && $_POST['senderDistrict'] == $row['district_id']) ? 'selected' : ''; ?>>
                                                <?php echo $row['name']; ?>
                                            </option>
                                        <?php endwhile;
                                    } ?>
                                </select>
                            </div>
                            <div class="col">
                                <select class="form-select" id="senderWard" name="senderWard">
                                    <option selected>Chọn Phường/Xã</option>
                                    <?php 
                                    if(isset($_POST['senderDistrict'])) {
                                        $wardQuery = $conn->query("SELECT wards_id, name FROM wards WHERE district_id = '{$_POST['senderDistrict']}'");
                                        while($row = $wardQuery->fetch_assoc()): ?>
                                            <option value="<?php echo $row['wards_id']; ?>" 
                                                    <?php echo (isset($_POST['senderWard']) && $_POST['senderWard'] == $row['wards_id']) ? 'selected' : ''; ?>>
                                                <?php echo $row['name']; ?>
                                            </option>
                                        <?php endwhile;
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Receiver Information -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-user"></i> Người nhận</h5>
                        <div class="mb-3">
                            <label for="receiverName" class="form-label">Họ tên người nhận:</label>
                            <input type="text" class="form-control" id="receiverName" name="receiverName" 
                                   value="<?php echo isset($_POST['receiverName']) ? htmlspecialchars($_POST['receiverName']) : ''; ?>" placeholder="Họ tên">
                        </div>
                        <div class="mb-3">
                            <label for="receiverPhone" class="form-label">Số điện thoại người nhận:</label>
                            <input type="text" class="form-control" id="receiverPhone" name="receiverPhone" 
                                   value="<?php echo isset($_POST['receiverPhone']) ? htmlspecialchars($_POST['receiverPhone']) : ''; ?>" placeholder="Số điện thoại">
                        </div>
                        <div class="mb-3">
                            <label for="receiverAddress" class="form-label">Địa chỉ:</label>
                            <input type="text" class="form-control" id="receiverAddress" name="receiverAddress" 
                                   value="<?php echo isset($_POST['receiverAddress']) ? htmlspecialchars($_POST['receiverAddress']) : ''; ?>" placeholder="Nhập địa chỉ">
                        </div>
                        <div class="row">
                            <div class="col">
                                <select class="form-select" id="receiverProvince" name="receiverProvince" onchange="updateDistricts(this.value, 'receiverDistrict', 'receiverWard')">
                                    <option selected>Chọn Tỉnh/Thành phố</option>
                                    <?php 
                                    $provinces->data_seek(0); // Reset con trỏ về đầu kết quả
                                    while($row = $provinces->fetch_assoc()): ?>
                                        <option value="<?php echo $row['province_id']; ?>" 
                                                <?php echo (isset($_POST['receiverProvince']) && $_POST['receiverProvince'] == $row['province_id']) ? 'selected' : ''; ?>>
                                            <?php echo $row['name']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col">
                                <select class="form-select" id="receiverDistrict" name="receiverDistrict" onchange="updateWards(this.value, 'receiverWard')">
                                    <option selected>Chọn Quận/Huyện</option>
                                    <?php 
                                    if(isset($_POST['receiverProvince'])) {
                                        $districtQuery = $conn->query("SELECT district_id, name FROM district WHERE province_id = '{$_POST['receiverProvince']}'");
                                        while($row = $districtQuery->fetch_assoc()): ?>
                                            <option value="<?php echo $row['district_id']; ?>" 
                                                    <?php echo (isset($_POST['receiverDistrict']) && $_POST['receiverDistrict'] == $row['district_id']) ? 'selected' : ''; ?>>
                                                <?php echo $row['name']; ?>
                                            </option>
                                        <?php endwhile;
                                    } ?>
                                </select>
                            </div>
                            <div class="col">
                                <select class="form-select" id="receiverWard" name="receiverWard">
                                    <option selected>Chọn Phường/Xã</option>
                                    <?php 
                                    if(isset($_POST['receiverDistrict'])) {
                                        $wardQuery = $conn->query("SELECT wards_id, name FROM wards WHERE district_id = '{$_POST['receiverDistrict']}'");
                                        while($row = $wardQuery->fetch_assoc()): ?>
                                            <option value="<?php echo $row['wards_id']; ?>" 
                                                    <?php echo (isset($_POST['receiverWard']) && $_POST['receiverWard'] == $row['wards_id']) ? 'selected' : ''; ?>>
                                                <?php echo $row['name']; ?>
                                            </option>
                                        <?php endwhile;
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

       
        <!-- Service and Payment Information Section -->
    <div class="row">
        <!-- Service Information -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-box"></i> Thông tin dịch vụ - hàng hóa</h5>
                    <div class="mb-3">
                        <label class="form-label">Dịch vụ:</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="serviceType" id="economy" value="economy" 
                                onchange="this.form.submit()" <?php echo (!isset($_POST['serviceType']) || $_POST['serviceType'] == 'economy') ? 'checked' : ''; ?>>
                            <label class="btn btn-outline-primary" for="economy">Tiết kiệm</label>
                            
                            <input type="radio" class="btn-check" name="serviceType" id="fast" value="fast" 
                                onchange="this.form.submit()" <?php echo (isset($_POST['serviceType']) && $_POST['serviceType'] == 'fast') ? 'checked' : ''; ?>>
                            <label class="btn btn-outline-primary" for="fast">Nhanh</label>
                            
                            <input type="radio" class="btn-check" name="serviceType" id="express" value="express" 
                                onchange="this.form.submit()" <?php echo (isset($_POST['serviceType']) && $_POST['serviceType'] == 'express') ? 'checked' : ''; ?>>
                            <label class="btn btn-outline-primary" for="express">Hỏa tốc</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="weight" class="form-label">Trọng lượng (kg):</label>
                            <input type="number" class="form-control" id="weight" name="weight" 
                                   value="<?php echo isset($_POST['weight']) ? htmlspecialchars($_POST['weight']) : ''; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="quantity" class="form-label">Số kiện:</label>
                            <input type="number" class="form-control" id="quantity" placeholder="1">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="value" class="form-label">Giá trị hàng hóa:</label>
                            <input type="number" class="form-control" id="value" name="value" 
                                   value="<?php echo isset($_POST['value']) ? htmlspecialchars($_POST['value']) : ''; ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="length" class="form-label">Dài (cm):</label>
                            <input type="number" class="form-control" id="length" name="length" 
                                   value="<?php echo isset($_POST['length']) ? htmlspecialchars($_POST['length']) : ''; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="width" class="form-label">Rộng (cm):</label>
                            <input type="number" class="form-control" id="width" name="width" 
                                   value="<?php echo isset($_POST['width']) ? htmlspecialchars($_POST['width']) : ''; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="height" class="form-label">Cao (cm):</label>
                            <input type="number" class="form-control" id="height" name="height" 
                                   value="<?php echo isset($_POST['height']) ? htmlspecialchars($_POST['height']) : ''; ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Giá cước vận chuyển</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-2">Giá trị hàng: 
                                            <span><?php 
                                                if (isset($_POST['value'])) {
                                                    echo number_format($_POST['value'], 0, ',', '.') . ' VNĐ';
                                                } else {
                                                    echo '0 VNĐ';
                                                }
                                            ?></span>
                                        </p>
                                        <p class="mb-2">Cước chính: 
                                            <span><?php 
                                                if (isset($shippingFees)) {
                                                    echo number_format($shippingFees['mainFee'], 0, ',', '.') . ' VNĐ';
                                                } else {
                                                    echo '0 VNĐ';
                                                }
                                            ?></span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-2">VAT (10%): 
                                            <span><?php 
                                                if (isset($shippingFees)) {
                                                    echo number_format($shippingFees['vatFee'], 0, ',', '.') . ' VNĐ';
                                                } else {
                                                    echo '0 VNĐ';
                                                }
                                            ?></span>
                                        </p>
                                        <p class="mb-2 fw-bold">Tổng cước: 
                                            <span class="text-primary"><?php 
                                                if (isset($shippingFees)) {
                                                    echo number_format($shippingFees['totalFee'], 0, ',', '.') . ' VNĐ';
                                                } else {
                                                    echo '0 VNĐ';
                                                }
                                            ?></span>
                                        </p>
                                    </div>
                                </div>
                                <div class="alert alert-info mt-2" role="alert">
                                    <small>* Giá cước sẽ được tính khi bạn nhập đầy đủ thông tin và bấm "Tạo vận đơn"</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-money-bill"></i> Thông tin thanh toán</h5>
                    <div class="mb-3">
                        <label class="form-label">Hình thức thanh toán:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="paymentType" id="sender" 
                                   value="Người gửi thanh toán" <?php echo (!isset($_POST['paymentType']) || $_POST['paymentType'] == 'Người gửi thanh toán') ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="sender">Người gửi thanh toán</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="paymentType" id="receiver" 
                                   value="Người nhận thanh toán" <?php echo (isset($_POST['paymentType']) && $_POST['paymentType'] == 'Người nhận thanh toán') ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="receiver">Người nhận thanh toán</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="paymentType" id="cod" 
                                   value="Thanh toán COD" <?php echo (isset($_POST['paymentType']) && $_POST['paymentType'] == 'Thanh toán COD') ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="cod">Thanh toán COD</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="promoCode" class="form-label">Mã khuyến mãi (Nếu có):</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="promoCode" placeholder="Nhập mã khuyến mãi">
                            <button class="btn btn-primary" type="button">Áp dụng</button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="deliveryInstructions" class="form-label">Yêu cầu khi giao:</label>
                        <textarea class="form-control" id="deliveryInstructions" rows="3" placeholder="Nhập yêu cầu đặc biệt"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>


      <!-- Submit Button -->
    <div class="text-center">
        <button type="submit" name="submit" class="btn btn-custom-orange">Tạo vận đơn</button>
    </div>
</form>

<!-- Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Tạo vận đơn thành công</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (isset($orderDetails)): ?>
                <div class="alert alert-success">
                    <strong>Mã vận đơn:</strong> <?php echo $orderDetails['trackingCode']; ?>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-user"></i> Thông tin người gửi</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-1"><strong>Họ tên:</strong> <?php echo htmlspecialchars($orderDetails['sender']['name']); ?></p>
                                <p class="mb-1"><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($orderDetails['sender']['phone']); ?></p>
                                <p class="mb-1"><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($orderDetails['sender']['address']); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-user"></i> Thông tin người nhận</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-1"><strong>Họ tên:</strong> <?php echo htmlspecialchars($orderDetails['receiver']['name']); ?></p>
                                <p class="mb-1"><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($orderDetails['receiver']['phone']); ?></p>
                                <p class="mb-1"><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($orderDetails['receiver']['address']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-box"></i> Thông tin hàng hóa và cước phí</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Kích thước:</strong> <?php echo htmlspecialchars($orderDetails['package']['dimensions']); ?></p>
                                <p class="mb-1"><strong>Cân nặng:</strong> <?php echo htmlspecialchars($orderDetails['package']['weight']); ?></p>
                                <p class="mb-1"><strong>Giá trị hàng:</strong> <?php echo htmlspecialchars($orderDetails['package']['value']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Cước chính:</strong> <?php echo htmlspecialchars($orderDetails['shipping']['mainFee']); ?></p>
                                <p class="mb-1"><strong>VAT:</strong> <?php echo htmlspecialchars($orderDetails['shipping']['vatFee']); ?></p>
                                <p class="mb-1"><strong>Tổng cước:</strong> <span class="text-primary fw-bold"><?php echo htmlspecialchars($orderDetails['shipping']['totalFee']); ?></span></p>
                                <p class="mb-1"><strong>Hình thức thanh toán:</strong> <?php echo htmlspecialchars($orderDetails['paymentType']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print"></i> In vận đơn
                </button>
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
  
  

<script>
    // function validateForm() {
    //     // Lấy giá trị các trường trong form
    //     const senderName = document.getElementById("senderName").value.trim();
    //     const senderPhone = document.getElementById("senderPhone").value.trim();
    //     const senderAddress = document.getElementById("senderAddress").value.trim();
    //     const receiverName = document.getElementById("receiverName").value.trim();
    //     const receiverPhone = document.getElementById("receiverPhone").value.trim();
    //     const receiverAddress = document.getElementById("receiverAddress").value.trim();

    //     // Kiểm tra các trường bắt buộc
    //     if (!senderName || !senderPhone || !senderAddress || !receiverName || !receiverPhone || !receiverAddress) {
    //         alert("Vui lòng điền đầy đủ thông tin bắt buộc.");
    //         return false;
    //     }

    //     // Nếu mọi thứ đều ổn, hiển thị thông báo thành công
    //     alert("Vận đơn đã được tạo thành công!");
    // }

    const districts = <?= json_encode($districts->fetch_all(MYSQLI_ASSOC)) ?>;
    const wards = <?= json_encode($wards->fetch_all(MYSQLI_ASSOC)) ?>;

    function updateDistricts(provinceId, districtSelectId, wardSelectId) {
        const districtSelect = document.getElementById(districtSelectId);
        districtSelect.innerHTML = '<option selected>Chọn Quận/Huyện</option>';

        districts.forEach(district => {
            if (district.province_id == provinceId) {
                const option = document.createElement('option');
                option.value = district.district_id;
                option.textContent = district.name;
                districtSelect.appendChild(option);
            }
        });

        // Reset wards when province changes
        document.getElementById(wardSelectId).innerHTML = '<option selected>Chọn Phường/Xã</option>';
    }

    function updateWards(districtId, wardSelectId) {
        const wardSelect = document.getElementById(wardSelectId);
        wardSelect.innerHTML = '<option selected>Chọn Phường/Xã</option>';

        wards.forEach(ward => {
            if (ward.district_id == districtId) {
                const option = document.createElement('option');
                option.value = ward.wards_id;
                option.textContent = ward.name;
                wardSelect.appendChild(option);
            }
        });
    }
</script>

</body>
</html>

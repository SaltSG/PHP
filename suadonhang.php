<?php
session_start();
include 'admin/config.php';

// Kiểm tra đăng nhập
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: Login/login.php");
    exit;
}

// Lấy thông tin đơn hàng
if(isset($_GET['id'])) {
    $order_id = $_GET['id'];
    
    $sql = "SELECT * FROM orders WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows == 0) {
        header("location: quanlydonhang.php");
        exit;
    }
    
    $order = $result->fetch_assoc();
}

// Xử lý cập nhật đơn hàng
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $receiver_name = $_POST['receiver_name'];
    $receiver_phone = $_POST['receiver_phone'];
    $receiver_address = $_POST['receiver_address'];
    
    $sql = "UPDATE orders SET 
            receiver_name = ?,
            receiver_phone = ?,
            receiver_address = ?
            WHERE id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", 
        $receiver_name,
        $receiver_phone,
        $receiver_address,
        $order_id
    );
    
    if($stmt->execute()) {
        header("location: quanlydonhang.php?success=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa đơn hàng - VH Express</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'components/header.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-4">Sửa đơn hàng</h2>
        
        <form method="POST" action="">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Mã vận đơn: <?php echo htmlspecialchars($order['tracking_code']); ?></h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label><strong>Người gửi:</strong></label>
                                <input type="text" name="sender_name" class="form-control" value="<?php echo htmlspecialchars($order['sender_name']); ?>">
                            </div>
                            <div class="mb-3">
                                <label><strong>Số điện thoại người gửi:</strong></label>
                                <input type="text" name="sender_phone" class="form-control" value="<?php echo htmlspecialchars($order['sender_phone']); ?>">
                            </div>
                            <div class="mb-3">
                                <label><strong>Địa chỉ người gửi:</strong></label>
                                <input type="text" name="sender_address" class="form-control" value="<?php echo htmlspecialchars($order['sender_address']); ?>">
                            </div>
                            <div class="mb-3">
                                <label><strong>Người nhận:</strong></label>
                                <input type="text" name="receiver_name" class="form-control" value="<?php echo htmlspecialchars($order['receiver_name']); ?>">
                            </div>
                            <div class="mb-3">
                                <label><strong>Địa chỉ:</strong></label>
                                <input type="text" name="receiver_address" class="form-control" value="<?php echo htmlspecialchars($order['receiver_address']); ?>">
                            </div>
                            <div class="mb-3">
                                <label><strong>Số điện thoại:</strong></label>
                                <input type="text" name="receiver_phone" class="form-control" value="<?php echo htmlspecialchars($order['receiver_phone']); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Ngày tạo:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                            <p><strong>Phí vận chuyển:</strong> <?php echo number_format($order['shipping_fee'], 0, ',', '.'); ?> VNĐ</p>
                            <p><strong>Trạng thái thanh toán:</strong> <?php echo htmlspecialchars($order['payment_type']); ?></p>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <button type="submit" name="update_order" class="btn btn-success btn-sm">
                        <i class="fas fa-save"></i> Cập nhật
                    </button>
                    <button type="submit" name="delete_order" class="btn btn-danger btn-sm">
                        <i class="fas fa-trash"></i> Xóa
                    </button>
                </div>
            </div>
        </form>
    </div>

    <?php include 'components/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
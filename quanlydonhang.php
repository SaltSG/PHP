<?php
session_start();
include 'admin/config.php';

// Kiểm tra đăng nhập
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: Login/login.php");
    exit;
}

// Xử lý xóa đơn hàng
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_order'])) {
    $order_id = $_POST['order_id'];

    $sql = "DELETE FROM orders WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        header("location: quanlydonhang.php?delete_success=1");
        exit;
    } else {
        echo "Lỗi khi xóa đơn hàng.";
    }
}

// Lấy danh sách đơn hàng
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION["id"]);
$stmt->execute();
$result = $stmt->get_result();

// Tính tổng cước vận chuyển
$sql_total = "SELECT SUM(shipping_fee) as total_fee FROM orders WHERE user_id = ?";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param("i", $_SESSION["id"]);
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$total_fee = $result_total->fetch_assoc()['total_fee'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn hàng - VH Express</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <?php include 'components/header.php'; ?>
</head>
<body>
    
    <div class="container mt-4">  
        <h2 class="mb-4">Quản lý đơn hàng</h2>
        
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Thống kê cước vận chuyển</h5>
                <p class="card-text">
                    <strong>Tổng cước vận chuyển đã thu: </strong>
                    <span class="text-primary"><?php echo number_format($total_fee, 0, ',', '.'); ?> VNĐ</span>
                </p>
            </div>
        </div>
        
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($order = $result->fetch_assoc()): ?>
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Mã vận đơn: <?php echo htmlspecialchars($order['tracking_code']); ?></h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Người gửi:</strong> <?php echo htmlspecialchars($order['sender_name']); ?></p>
                                <p><strong>Số điện thoại người gửi:</strong> <?php echo htmlspecialchars($order['sender_phone']); ?></p>
                                <p><strong>Địa chỉ người gửi:</strong> <?php echo htmlspecialchars($order['sender_address']); ?></p>
                                <p><strong>Người nhận:</strong> <?php echo htmlspecialchars($order['receiver_name']); ?></p>
                                <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['receiver_address']); ?></p>
                                <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['receiver_phone']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Ngày tạo:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                                <p><strong>Phí vận chuyển:</strong> <?php echo number_format($order['shipping_fee'], 0, ',', '.'); ?> VNĐ</p>
                                <p><strong>Trạng thái thanh toán:</strong> <?php echo htmlspecialchars($order['payment_type']); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="suadonhang.php?id=<?php echo $order['id']; ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                        <form action="" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa đơn hàng này?');">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <button type="submit" name="delete_order" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-info">Bạn chưa có đơn hàng nào.</div>
        <?php endif; ?>
    </div>

    <?php include 'components/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
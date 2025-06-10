<?php
session_start();
include 'admin/config.php';
?>
<?php include 'components/zalo-button.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên hệ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <?php include 'components/header.php'; ?>
    <style>
        .contact-info {
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .map-container {
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .contact-info a {
            color: black; /* Đặt màu đen cho liên kết */
            text-decoration: none; /* Bỏ gạch chân */
        }
        .contact-info a:hover {
            text-decoration: underline; /* Gạch chân khi hover */
        }
        .feedback-form {
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .btn-yellow {
            background-color: #ffc107; /* Màu vàng */
            color: white; /* Màu chữ trắng */
        }
        .btn-yellow:hover {
            background-color: #e0a800; /* Màu vàng đậm khi hover */
            color: white; /* Màu chữ trắng */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mt-4 text-center">Liên Hệ Chúng Tôi</h2>
        
        <div class="row">
            <div class="col-md-6">
                <div class="contact-info">
                    <h5>Thông tin liên hệ</h5>
                    <p><strong>Địa chỉ:</strong> Tràng An 2, Tân Thành, Ninh Bình, Vietnam</p>
                    <p><strong>Số điện thoại:</strong> <a href="tel:0859921126">0859921126</a></p>
                    <p><strong>Email:</strong> <a href="mailto:contact@vh-express.com">contact@vh-express.com</a></p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="feedback-form">
                    <h5>Gửi Phản Hồi</h5>
                    <form action="process_feedback.php" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Nội dung phản hồi</label>
                            <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-yellow">Gửi Phản Hồi</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Google Map -->
        <div class="map-container">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3216.5225198175854!2d105.96306367452017!3d20.26453368120038!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x313679fcf3253e25%3A0xa1571cbe208a6d9c!2zMiBUcsOgbmcgQW4sIFTDom4gVGjDoG5oLCBOaW5oIELDrG5oLCBWaeG7h3QgTmFt!5e1!3m2!1svi!2s!4v1732272454992!5m2!1svi!2s" 
                width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
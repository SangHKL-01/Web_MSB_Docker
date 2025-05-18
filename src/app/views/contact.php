<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên Hệ - MSB Shop</title>
    <link rel="stylesheet" href="/public/assets/CSS/homepage.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .contact-section {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .contact-info {
            margin-top: 30px;
        }
        
        .contact-info h3 {
            margin-bottom: 15px;
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        
        .contact-info p {
            margin-bottom: 10px;
            font-size: 16px;
            line-height: 1.6;
        }
        
        .contact-info strong {
            color: #4CAF50;
            font-weight: 600;
        }
        
        .contact-map {
            margin-top: 30px;
            border-radius: 8px;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="nav-container">
            <div class="logo">
                <a href="index.php">MSB Shop</a>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Trang Chủ</a></li>
                    <li><a href="index.php?controller=Product&action=index">Sản Phẩm</a></li>
                    <li><a href="index.php?controller=Home&action=about">Giới Thiệu</a></li>
                    <li><a href="index.php?controller=Home&action=contact" class="active">Liên Hệ</a></li>
                    <li><a href="index.php?controller=Product&action=gio_hang">Giỏ Hàng <span id="cart-count">(<?= isset($cartItemCount) ? $cartItemCount : 0 ?>)</span></a></li>
                    <?php
                    if (isset($_SESSION['user'])) {
                        $avatar = !empty($_SESSION['user']['avatar']) ? '/uploads/avatars/' . htmlspecialchars($_SESSION['user']['avatar']) : '/public/assets/images/avatar.jpg';
                        echo '<li>
                                <a href="index.php?controller=user&action=profile">
                                <img src="' . $avatar . '" alt="Profile" width="40" style="border-radius: 50%; vertical-align: middle;" />
                                </a>
                            </li>';
                    } else {
                        echo '<li>
                                <a href="index.php?controller=user&action=login">
                                <img src="/public/assets/images/avatar_md.jpg" alt="" width="30" style="border-radius: 50%; vertical-align: middle;" />
                                </a>
                            </li>';
                    }
                    ?>  
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main">
        <section class="contact-section">
            <h1>Liên Hệ Với Chúng Tôi</h1>
            <p>Nếu bạn có bất kỳ câu hỏi hoặc yêu cầu nào, vui lòng liên hệ với chúng tôi qua các thông tin dưới đây.</p>
            
            <div class="contact-info">
                <h3>Thông Tin Liên Hệ</h3>
                <p><strong>Địa chỉ:</strong> 123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh</p>
                <p><strong>Điện thoại:</strong> (028) 1234 5678</p>
                <p><strong>Email:</strong> info@kheobang.com</p>
                <p><strong>Giờ làm việc:</strong> Thứ 2 - Thứ 6: 8:00 - 17:30, Thứ 7: 8:00 - 12:00</p>
            </div>
            
            <div class="contact-info">
                <h3>Dịch Vụ Khách Hàng</h3>
                <p><strong>Hotline:</strong> 1900 1234</p>
                <p><strong>Email hỗ trợ:</strong> support@kheobang.com</p>
                <p>Chúng tôi luôn sẵn sàng hỗ trợ bạn 24/7 với mọi vấn đề liên quan đến sản phẩm và dịch vụ.</p>
            </div>
            
            <div class="contact-info">
                <h3>Hợp Tác Kinh Doanh</h3>
                <p><strong>Liên hệ:</strong> Phòng Kinh doanh</p>
                <p><strong>Email:</strong> business@kheobang.com</p>
                <p><strong>Điện thoại:</strong> (028) 1234 5679</p>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <p>&copy; 2023 MSB Shop. Tất cả quyền lợi được bảo vệ.</p>
            <ul class="footer-links">
                <li>Chính Sách Bảo Mật</li>
                <li>Điều Khoản Sử Dụng</li>
                <li>FAQ</li>
                <li>Liên Hệ</li>
            </ul>
        </div>
    </footer>

</body>
</html>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Hàng Thành Công - MSB Shop</title>
    <link rel="stylesheet" href="http://localhost/WEB_MSB/public/assets/CSS/homepage.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .success-page {
            min-height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .success-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 600px;
            overflow: hidden;
            padding-bottom: 30px;
        }
        
        .success-header {
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            color: white;
            padding: 30px 0;
            text-align: center;
        }
        
        .checkmark {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            margin: 0 auto 15px;
        }
        
        .checkmark svg {
            width: 40px;
            height: 40px;
            color: #4CAF50;
        }
        
        .success-title {
            font-size: 24px;
            margin: 0;
            font-weight: 600;
        }
        
        .success-body {
            padding: 30px;
            text-align: center;
        }
        
        .order-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0 30px;
            border-left: 5px solid #4CAF50;
        }
        
        .order-number {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 5px;
            color: #333;
        }
        
        .order-date {
            color: #666;
            font-size: 14px;
        }
        
        .message {
            margin: 20px 0;
            line-height: 1.6;
            color: #555;
        }
        
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn-success {
            background-color: #4CAF50;
            color: white;
        }
        
        .btn-outline {
            background-color: transparent;
            border: 2px solid #4CAF50;
            color: #4CAF50;
        }
        
        .next-steps {
            margin-top: 25px;
            text-align: left;
            padding: 0 30px;
        }
        
        .step-list {
            padding-left: 20px;
        }
        
        .step-list li {
            margin-bottom: 10px;
            color: #555;
        }
        
        @media (max-width: 576px) {
            .action-buttons {
                flex-direction: column;
            }
            
            .action-buttons .btn {
                width: 100%;
            }
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
                    <li><a href="#">Liên Hệ</a></li>
                    <li><a href="index.php?controller=Product&action=gio_hang">Giỏ Hàng <span id="cart-count">(0)</span></a></li>
                    <?php
                    if (isset($_SESSION['user'])) {
                        echo '<li>
                                <a href="index.php?controller=user&action=profile">
                                <img src="http://localhost/WEB_MSB/public/assets/images/avatar.jpg" alt="Profile" width="40" style="border-radius: 50%; vertical-align: middle;" />
                                </a>
                            </li>';
                    } else {
                        echo '<li>
                                <a href="index.php?controller=user&action=login">
                                <img src="http://localhost/WEB_MSB/public/assets/images/avatar_md.jpg" alt="" width="30" style="border-radius: 50%; vertical-align: middle;" />
                                </a>
                            </li>';
                    }
                    ?>  
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="success-page">
        <div class="success-card">
            <div class="success-header">
                <div class="checkmark">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1 class="success-title">Đặt Hàng Thành Công!</h1>
            </div>
            
            <div class="success-body">
                <p class="message">Cảm ơn bạn đã mua sắm tại MSB Shop. Đơn hàng của bạn đã được tiếp nhận và đang được xử lý.</p>
                
                <div class="order-info">
                    <div class="order-number">Mã đơn hàng: #<?= htmlspecialchars($order_number) ?></div>
                    <div class="order-date">Ngày đặt: <?= date('d/m/Y H:i') ?></div>
                </div>
                
                <div class="next-steps">
                    <h3>Các bước tiếp theo:</h3>
                    <ol class="step-list">
                        <li>Kiểm tra email xác nhận đơn hàng của bạn</li>
                        <li>Theo dõi trạng thái đơn hàng trong phần "Lịch sử đơn hàng"</li>
                        <li>Chuẩn bị phương thức thanh toán (nếu chọn thanh toán khi nhận hàng)</li>
                    </ol>
                </div>
                
                <div class="action-buttons">
                    <a href="index.php?controller=Product&action=history" class="btn btn-success">Xem Lịch Sử Đơn Hàng</a>
                    <a href="index.php?controller=Product&action=index" class="btn btn-outline">Tiếp Tục Mua Sắm</a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <p>&copy; 2023 MSB Shop. Tất cả quyền lợi được bảo vệ.</p>
            <ul class="footer-links">
                <li><a href="#">Chính Sách Bảo Mật</a></li>
                <li><a href="#">Điều Khoản Sử Dụng</a></li>
                <li><a href="#">FAQ</a></li>
                <li><a href="#">Liên Hệ</a></li>
            </ul>
        </div>
    </footer>

    <!-- Script to update cart count dynamically -->
    <script>
        // Đặt lại số lượng giỏ hàng sau khi đặt hàng thành công
        sessionStorage.setItem("cartCount", "0");
        document.getElementById("cart-count").textContent = "(0)";
        
        // Thêm hiệu ứng đánh dấu hoàn thành
        setTimeout(function() {
            document.querySelector('.checkmark').style.transform = 'scale(1.2)';
            setTimeout(function() {
                document.querySelector('.checkmark').style.transform = 'scale(1)';
            }, 200);
        }, 500);
    </script>
</body>
</html> 
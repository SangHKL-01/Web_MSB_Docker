<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - MSB Shop</title>
    <link rel="stylesheet" href="/public/assets/CSS/homepage.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .about-section {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .about-content {
            line-height: 1.8;
            color: #333;
        }
        
        .about-message {
            margin-top: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border-left: 4px solid #4CAF50;
            font-style: italic;
        }
        
        .about-team {
            margin-top: 40px;
        }
        
        .about-team h3 {
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
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
                    <li><a href="index.php?controller=Home&action=about" class="active">Giới Thiệu</a></li>
                    <li><a href="index.php?controller=Home&action=contact">Liên Hệ</a></li>
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
        <section class="about-section">
            <h1><?= htmlspecialchars($title) ?></h1>
            
            <div class="about-content">
                <p><?= htmlspecialchars($content) ?></p>
                
                <?php if (!empty($message)): ?>
                <div class="about-message">
                    <p>Thông điệp từ khách hàng: <?= htmlspecialchars($message) ?></p>
                    <?php if (!empty($name)): ?>
                    <p>- <?= htmlspecialchars($name) ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <div class="about-team">
                    <h3>Đội ngũ của chúng tôi</h3>
                    <p>Chúng tôi là một nhóm các chuyên gia đam mê về sản phẩm chất lượng cao. Với nhiều năm kinh nghiệm trong ngành, chúng tôi cam kết mang đến cho khách hàng những sản phẩm tốt nhất với giá cả hợp lý nhất.</p>
                </div>
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
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm Kiếm Sản Phẩm - MSB Shop</title>
    <link rel="stylesheet" href="/public/assets/CSS/homepage.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
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
                                <img src="/public/assets/images/avatar.jpg" alt="Profile" width="40" style="border-radius: 50%; vertical-align: middle;" />
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

    <!-- Hero Section with Search -->
    <section class="hero" style="height: 60vh;">
        <div class="hero-content">
            <h1>Tìm Kiếm Sản Phẩm</h1>
            <p>Nhập từ khóa để tìm kiếm sản phẩm mà bạn mong muốn.</p>
            
            <div class="search-container" style="margin-top: 2rem;">
                <form method="GET" action="index.php" class="search-form">
                    <input type="hidden" name="controller" value="Product">
                    <input type="hidden" name="action" value="search">
                    <input type="text" name="keyword" placeholder="Tìm kiếm sản phẩm..." class="search-input" style="width: 400px; padding: 12px 20px; font-size: 16px;">
                    <button type="submit" class="search-button" style="padding: 12px 24px; font-size: 16px;">🔍 Tìm kiếm</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Popular Categories -->
    <section class="category-section">
        <div class="container">
            <h2 class="section-title">Danh Mục Phổ Biến</h2>
            <div class="category-grid">
                <div class="category-card" onclick="location.href='index.php?controller=Product&action=search&keyword=áo'">
                    <div class="category-icon">👕</div>
                    <h3>Áo</h3>
                </div>
                <div class="category-card" onclick="location.href='index.php?controller=Product&action=search&keyword=quần'">
                    <div class="category-icon">👖</div>
                    <h3>Quần</h3>
                </div>
                <div class="category-card" onclick="location.href='index.php?controller=Product&action=search&keyword=giày'">
                    <div class="category-icon">👟</div>
                    <h3>Giày</h3>
                </div>
                <div class="category-card" onclick="location.href='index.php?controller=Product&action=search&keyword=túi'">
                    <div class="category-icon">👜</div>
                    <h3>Túi Xách</h3>
                </div>
            </div>
        </div>
    </section>

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

    <style>
        .category-section {
            padding: 40px 0;
            background-color: #f8f9fa;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .category-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-top: 20px;
        }
        
        .category-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        
        .category-card:hover {
            transform: translateY(-5px);
        }
        
        .category-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }
        
        .category-card h3 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        
        @media (max-width: 768px) {
            .category-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</body>
</html> 
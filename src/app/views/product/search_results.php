<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết Quả Tìm Kiếm - MSB Shop</title>
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

    <!-- Mini Hero Banner -->
    <section class="hero" style="height: 25vh;">
        <div class="hero-content">
            <h1>Kết Quả Tìm Kiếm</h1>
            <p>Từ khóa: "<?= htmlspecialchars($keyword) ?>"</p>
        </div>
    </section>

    <!-- Search Form -->
    <div class="search-container">
        <form method="GET" action="index.php" class="search-form">
            <input type="hidden" name="controller" value="Product">
            <input type="hidden" name="action" value="search">
            <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" placeholder="Tìm kiếm sản phẩm..." class="search-input">
            <button type="submit" class="search-button">🔍 Tìm kiếm</button>
        </form>
    </div>

    <!-- Main Content -->
    <main class="main">
        <!-- Results Section -->
        <section class="product-section">
            <h2 class="section-title">Sản Phẩm Tìm Thấy</h2>
            <?php if (!empty($products)): ?>
                <div class="product-grid">
                    <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-img">
                            <img src="/public/assets/images/<?= isset($product['image']) ? $product['image'] : 'product_default.jpg' ?>" alt="<?= htmlspecialchars($product['name'] ?? 'Sản phẩm') ?>">
                        </div>
                        <div class="product-info">
                            <h3 class="product-title"><?= htmlspecialchars($product['name'] ?? 'Sản phẩm không tên') ?></h3>
                            <p class="product-price"><?= number_format($product['price'] ?? 0, 0, ',', '.') ?> VNĐ</p>
                            <div class="product-actions">
                                <a href="index.php?controller=Product&action=details_product&id=<?= $product['id'] ?>" class="btn btn-sm btn-primary">Xem Chi Tiết</a>
                                <form method="POST" action="index.php?controller=Product&action=insert_cart&id=<?= $product['id'] ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-sm btn-secondary">Thêm vào Giỏ</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-results">
                    <div class="empty-icon">🔍</div>
                    <h3>Không tìm thấy sản phẩm nào</h3>
                    <p>Rất tiếc, không có sản phẩm nào phù hợp với từ khóa "<?= htmlspecialchars($keyword) ?>"</p>
                    <div class="empty-actions">
                        <a href="index.php?controller=Product&action=index" class="btn btn-primary">Xem tất cả sản phẩm</a>
                        <a href="index.php?controller=Product&action=search" class="btn btn-outline">Tìm kiếm lại</a>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <!-- Related Categories -->
        <?php if (!empty($products)): ?>
        <section class="related-section">
            <h2 class="section-title">Có thể bạn quan tâm</h2>
            <div class="category-list">
                <a href="index.php?controller=Product&action=search&keyword=áo" class="category-tag">Áo</a>
                <a href="index.php?controller=Product&action=search&keyword=quần" class="category-tag">Quần</a>
                <a href="index.php?controller=Product&action=search&keyword=giày" class="category-tag">Giày</a>
                <a href="index.php?controller=Product&action=search&keyword=túi" class="category-tag">Túi xách</a>
                <a href="index.php?controller=Product&action=search&keyword=đồng hồ" class="category-tag">Đồng hồ</a>
            </div>
        </section>
        <?php endif; ?>
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

    <style>
        .empty-results {
            text-align: center;
            padding: 50px 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .empty-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }
        
        .empty-results h3 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
        }
        
        .empty-results p {
            margin-bottom: 30px;
            color: #6c757d;
        }
        
        .empty-actions {
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        
        .btn-outline {
            background-color: transparent;
            border: 2px solid #3498db;
            color: #3498db;
        }
        
        .related-section {
            padding: 40px 0;
            background-color: #f8f9fa;
        }
        
        .category-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            margin: 20px 0;
        }
        
        .category-tag {
            display: inline-block;
            padding: 8px 15px;
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            color: #333;
            font-size: 14px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .category-tag:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            color: #3498db;
        }
    </style>
</body>
</html> 
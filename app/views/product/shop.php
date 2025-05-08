<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Sản Phẩm - Kheo Báng Shop</title>
    <link rel="stylesheet" href="http://localhost/WEB_MSB/public/assets/CSS/homepage.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .alert {
            padding: 10px 15px;
            margin-bottom: 15px;
            border-radius: 4px;
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            max-width: 300px;
            animation: fadeIn 0.3s, fadeOut 0.5s 3s forwards;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="nav-container">
            <div class="logo">
                <a href="index.php">Kheo Báng</a>
            </div>
            <nav>
            <ul>
          <li><a href="index.php">Trang Chủ</a></li>
          <li><a href="index.php?controller=Product&action=index">Sản Phẩm</a></li>
          <li><a href="index.php?controller=Home&action=about">Giới Thiệu</a></li>
          <li><a href="index.php?controller=Home&action=contact">Liên Hệ</a></li>
          <li><a href="index.php?controller=Product&action=gio_hang">Giỏ Hàng <span id="cart-count">(<?= isset($cartItemCount) ? $cartItemCount : 0 ?>)</span></a></li>
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

    <!-- Hiển thị thông báo khi thêm vào giỏ hàng thành công -->
    <?php if (isset($_SESSION['cart_message'])): ?>
    <div class="alert alert-success" id="cart-alert">
        <?= $_SESSION['cart_message'] ?>
    </div>
    <?php 
        // Xóa thông báo sau khi hiển thị
        unset($_SESSION['cart_message']);
    endif; ?>

    <!-- Hero Section - Mini banner for product page -->
    <section class="hero" style="height: 30vh;">
        <div class="hero-content">
            <h1>Sản Phẩm Của Chúng Tôi</h1>
            <p>Khám phá các sản phẩm chất lượng cao với giá cả phải chăng.</p>
        </div>
    </section>

    <!-- Search Form -->
    <div class="search-container">
        <form method="GET" action="index.php" class="search-form">
            <input type="hidden" name="controller" value="Product">
            <input type="hidden" name="action" value="search">
            <input type="text" name="keyword" placeholder="Tìm kiếm sản phẩm..." class="search-input" required>
            <button type="submit" class="search-button">🔍 Tìm kiếm</button>
        </form>
        
        <!-- Thêm các danh mục phổ biến -->
        <div class="popular-tags">
            <span>Phổ biến:</span>
            <a href="index.php?controller=Product&action=search&keyword=laptop">laptop</a>
            <a href="index.php?controller=Product&action=search&keyword=apple watch">apple watch</a>
            <a href="index.php?controller=Product&action=search&keyword=smartphone">smartphone</a>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main">
        <!-- Filter/Sort Options (Optional) -->
        <div class="filter-section" style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
            <form method="GET" action="index.php">
                <input type="hidden" name="controller" value="Product">
                <input type="hidden" name="action" value="index">
                <select name="sort_by" class="search-input" style="width: auto; margin-right: 10px;">
                    <option value="name">Tên sản phẩm</option>
                    <option value="price">Giá</option>
                </select>
                <select name="sort_dir" class="search-input" style="width: auto; margin-right: 10px;">
                    <option value="ASC">Tăng dần</option>
                    <option value="DESC">Giảm dần</option>
                </select>
                <button type="submit" class="btn btn-sm btn-primary">Sắp xếp</button>
            </form>
        </div>

        <!-- All Products Section -->
        <section class="product-section">
            <h2 class="section-title">Tất Cả Sản Phẩm</h2>
            <?php if (!empty($products)): ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-img">
                        <img src="http://localhost/WEB_MSB/public/assets/images/<?= isset($product['image']) ? $product['image'] : 'product_default.jpg' ?>" alt="<?= htmlspecialchars($product['name'] ?? 'Sản phẩm') ?>">
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
                            <a href="index.php?controller=Product&action=checkout&id=<?= $product['id'] ?>" class="btn btn-sm btn-accent">Mua ngay</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div style="text-align: center; padding: 30px; background-color: #f8f9fa; border-radius: 8px;">
                <p>Không tìm thấy sản phẩm nào.</p>
            </div>
            <?php endif; ?>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <p>&copy; 2023 Kheo Báng Shop. Tất cả quyền lợi được bảo vệ.</p>
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
        // Kiểm tra giỏ hàng trong session và cập nhật số lượng
        if (sessionStorage.getItem("cartCount")) {
            document.getElementById("cart-count").textContent = `(${sessionStorage.getItem("cartCount")})`;
        }
        
        // Tự động ẩn thông báo sau 3 giây
        const alertElement = document.getElementById('cart-alert');
        if (alertElement) {
            setTimeout(() => {
                alertElement.style.display = 'none';
            }, 3000);
        }
    </script>
</body>
</html>
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
        #buyNowModal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0; top: 0;
            width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.35);
            align-items: center; justify-content: center;
            transition: background 0.2s;
        }
        #buyNowModal.active {
            display: flex;
            animation: fadeInModalBg 0.2s;
        }
        @keyframes fadeInModalBg {
            from { background: rgba(0,0,0,0); }
            to { background: rgba(0,0,0,0.35); }
        }
        #buyNowModal .modal-content {
            background: #fff;
            padding: 32px 28px 24px 28px;
            border-radius: 16px;
            min-width: 320px;
            max-width: 95vw;
            margin: auto;
            position: relative;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
            animation: modalPopIn 0.25s;
        }
        @keyframes modalPopIn {
            from { transform: scale(0.85); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        #buyNowModal h3 {
            margin-top: 0;
            margin-bottom: 18px;
            font-size: 1.25em;
            font-weight: 600;
            text-align: center;
        }
        #buyNowModal input[type=number] {
            width: 90px;
            font-size: 1.5em;
            padding: 6px 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            text-align: center;
            margin-bottom: 10px;
            outline: none;
            transition: border 0.2s;
        }
        #buyNowModal input[type=number]:focus {
            border: 1.5px solid #007bff;
        }
        #buyNowModal .modal-actions {
            margin-top: 18px;
            display: flex;
            justify-content: center;
            gap: 12px;
        }
        #buyNowModal .btn {
            min-width: 90px;
            font-size: 1em;
            border-radius: 6px;
            padding: 7px 0;
        }
        #closeBuyNowModal {
            background: #eee;
            color: #333;
            border: none;
        }
        #closeBuyNowModal:hover {
            background: #ddd;
        }
        #buyNowModal .close-x {
            position: absolute;
            top: 10px; right: 14px;
            font-size: 1.3em;
            color: #888;
            cursor: pointer;
            transition: color 0.2s;
            font-weight: bold;
        }
        #buyNowModal .close-x:hover {
            color: #e74c3c;
        }
        #buyNowModal .modal-product-img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 10px;
            display: block;
            margin-left: auto;
            margin-right: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        #buyNowModal .modal-product-name {
            font-weight: 600;
            font-size: 1.1em;
            margin-bottom: 4px;
            text-align: center;
        }
        #buyNowModal .modal-product-desc {
            font-size: 0.98em;
            color: #555;
            margin-bottom: 12px;
            text-align: center;
            max-height: 60px;
            overflow: auto;
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
                        $avatar = !empty($_SESSION['user']['avatar']) ? 'uploads/avatars/' . htmlspecialchars($_SESSION['user']['avatar']) : 'public/assets/images/avatar.jpg';
                        echo '<li>
                                <a href="index.php?controller=user&action=profile">
                                <img src="' . $avatar . '" alt="Profile" width="40" style="border-radius: 50%; vertical-align: middle;" />
                                </a>
                            </li>';
                    } else {
                        echo '<li>
                                <a href="index.php?controller=user&action=login">
                                <img src="public/assets/images/avatar_md.jpg" alt="" width="30" style="border-radius: 50%; vertical-align: middle;" />
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
    <!-- Hiển thị thông báo lỗi -->
    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger" id="cart-alert-error">
        <?= $_SESSION['error'] ?>
    </div>
    <?php 
        unset($_SESSION['error']);
    endif; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Chào mừng đến với Kheo Báng Shop</h1>
            <p>Khám phá các sản phẩm chất lượng cao với giá cả phải chăng. Đổi mới mỗi ngày, nâng tầm trải nghiệm.</p>
            <a href="index.php?controller=Product&action=index" class="btn">Khám phá ngay</a>
        </div>
    </section>

    <!-- Search Form -->
    <div class="search-container">
        <form method="GET" action="index.php" class="search-form">
            <input type="hidden" name="controller" value="Product">
            <input type="hidden" name="action" value="search">
            <input type="text" name="keyword" placeholder="Tìm kiếm sản phẩm..." class="search-input">
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
        <!-- Newest Products Section -->
        <?php if (!empty($newestProducts)): ?>
        <section class="product-section">
            <h2 class="section-title">Sản Phẩm Mới Nhất</h2>
            <div class="product-grid">
                <?php foreach ($newestProducts as $product): ?>
                <div class="product-card" data-stock="<?= isset($product['stock']) ? $product['stock'] : 9999 ?>">
                    <div class="product-img">
                        <img src="http://localhost/WEB_MSB/public/assets/images/<?= isset($product['image']) ? $product['image'] : 'product_default.jpg' ?>" alt="<?= htmlspecialchars($product['name'] ?? 'Sản phẩm') ?>">
                        <span class="product-badge">NEW</span>
                    </div>
                    <div class="product-info">
                        <h3 class="product-title"><?= htmlspecialchars($product['name'] ?? 'Sản phẩm không tên') ?></h3>
                        <p class="product-price"><?= number_format($product['price'] ?? 0, 0, ',', '.') ?> VNĐ</p>
                        <div class="product-actions">
                            <a href="index.php?controller=Product&action=details_product&id=<?= $product['id'] ?>" class="btn btn-sm btn-primary">Xem Chi Tiết</a>
                            <?php if ($product['stock'] > 0): ?>
                                <form method="POST" action="index.php?controller=Product&action=insert_cart&id=<?= $product['id'] ?>" class="add-to-cart-form" data-product-id="<?= $product['id'] ?>" style="display:inline;">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="button" class="btn btn-sm btn-secondary add-to-cart-btn-modal" data-product-id="<?= $product['id'] ?>">Thêm vào Giỏ</button>
                                </form>
                                <button type="button" class="btn btn-sm btn-accent buy-now-btn" data-product-id="<?= $product['id'] ?>">Mua ngay</button>
                            <?php else: ?>
                                <span class="btn btn-sm" style="background-color: #ccc; cursor: not-allowed;">Hết hàng</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Featured Products Section -->
        <?php if (!empty($featuredProducts)): ?>
        <section class="product-section">
            <h2 class="section-title">Sản Phẩm Nổi Bật</h2>
            <div class="product-grid">
                <?php foreach ($featuredProducts as $product): ?>
                <div class="product-card" data-stock="<?= isset($product['stock']) ? $product['stock'] : 9999 ?>">
                    <div class="product-img">
                        <img src="http://localhost/WEB_MSB/public/assets/images/<?= isset($product['image']) ? $product['image'] : 'product_default.jpg' ?>" alt="<?= htmlspecialchars($product['name'] ?? 'Sản phẩm') ?>">
                    </div>
                    <div class="product-info">
                        <h3 class="product-title"><?= htmlspecialchars($product['name'] ?? 'Sản phẩm không tên') ?></h3>
                        <p class="product-price"><?= number_format($product['price'] ?? 0, 0, ',', '.') ?> VNĐ</p>
                        <div class="product-actions">
                            <a href="index.php?controller=Product&action=details_product&id=<?= $product['id'] ?>" class="btn btn-sm btn-primary">Xem Chi Tiết</a>
                            <?php if ($product['stock'] > 0): ?>
                                <form method="POST" action="index.php?controller=Product&action=insert_cart&id=<?= $product['id'] ?>" class="add-to-cart-form" data-product-id="<?= $product['id'] ?>" style="display:inline;">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="button" class="btn btn-sm btn-secondary add-to-cart-btn-modal" data-product-id="<?= $product['id'] ?>">Thêm vào Giỏ</button>
                                </form>
                                <button type="button" class="btn btn-sm btn-accent buy-now-btn" data-product-id="<?= $product['id'] ?>">Mua ngay</button>
                            <?php else: ?>
                                <span class="btn btn-sm" style="background-color: #ccc; cursor: not-allowed;">Hết hàng</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Recently Viewed Products Section -->
        <?php if (!empty($recentProducts)): ?>
        <section class="product-section">
            <h2 class="section-title">Sản Phẩm Đã Xem Gần Đây</h2>
            <div class="product-grid">
                <?php foreach ($recentProducts as $product): ?>
                <div class="product-card" data-stock="<?= isset($product['stock']) ? $product['stock'] : 9999 ?>">
                    <div class="product-img">
                        <img src="http://localhost/WEB_MSB/public/assets/images/<?= isset($product['image']) ? $product['image'] : 'product_default.jpg' ?>" alt="<?= htmlspecialchars($product['name'] ?? 'Sản phẩm') ?>">
                    </div>
                    <div class="product-info">
                        <h3 class="product-title"><?= htmlspecialchars($product['name'] ?? 'Sản phẩm không tên') ?></h3>
                        <p class="product-price"><?= number_format($product['price'] ?? 0, 0, ',', '.') ?> VNĐ</p>
                        <div class="product-actions">
                            <a href="index.php?controller=Product&action=details_product&id=<?= $product['id'] ?>" class="btn btn-sm btn-primary">Xem Chi Tiết</a>
                            <?php if ($product['stock'] > 0): ?>
                                <form method="POST" action="index.php?controller=Product&action=insert_cart&id=<?= $product['id'] ?>" class="add-to-cart-form" data-product-id="<?= $product['id'] ?>" style="display:inline;">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="button" class="btn btn-sm btn-secondary add-to-cart-btn-modal" data-product-id="<?= $product['id'] ?>">Thêm vào Giỏ</button>
                                </form>
                                <button type="button" class="btn btn-sm btn-accent buy-now-btn" data-product-id="<?= $product['id'] ?>">Mua ngay</button>
                            <?php else: ?>
                                <span class="btn btn-sm" style="background-color: #ccc; cursor: not-allowed;">Hết hàng</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <p>&copy; 2023 Kheo Báng Shop. Tất cả quyền lợi được bảo vệ.</p>
            <ul class="footer-links">
                <li>Chính Sách Bảo Mật</li>
                <li>Điều Khoản Sử Dụng</li>
                <li>FAQ</li>
                <li>Liên Hệ</li>
            </ul>
        </div>
    </footer>

    <!-- Modal nhập số lượng cho Mua ngay -->
    <div id="buyNowModal">
        <div class="modal-content">
            <span class="close-x" id="closeBuyNowModalX">&times;</span>
            <img id="modalProductImg" class="modal-product-img" src="" alt="Ảnh sản phẩm">
            <div class="modal-product-name" id="modalProductName"></div>
            <div class="modal-product-desc" id="modalProductDesc"></div>
            <h3>Nhập số lượng muốn mua</h3>
            <form id="buyNowForm" style="margin-bottom:0; text-align:center;">
                <div style="display:flex; justify-content:center; align-items:center; gap:8px; margin-bottom:10px;">
                    <button type="button" id="decreaseBuyNowQuantity" style="width:32px; height:32px; font-size:1.3em; border-radius:6px; border:1px solid #ccc; background:#f5f5f5;">-</button>
                    <input type="number" id="buyNowQuantity" name="quantity" value="1" min="1" step="1" style="width:90px; font-size:1.5em; text-align:center;">
                    <button type="button" id="increaseBuyNowQuantity" style="width:32px; height:32px; font-size:1.3em; border-radius:6px; border:1px solid #ccc; background:#f5f5f5;">+</button>
                </div>
                <input type="hidden" id="buyNowProductId" name="id" value="">
                <div class="modal-actions">
                    <button type="submit" class="btn btn-sm btn-accent">Xác nhận</button>
                    <button type="button" id="closeBuyNowModal" class="btn btn-sm">Hủy</button>
                </div>
            </form>
        </div>
    </div>
    <script>
    // Lấy dữ liệu sản phẩm từ HTML để truyền vào modal
    var productData = {};
    document.querySelectorAll('.buy-now-btn').forEach(function(btn) {
        var card = btn.closest('.product-card');
        if (card) {
            var id = btn.getAttribute('data-product-id');
            var img = card.querySelector('img');
            var name = card.querySelector('.product-title');
            var desc = card.querySelector('.product-info p');
            productData[id] = {
                img: img ? img.src : '',
                name: name ? name.textContent : '',
                desc: desc ? desc.textContent : ''
            };
        }
    });
    var buyNowBtns = document.querySelectorAll('.buy-now-btn');
    var buyNowModal = document.getElementById('buyNowModal');
    var buyNowForm = document.getElementById('buyNowForm');
    var buyNowProductId = document.getElementById('buyNowProductId');
    var buyNowQuantity = document.getElementById('buyNowQuantity');
    var closeBuyNowModal = document.getElementById('closeBuyNowModal');
    var closeBuyNowModalX = document.getElementById('closeBuyNowModalX');
    var modalProductImg = document.getElementById('modalProductImg');
    var modalProductName = document.getElementById('modalProductName');
    var modalProductDesc = document.getElementById('modalProductDesc');
    var increaseBtn = document.getElementById('increaseBuyNowQuantity');
    var decreaseBtn = document.getElementById('decreaseBuyNowQuantity');
    var currentStock = 9999;

    function openBuyNowModal(productId) {
        buyNowProductId.value = productId;
        buyNowQuantity.value = 1;
        // Hiển thị thông tin sản phẩm
        if (productData[productId]) {
            modalProductImg.src = productData[productId].img;
            modalProductName.textContent = productData[productId].name;
            modalProductDesc.textContent = productData[productId].desc;
            // Lấy tồn kho từ đúng product-card
            var btn = document.querySelector('.buy-now-btn[data-product-id="' + productId + '"]');
            var card = btn ? btn.closest('.product-card[data-stock]') : null;
            if (card) {
                currentStock = parseInt(card.getAttribute('data-stock')) || 9999;
                buyNowQuantity.max = currentStock;
                buyNowQuantity.setAttribute('max', currentStock);
                if (currentStock <= 0) {
                    buyNowQuantity.value = 0;
                    buyNowQuantity.disabled = true;
                    increaseBtn.disabled = true;
                    decreaseBtn.disabled = true;
                } else {
                    buyNowQuantity.disabled = false;
                    increaseBtn.disabled = false;
                    decreaseBtn.disabled = false;
                }
            }
        } else {
            modalProductImg.src = '';
            modalProductName.textContent = '';
            modalProductDesc.textContent = '';
            buyNowQuantity.max = 9999;
            buyNowQuantity.disabled = false;
            increaseBtn.disabled = false;
            decreaseBtn.disabled = false;
        }
        buyNowModal.classList.add('active');
        buyNowQuantity.focus();
    }
    function closeBuyNow() {
        buyNowModal.classList.remove('active');
    }
    buyNowBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var productId = btn.getAttribute('data-product-id');
            openBuyNowModal(productId);
        });
    });
    closeBuyNowModal.addEventListener('click', closeBuyNow);
    closeBuyNowModalX.addEventListener('click', closeBuyNow);
    buyNowModal.addEventListener('click', function(e) {
        if (e.target === buyNowModal) closeBuyNow();
    });
    buyNowForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var productId = buyNowProductId.value;
        var quantity = buyNowQuantity.value;
        if (!quantity || quantity < 1) quantity = 1;
        window.location.href = 'index.php?controller=Product&action=buy_now&id=' + encodeURIComponent(productId) + '&quantity=' + encodeURIComponent(quantity);
    });
    increaseBtn.addEventListener('click', function() {
        var val = parseInt(buyNowQuantity.value) || 1;
        if (val < currentStock) {
            buyNowQuantity.value = val + 1;
        }
    });
    decreaseBtn.addEventListener('click', function() {
        var val = parseInt(buyNowQuantity.value) || 1;
        if (val > 1) {
            buyNowQuantity.value = val - 1;
        }
    });
    buyNowQuantity.addEventListener('input', function() {
        var val = parseInt(buyNowQuantity.value) || 1;
        if (val > currentStock) buyNowQuantity.value = currentStock;
        if (val < 1) buyNowQuantity.value = 1;
    });
    buyNowQuantity.addEventListener('change', function() {
        var val = parseInt(buyNowQuantity.value) || 1;
        if (val > currentStock) buyNowQuantity.value = currentStock;
        if (val < 1) buyNowQuantity.value = 1;
    });
    document.querySelectorAll('.increase-qty-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = btn.getAttribute('data-product-id');
            var input = document.getElementById('qty-input-' + id);
            var max = parseInt(input.getAttribute('max')) || 9999;
            var val = parseInt(input.value) || 1;
            if (val < max) input.value = val + 1;
        });
    });
    document.querySelectorAll('.decrease-qty-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = btn.getAttribute('data-product-id');
            var input = document.getElementById('qty-input-' + id);
            var val = parseInt(input.value) || 1;
            if (val > 1) input.value = val - 1;
        });
    });
    document.querySelectorAll('.qty-input').forEach(function(input) {
        input.addEventListener('input', function() {
            var max = parseInt(input.getAttribute('max')) || 9999;
            var val = parseInt(input.value) || 1;
            if (val > max) input.value = max;
            if (val < 1) input.value = 1;
        });
    });
    // Thêm modal chọn số lượng cho Thêm vào giỏ hàng
    var addToCartBtns = document.querySelectorAll('.add-to-cart-btn-modal');
    addToCartBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var productId = btn.getAttribute('data-product-id');
            openBuyNowModal(productId); // Dùng lại modal của Mua ngay
            // Đổi nút xác nhận trong modal thành "Thêm vào giỏ hàng"
            var confirmBtn = buyNowForm.querySelector('button[type="submit"]');
            confirmBtn.textContent = 'Thêm vào Giỏ';
            // Gắn sự kiện submit mới cho modal
            buyNowForm.onsubmit = function(e) {
                e.preventDefault();
                var quantity = buyNowQuantity.value;
                if (!quantity || quantity < 1) quantity = 1;
                // Submit form ẩn tương ứng với sản phẩm
                var form = document.querySelector('.add-to-cart-form[data-product-id="' + productId + '"]');
                if (form) {
                    form.querySelector('input[name="quantity"]').value = quantity;
                    form.submit();
                }
                closeBuyNow();
            };
        });
    });
    // Khi nhấn nút Mua ngay thì modal vẫn là Xác nhận
    buyNowBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var productId = btn.getAttribute('data-product-id');
            openBuyNowModal(productId);
            var confirmBtn = buyNowForm.querySelector('button[type="submit"]');
            confirmBtn.textContent = 'Xác nhận';
            buyNowForm.onsubmit = function(e) {
                e.preventDefault();
                var quantity = buyNowQuantity.value;
                if (!quantity || quantity < 1) quantity = 1;
                window.location.href = 'index.php?controller=Product&action=buy_now&id=' + encodeURIComponent(productId) + '&quantity=' + encodeURIComponent(quantity);
            };
        });
    });
    </script>
</body>
</html>

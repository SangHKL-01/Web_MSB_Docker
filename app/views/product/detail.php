<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chi Tiết Sản Phẩm - Kheo Báng Shop</title>
  <link rel="stylesheet" href="/WEB_MSB/public/assets/CSS/homepage.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
  <style>
    .alert {
      padding: 15px 20px;
      margin-bottom: 15px;
      border-radius: 6px;
      position: fixed;
      top: 80px;
      right: 20px;
      z-index: 1000;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      max-width: 350px;
      animation: fadeIn 0.3s, fadeOut 0.5s 3s forwards;
    }
    .alert-success {
      background-color: #d4edda !important;
      color: #155724 !important;
      border: 1px solid #c3e6cb !important;
    }
    .alert-error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeOut {
      from { opacity: 1; }
      to { opacity: 0; }
    }
    .quantity-control {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
    }
    .quantity-btn {
      background: #eee;
      border: none;
      width: 30px;
      height: 30px;
      font-size: 16px;
      cursor: pointer;
      border-radius: 4px;
    }
    .quantity-input {
      width: 60px;
      text-align: center;
      margin: 0 10px;
      border: 1px solid #ddd;
      border-radius: 4px;
      padding: 5px;
    }
    .add-to-cart-btn {
      background: #4a6cf7;
      color: white;
      border: none;
      padding: 12px 24px;
      font-size: 16px;
      border-radius: 8px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: all 0.3s ease;
    }
    .add-to-cart-btn:hover {
      background: #3a5bd9;
      transform: translateY(-2px);
      box-shadow: 0 6px 12px rgba(0,0,0,0.1);
    }
    .add-to-cart-btn:active {
      transform: translateY(0);
    }
    .cart-icon {
      font-size: 20px;
    }
    .product-price {
      font-size: 24px;
      color: #e53e3e;
      font-weight: bold;
      margin-bottom: 20px;
    }
    .product-stock {
      color: #48bb78;
      font-weight: 500;
      margin-bottom: 15px;
    }
    
    /* CSS bổ sung cho trang chi tiết sản phẩm */
    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 15px;
    }
    
    .mx-auto {
      margin-left: auto;
      margin-right: auto;
    }
    
    .px-4 {
      padding-left: 1rem;
      padding-right: 1rem;
    }
    
    .py-8 {
      padding-top: 2rem;
      padding-bottom: 2rem;
    }
    
    .max-w-4xl {
      max-width: 56rem;
    }
    
    .p-8 {
      padding: 2rem;
    }
    
    .bg-white {
      background-color: white;
    }
    
    .rounded-xl {
      border-radius: 0.75rem;
    }
    
    .shadow-lg {
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    .mb-8 {
      margin-bottom: 2rem;
    }
    
    .flex {
      display: flex;
    }
    
    .flex-col {
      flex-direction: column;
    }
    
    .gap-10 {
      gap: 2.5rem;
    }
    
    .w-full {
      width: 100%;
    }
    
    .h-80 {
      height: 20rem;
    }
    
    .object-cover {
      object-fit: cover;
    }
    
    .space-y-4 > * + * {
      margin-top: 1rem;
    }
    
    .text-3xl {
      font-size: 1.875rem;
      line-height: 2.25rem;
    }
    
    .text-gray-700 {
      color: #4a5568;
    }
    
    .text-lg {
      font-size: 1.125rem;
      line-height: 1.75rem;
    }
    
    .font-bold {
      font-weight: 700;
    }
    
    .text-gray-900 {
      color: #1a202c;
    }
    
    .mt-4 {
      margin-top: 1rem;
    }
    
    .text-blue-600 {
      color: #3182ce;
    }
    
    .hover\:underline:hover {
      text-decoration: underline;
    }
    
    .mt-10 {
      margin-top: 2.5rem;
    }
    
    .text-2xl {
      font-size: 1.5rem;
      line-height: 2rem;
    }
    
    .mb-6 {
      margin-bottom: 1.5rem;
    }
    
    .grid {
      display: grid;
    }
    
    .grid-cols-1 {
      grid-template-columns: repeat(1, minmax(0, 1fr));
    }
    
    .gap-6 {
      gap: 1.5rem;
    }
    
    .h-48 {
      height: 12rem;
    }
    
    .p-4 {
      padding: 1rem;
    }
    
    .text-center {
      text-align: center;
    }
    
    .inline-block {
      display: inline-block;
    }
    
    .bg-blue-600 {
      background-color: #3182ce;
    }
    
    .text-white {
      color: white;
    }
    
    .px-6 {
      padding-left: 1.5rem;
      padding-right: 1.5rem;
    }
    
    .py-3 {
      padding-top: 0.75rem;
      padding-bottom: 0.75rem;
    }
    
    .bg-blue-700 {
      background-color: #2b6cb0;
    }
    
    .transition {
      transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
      transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
      transition-duration: 150ms;
    }
    
    /* Responsive */
    @media (min-width: 768px) {
      .md\:flex-row {
        flex-direction: row;
      }
      
      .md\:w-1\/2 {
        width: 50%;
      }
      
      .md\:grid-cols-4 {
        grid-template-columns: repeat(4, minmax(0, 1fr));
      }
    }
    
    @media (min-width: 640px) {
      .sm\:grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    /* Modal nhập số lượng cho Mua ngay */
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
                        echo '<li><a href="index.php?controller=user&action=profile"><img src="' . $avatar . '" alt="Profile" width="40" style="border-radius: 50%; vertical-align: middle;" /></a></li>';
                    } else {
                        echo '<li><a href="index.php?controller=user&action=login"><img src="public/assets/images/avatar_md.jpg" alt="" width="30" style="border-radius: 50%; vertical-align: middle;" /></a></li>';
                    }
                    ?>
        </ul>
      </nav>
    </div>
  </header>

  <!-- Hiển thị thông báo -->
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
    
  <div class="container mx-auto px-4 py-8">
    <!-- Chi tiết sản phẩm -->
    <main class="max-w-4xl mx-auto p-8 bg-white rounded-xl shadow-lg mb-8">
      <div class="flex flex-col md:flex-row gap-10">
        <!-- Hình ảnh -->
        <div class="w-full md:w-1/2">
          <img src="<?= isset($product['image']) ? 'http://localhost/WEB_MSB/public/assets/images/' . $product['image'] : 'http://localhost/WEB_MSB/public/assets/images/product1.jpg' ?>" 
               alt="<?= $product['name'] ?>"
               class="rounded-xl w-full h-80 object-cover">
        </div>

        <!-- Thông tin -->
        <div class="w-full md:w-1/2 space-y-4">
          <h2 class="text-3xl font-bold text-gray-900"><?= $product['name'] ?></h2>
          <p class="text-gray-700 text-lg"><?= $product['description'] ?></p>
          
          <div class="product-price">
            <?= number_format($product['price'], 0, ',', '.') ?>₫
          </div>
          
          <?php if (isset($product['stock']) && $product['stock'] > 0): ?>
          <div class="product-stock">
            <span>✓</span> Còn hàng (<?= $product['stock'] ?> sản phẩm)
          </div>
          <?php else: ?>
          <div class="product-stock" style="color: #e53e3e;">
            <span>✗</span> Hết hàng
          </div>
          <?php endif; ?>

          <form method="POST" action="index.php?controller=product&action=insert_cart&id=<?= $product['id'] ?>" id="add-to-cart-form">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            
            <div class="quantity-control">
              <button type="button" class="quantity-btn" onclick="decreaseQuantity()">-</button>
              <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?= isset($product['stock']) ? $product['stock'] : 1 ?>" class="quantity-input" 
                <?php if (!isset($product['stock']) || $product['stock'] <= 0) echo "disabled"; ?>>
              <button type="button" class="quantity-btn" onclick="increaseQuantity(<?= isset($product['stock']) ? $product['stock'] : 0 ?>)">+</button>
            </div>
            
            <div class="flex space-x-4">
              <button type="submit" class="add-to-cart-btn" <?php if (!isset($product['stock']) || $product['stock'] <= 0) echo "disabled style='opacity: 0.6; cursor: not-allowed;'"; ?>>
                <span class="cart-icon">🛒</span> Thêm vào giỏ hàng
              </button>
              <button type="button" class="add-to-cart-btn buy-now-btn" style="background-color: #e53e3e;" data-product-id="<?= $product['id'] ?>" <?php if (!isset($product['stock']) || $product['stock'] <= 0) echo "disabled style='opacity: 0.6; cursor: not-allowed; pointer-events: none;'"; ?>>
                <span class="cart-icon">⚡</span> Mua ngay
              </button>
            </div>
          </form>
          
          <!-- Nút quay lại -->
          <div class="mt-4">
            <a href="javascript:history.back()" class="text-blue-600 hover:underline">
              ← Quay lại
            </a>
          </div>
        </div>
      </div>
    </main>

    <!-- Sản phẩm đã xem gần đây -->
    <?php if (!empty($recentlyViewed)): ?>
    <section class="mt-10">
      <h3 class="text-2xl font-bold mb-6">Sản phẩm đã xem gần đây</h3>
      
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        <?php foreach ($recentlyViewed as $item): ?>
        <div class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition">
          <a href="index.php?controller=product&action=viewProduct&id=<?= $item['id'] ?>">
            <img src="<?= isset($item['image']) ? 'http://localhost/WEB_MSB/public/assets/images/' . $item['image'] : '/WEB_MSB/public/assets/images/product1.jpg' ?>" 
                 alt="<?= $item['name'] ?>"
                 class="w-full h-48 object-cover">
          </a>
          <div class="p-4">
            <h3 class="text-lg font-semibold mb-2">
              <a href="index.php?controller=product&action=viewProduct&id=<?= $item['id'] ?>" 
                 class="text-gray-900 hover:text-blue-600 transition">
                <?= $item['name'] ?>
              </a>
            </h3>
            <p class="text-red-600 font-bold">
              <?= number_format($item['price'], 0, ',', '.') ?>₫
            </p>
            <div class="mt-4">
              <?php if (isset($item['stock']) && $item['stock'] > 0): ?>
              <a href="index.php?controller=product&action=insert_cart&id=<?= $item['id'] ?>"
                 class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition block text-center">
                Thêm vào giỏ
              </a>
              <?php else: ?>
              <span class="bg-gray-300 text-gray-600 px-4 py-2 rounded-lg block text-center">
                Hết hàng
              </span>
              <?php endif;?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      
      <div class="mt-4 text-center">
        <a href="index.php?controller=product&action=recentlyViewed" class="text-blue-600 hover:underline">
          Xem tất cả sản phẩm đã xem →
        </a>
      </div>
    </section>
    <?php endif; ?>

    <!-- Nút quay lại trang chủ -->
    <div class="mt-10 text-center">
      <a href="index.php?controller=product&action=index" 
         class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
        Tiếp tục mua sắm
      </a>
    </div>
  </div>

  <!-- Modal nhập số lượng cho Mua ngay -->
  <div id="buyNowModal">
      <div class="modal-content">
          <span class="close-x" id="closeBuyNowModalX">&times;</span>
          <img id="modalProductImg" class="modal-product-img" src="" alt="Ảnh sản phẩm">
          <div class="modal-product-name" id="modalProductName"></div>
          <div class="modal-product-desc" id="modalProductDesc"></div>
          <h3>Nhập số lượng muốn mua</h3>
          <form id="buyNowForm" style="margin-bottom:0; text-align:center;">
              <input type="number" id="buyNowQuantity" name="quantity" value="1" min="1">
              <input type="hidden" id="buyNowProductId" name="id" value="">
              <div class="modal-actions">
                  <button type="submit" class="btn btn-sm btn-accent">Xác nhận</button>
                  <button type="button" id="closeBuyNowModal" class="btn btn-sm">Hủy</button>
              </div>
          </form>
      </div>
  </div>

  <script>
    // Xử lý thông báo
    const alertElements = document.querySelectorAll('.alert');
    alertElements.forEach(alert => {
      setTimeout(() => {
        alert.style.display = 'none';
      }, 3000);
    });
    
    // Xử lý số lượng
    function decreaseQuantity() {
      var quantityInput = document.getElementById('quantity');
      var currentValue = parseInt(quantityInput.value);
      if (currentValue > 1) {
        quantityInput.value = currentValue - 1;
      }
    }
    
    function increaseQuantity(maxStock) {
      var quantityInput = document.getElementById('quantity');
      var currentValue = parseInt(quantityInput.value);
      if (currentValue < maxStock) {
        quantityInput.value = currentValue + 1;
      }
    }

    // Đảm bảo không cho nhập số lượng vượt quá tồn kho
    var quantityInput = document.getElementById('quantity');
    if (quantityInput) {
      quantityInput.addEventListener('input', function() {
        var maxStock = <?= isset($product['stock']) ? (int)$product['stock'] : 1 ?>;
        var value = parseInt(this.value);
        if (value > maxStock) this.value = maxStock;
        if (value < 1 || isNaN(value)) this.value = 1;
      });
    }

    var buyNowBtn = document.querySelector('.buy-now-btn');
    var buyNowModal = document.getElementById('buyNowModal');
    var buyNowForm = document.getElementById('buyNowForm');
    var buyNowProductId = document.getElementById('buyNowProductId');
    var buyNowQuantity = document.getElementById('buyNowQuantity');
    var closeBuyNowModal = document.getElementById('closeBuyNowModal');
    var closeBuyNowModalX = document.getElementById('closeBuyNowModalX');
    var modalProductImg = document.getElementById('modalProductImg');
    var modalProductName = document.getElementById('modalProductName');
    var modalProductDesc = document.getElementById('modalProductDesc');

    function openBuyNowModal(productId) {
        buyNowProductId.value = productId;
        // Lấy số lượng đã chọn ở input ngoài
        var outsideQuantity = document.getElementById('quantity');
        var selectedQuantity = outsideQuantity ? parseInt(outsideQuantity.value) || 1 : 1;
        buyNowQuantity.value = selectedQuantity;
        // Hiển thị thông tin sản phẩm
        modalProductImg.src = "<?= isset($product['image']) ? 'http://localhost/WEB_MSB/public/assets/images/' . $product['image'] : 'http://localhost/WEB_MSB/public/assets/images/product1.jpg' ?>";
        modalProductName.textContent = "<?= htmlspecialchars($product['name']) ?>";
        modalProductDesc.textContent = "<?= htmlspecialchars($product['description']) ?>";
        // Giới hạn max theo tồn kho
        var stock = <?= isset($product['stock']) ? (int)$product['stock'] : 9999 ?>;
        buyNowQuantity.max = stock;
        buyNowQuantity.setAttribute('max', stock);
        if (stock <= 0) {
            buyNowQuantity.value = 0;
            buyNowQuantity.disabled = true;
        } else {
            buyNowQuantity.disabled = false;
        }
        buyNowModal.classList.add('active');
        buyNowQuantity.focus();
    }
    function closeBuyNow() {
        buyNowModal.classList.remove('active');
    }
    if (buyNowBtn) {
        buyNowBtn.addEventListener('click', function() {
            var productId = buyNowBtn.getAttribute('data-product-id');
            openBuyNowModal(productId);
        });
    }
    closeBuyNowModal.addEventListener('click', closeBuyNow);
    closeBuyNowModalX.addEventListener('click', closeBuyNow);
    buyNowModal.addEventListener('click', function(e) {
        if (e.target === buyNowModal) closeBuyNow();
    });
    buyNowForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var productId = buyNowProductId.value;
        var quantity = parseInt(buyNowQuantity.value);
        var stock = <?= isset($product['stock']) ? (int)$product['stock'] : 9999 ?>;
        if (!quantity || quantity < 1) quantity = 1;
        if (quantity > stock) {
          alert('Số lượng vượt quá tồn kho!');
          buyNowQuantity.value = stock;
          return;
        }
        window.location.href = 'index.php?controller=Product&action=buy_now&id=' + encodeURIComponent(productId) + '&quantity=' + encodeURIComponent(quantity);
    });
  </script>
</body>
</html>
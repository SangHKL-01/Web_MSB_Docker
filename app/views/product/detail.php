<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title><?= $product['name'] ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="container mx-auto px-4 py-8">
  <!-- Chi tiết sản phẩm -->
  <main class="max-w-4xl mx-auto p-8 bg-white rounded-xl shadow-lg mb-8">
    <div class="flex flex-col md:flex-row gap-10">
      <!-- Hình ảnh -->
      <div class="w-full md:w-1/2">
        <img src="<?= isset($product['image']) ? $product['image'] : 'public/assets/images/product-placeholder.jpg' ?>" 
             alt="<?= $product['name'] ?>"
             class="rounded-xl w-full h-80 object-cover">
      </div>

      <!-- Thông tin -->
      <div class="w-full md:w-1/2 space-y-4">
        <h2 class="text-3xl font-bold text-gray-900"><?= $product['name'] ?></h2>
        <p class="text-gray-700 text-lg"><?= $product['description'] ?></p>
        <p class="text-2xl text-red-600 font-semibold">
          <?= number_format($product['price'], 0, ',', '.') ?>₫
        </p>

        <form method="POST" action="index.php?controller=product&action=insert_cart&id=<?= $product['id'] ?>" class="space-y-4">
          <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
          <label class="block">
            <span class="text-sm text-gray-600">Số lượng</span>
            <input type="number" name="quantity" value="1" min="1"
                   class="mt-1 block w-24 border border-gray-300 px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
          </label>
          <button type="submit"
                  class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            🛒 Thêm vào giỏ hàng
          </button>
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
          <img src="<?= isset($item['image']) ? $item['image'] : 'public/assets/images/product-placeholder.jpg' ?>" 
               alt="<?= $item['name'] ?>"
               class="w-full h-48 object-cover">
        </a>
        <div class="p-4">
          <h3 class="text-lg font-semibold mb-2 line-clamp-2">
            <a href="index.php?controller=product&action=viewProduct&id=<?= $item['id'] ?>" 
               class="text-gray-900 hover:text-blue-600 transition">
              <?= $item['name'] ?>
            </a>
          </h3>
          <p class="text-red-600 font-bold">
            <?= number_format($item['price'], 0, ',', '.') ?>₫
          </p>
          <div class="mt-4">
            <a href="index.php?controller=product&action=insert_cart&id=<?= $item['id'] ?>"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition block text-center">
              Thêm vào giỏ
            </a>
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

</body>
</html> 
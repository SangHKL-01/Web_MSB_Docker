<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title><?= $title ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="container mx-auto px-4 py-8">
  <h1 class="text-3xl font-bold mb-8 text-center"><?= $title ?></h1>
  
  <?php if (empty($products)): ?>
    <div class="text-center py-10">
      <p class="text-xl text-gray-600 mb-6">Bạn chưa xem sản phẩm nào gần đây.</p>
      <a href="index.php?controller=product&action=index" 
         class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
        Tiếp tục mua sắm
      </a>
    </div>
  <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <?php foreach ($products as $product): ?>
        <div class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition">
          <a href="index.php?controller=product&action=viewProduct&id=<?= $product['id'] ?>">
            <img src="<?= isset($product['image']) ? '/public/assets/images/'.$product['image'] : '/public/assets/images/product-placeholder.jpg' ?>" 
                alt="<?= $product['name'] ?>"
                class="w-full h-48 object-cover">
          </a>
          <div class="p-4">
            <h3 class="text-lg font-semibold mb-2">
              <a href="index.php?controller=product&action=viewProduct&id=<?= $product['id'] ?>" 
                 class="text-gray-900 hover:text-blue-600 transition">
                <?= $product['name'] ?>
              </a>
            </h3>
            <p class="text-red-600 font-bold">
              <?= number_format($product['price'], 0, ',', '.') ?>₫
            </p>
            <div class="mt-4 flex space-x-2">
              <a href="index.php?controller=product&action=viewProduct&id=<?= $product['id'] ?>"
                 class="bg-gray-200 text-gray-800 px-3 py-1 rounded hover:bg-gray-300 transition flex-grow text-center">
                Xem chi tiết
              </a>
              <a href="index.php?controller=product&action=insert_cart&id=<?= $product['id'] ?>"
                 class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition flex-grow text-center">
                Thêm vào giỏ
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    
    <div class="mt-8 text-center">
      <a href="index.php?controller=product&action=index" 
         class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
        Tiếp tục mua sắm
      </a>
    </div>
  <?php endif; ?>
</div>

</body>
</html> 
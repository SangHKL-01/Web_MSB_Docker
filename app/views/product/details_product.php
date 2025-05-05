<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title><?= $product['name'] ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<main class="max-w-4xl mx-auto p-8 bg-white rounded-2xl shadow-xl mt-10">
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
    </div>
  </div>
</main>

</body>
</html>

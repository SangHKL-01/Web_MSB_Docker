<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Giỏ Hàng - Shop Trực Tuyến</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<div class="container mx-auto px-4 py-8">
  <h1 class="text-2xl font-bold mb-6">Giỏ Hàng Của Bạn</h1>
  
  <?php if (!empty($products)): ?>
    <div class="overflow-x-auto mt-6">
        <table class="min-w-full border border-gray-300 bg-white shadow rounded-lg">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">ID</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Tên sản phẩm</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Số lượng</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr class="border-t">
                        <td class="px-4 py-2"><?= $product['id'] ?? '-' ?></td>
                        <td class="px-4 py-2"><?= $product['product_name'] ?? $product['name'] ?? '-' ?></td>
                        <td class="px-4 py-2"><?= $product['quantity'] ?? '1' ?></td>
                        <td class="px-4 py-2">
                            <a href="index.php?controller=product&action=remove_from_cart&id=<?= $product['id'] ?>" 
                               class="bg-red-500 hover:bg-red-600 text-white py-1 px-3 rounded text-sm">
                                Xóa
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="mt-6 flex justify-between items-center">
          <a href="index.php?controller=product&action=index" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded">
            Tiếp tục mua hàng
          </a>
          
          <a href="index.php?controller=product&action=checkout" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">
            Thanh toán
          </a>
        </div>
    </div>
  <?php else: ?>
    <div class="bg-white p-8 rounded-lg shadow text-center">
      <p class="text-gray-600 mb-4">Giỏ hàng của bạn chưa có sản phẩm nào.</p>
      <a href="index.php?controller=product&action=index" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded inline-block">
        Tiếp tục mua hàng
      </a>
    </div>
  <?php endif; ?>
</div>

</body>
</html>
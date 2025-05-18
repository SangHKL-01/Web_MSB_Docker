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
    <div id="checkout-error" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
      <span class="block sm:inline">Vui lòng chọn ít nhất một sản phẩm để thanh toán.</span>
    </div>
    <form method="POST" action="index.php?controller=product&action=checkout" id="checkout-form" onsubmit="return validateCheckout()">
    <div class="overflow-x-auto mt-6">
        <table class="min-w-full border border-gray-300 bg-white shadow rounded-lg">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700"><input type="checkbox" id="checkAll" onclick="toggleAll(this)"></th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">ID</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Tên sản phẩm</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Số lượng</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Đơn giá</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Thành tiền</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = 0;
                foreach ($products as $product): 
                    $price = isset($product['price']) ? $product['price'] : 0;
                    $quantity = isset($product['quantity']) ? $product['quantity'] : 1;
                    $subtotal = $price * $quantity;
                    $total += $subtotal;
                ?>
                    <tr class="border-t">
                        <td class="px-4 py-2"><input type="checkbox" name="cart_ids[]" value="<?= $product['id'] ?>"></td>
                        <td class="px-4 py-2"><?= $product['id'] ?? '-' ?></td>
                        <td class="px-4 py-2"><?= $product['product_name'] ?? $product['name'] ?? $product['name_product'] ?? '-' ?></td>
                        <td class="px-4 py-2"><?= $quantity ?></td>
                        <td class="px-4 py-2"><?= number_format($price, 0, ',', '.') ?> đ</td>
                        <td class="px-4 py-2"><?= number_format($subtotal, 0, ',', '.') ?> đ</td>
                        <td class="px-4 py-2">
                            <a href="index.php?controller=product&action=remove_from_cart&id=<?= $product['id'] ?>" 
                               class="bg-red-500 hover:bg-red-600 text-white py-1 px-3 rounded text-sm">
                                Xóa
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr class="border-t font-bold bg-gray-50">
                    <td colspan="5" class="px-4 py-2 text-right">Tổng tiền (tạm tính):</td>
                    <td class="px-4 py-2" colspan="2"><?= number_format($total, 0, ',', '.') ?> đ</td>
                </tr>
            </tbody>
        </table>
        <script>
        function toggleAll(source) {
            checkboxes = document.getElementsByName('cart_ids[]');
            for(var i=0, n=checkboxes.length;i<n;i++) {
                checkboxes[i].checked = source.checked;
            }
        }
        
        function validateCheckout() {
            const checkboxes = document.getElementsByName('cart_ids[]');
            let atLeastOneChecked = false;
            
            for(let i=0; i<checkboxes.length; i++) {
                if(checkboxes[i].checked) {
                    atLeastOneChecked = true;
                    break;
                }
            }
            
            if(!atLeastOneChecked) {
                document.getElementById('checkout-error').classList.remove('hidden');
                return false;
            }
            
            return true;
        }
        </script>
        <div class="mt-6 flex justify-between items-center">
          <a href="index.php?controller=product&action=index" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded">
            Tiếp tục mua hàng
          </a>
          <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">
            Thanh toán các sản phẩm đã chọn
          </button>
        </div>
    </div>
    </form>
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
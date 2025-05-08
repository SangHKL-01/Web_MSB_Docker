<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Thanh Toán Mua Ngay - Shop Trực Tuyến</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<div class="container mx-auto px-4 py-8">
  <h1 class="text-2xl font-bold mb-6">Thanh Toán Mua Ngay</h1>
  
  <?php if(isset($_SESSION['error'])): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
      <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
  <?php endif; ?>
  
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Phần thông tin sản phẩm -->
    <div class="bg-white p-6 rounded-lg shadow">
      <h2 class="text-lg font-semibold mb-4">Thông tin sản phẩm</h2>
      
      <?php if (isset($product) && !empty($product)): ?>
        <div class="overflow-x-auto mt-6">
          <table class="min-w-full border border-gray-300 bg-white">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-4 py-2 text-left text-sm">Sản phẩm</th>
                <th class="px-4 py-2 text-left text-sm">Số lượng</th>
                <th class="px-4 py-2 text-left text-sm">Đơn giá</th>
                <th class="px-4 py-2 text-left text-sm">Thành tiền</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $price = isset($product['price']) ? $product['price'] : 0;
              $quantity = isset($product['quantity']) ? $product['quantity'] : 1;
              $subtotal = $price * $quantity;
              ?>
              <tr class="border-t">
                <td class="px-4 py-2"><?= isset($product['product_name']) ? $product['product_name'] : (isset($product['name']) ? $product['name'] : 'Sản phẩm') ?></td>
                <td class="px-4 py-2"><?= $quantity ?></td>
                <td class="px-4 py-2"><?= number_format($price, 0, ',', '.') ?> đ</td>
                <td class="px-4 py-2"><?= number_format($subtotal, 0, ',', '.') ?> đ</td>
              </tr>
              <tr class="border-t font-bold bg-gray-50">
                <td colspan="3" class="px-4 py-2 text-right">Tổng tiền:</td>
                <td class="px-4 py-2"><?= number_format($subtotal, 0, ',', '.') ?> đ</td>
              </tr>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="text-gray-600">Không có thông tin sản phẩm.</p>
        <p><a href="index.php?controller=product&action=index" class="text-blue-500 hover:underline">Quay lại cửa hàng</a></p>
      <?php endif; ?>
    </div>
    
    <!-- Phần thông tin thanh toán -->
    <div class="bg-white p-6 rounded-lg shadow">
      <h2 class="text-lg font-semibold mb-4">Thông tin thanh toán</h2>
      
      <form method="POST" action="index.php?controller=product&action=buy_now&id=<?= $product['product_id'] ?>" class="space-y-4">
        <input type="hidden" name="checkout" value="1">
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Họ tên</label>
          <input type="text" name="fullname" required
                 class="w-full border px-4 py-2 rounded-lg bg-white text-gray-800" />
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
          <input type="text" name="address" required
                 class="w-full border px-4 py-2 rounded-lg bg-white text-gray-800" />
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
          <input type="text" name="phone" required pattern="[0-9]{10,11}" title="Số điện thoại phải chứa 10-11 chữ số"
                 class="w-full border px-4 py-2 rounded-lg bg-white text-gray-800" />
          <small class="text-gray-500">Vui lòng nhập số điện thoại từ 10-11 chữ số</small>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input type="email" name="email"
                 class="w-full border px-4 py-2 rounded-lg bg-white text-gray-800" />
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Phương thức thanh toán</label>
          <select name="payment_method" required
                  class="w-full border px-4 py-2 rounded-lg bg-white text-gray-800">
            <option value="COD">Thanh toán khi nhận hàng (COD)</option>
            <option value="bank_transfer">Chuyển khoản ngân hàng</option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
          <textarea name="notes" 
                   class="w-full border px-4 py-2 rounded-lg bg-white text-gray-800" rows="3"></textarea>
        </div>
        
        <div class="pt-4">
          <button type="submit"
                  class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg">
            Xác nhận đặt hàng
          </button>
        </div>
      </form>
      
      <div class="mt-4">
        <a href="index.php?controller=product&action=detail&id=<?= $product['product_id'] ?>" 
           class="text-blue-500 hover:underline text-sm">
          « Quay lại sản phẩm
        </a>
      </div>
    </div>
  </div>
</div>

<script>
  // Kiểm tra form trước khi submit
  document.querySelector('form').addEventListener('submit', function(e) {
    const phoneInput = document.querySelector('input[name="phone"]');
    const phoneValue = phoneInput.value.trim();
    
    // Kiểm tra số điện thoại chỉ chứa số
    if (!/^\d+$/.test(phoneValue)) {
      e.preventDefault();
      alert('Số điện thoại chỉ được chứa chữ số!');
      phoneInput.focus();
      return false;
    }
    
    // Kiểm tra độ dài số điện thoại
    if (phoneValue.length < 10 || phoneValue.length > 11) {
      e.preventDefault();
      alert('Số điện thoại phải có 10-11 chữ số!');
      phoneInput.focus();
      return false;
    }
  });
</script>
</body>
</html>
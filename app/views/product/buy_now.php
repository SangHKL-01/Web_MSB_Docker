<?php require_once 'app/views/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Thanh Toán Mua Ngay</h1>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= $_SESSION['error'] ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <!-- Phần thông tin sản phẩm -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Thông tin sản phẩm</h2>
        
        <?php if (isset($product) && !empty($product)): ?>
            <div class="border-b pb-4 mb-4">
                <div class="flex items-center">
                    <?php if (isset($product['image'])): ?>
                        <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>" class="w-16 h-16 object-cover mr-4">
                    <?php endif; ?>
                    <div>
                        <h3 class="font-medium"><?= $product['name'] ?></h3>
                        <p class="text-gray-600">Số lượng: <?= $product['quantity'] ?></p>
                        <p class="text-gray-800 font-medium">Giá: <?= number_format($product['price'], 0, ',', '.') ?> VNĐ</p>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-between font-semibold text-lg">
                <span>Tổng cộng:</span>
                <span><?= number_format($product['price'] * $product['quantity'], 0, ',', '.') ?> VNĐ</span>
            </div>
        <?php else: ?>
            <p class="text-gray-600">Không có thông tin sản phẩm.</p>
            <p><a href="index.php?controller=product&action=index" class="text-blue-500 hover:underline">Quay lại cửa hàng</a></p>
        <?php endif; ?>
    </div>
    
    <!-- Form thanh toán -->
    <?php if (isset($product) && !empty($product)): ?>
        <form method="POST" action="index.php?controller=product&action=buy_now&id=<?= $product['product_id'] ?>" class="space-y-4">
            <input type="hidden" name="checkout" value="1">
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-4">Thông tin thanh toán</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="fullname" class="block text-gray-700 mb-1">Họ tên <span class="text-red-500">*</span></label>
                        <input type="text" id="fullname" name="fullname" class="w-full px-3 py-2 border rounded" required>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" class="w-full px-3 py-2 border rounded">
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-gray-700 mb-1">Số điện thoại <span class="text-red-500">*</span></label>
                        <input type="text" id="phone" name="phone" class="w-full px-3 py-2 border rounded" required>
                    </div>
                    
                    <div>
                        <label for="address" class="block text-gray-700 mb-1">Địa chỉ <span class="text-red-500">*</span></label>
                        <input type="text" id="address" name="address" class="w-full px-3 py-2 border rounded" required>
                    </div>
                </div>
                
                <div class="mt-4">
                    <label for="notes" class="block text-gray-700 mb-1">Ghi chú</label>
                    <textarea id="notes" name="notes" rows="3" class="w-full px-3 py-2 border rounded"></textarea>
                </div>
                
                <div class="mt-4">
                    <label class="block text-gray-700 mb-1">Phương thức thanh toán</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="payment_method" value="cod" checked class="mr-2">
                            <span>Thanh toán khi nhận hàng (COD)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="payment_method" value="bank_transfer" class="mr-2">
                            <span>Chuyển khoản ngân hàng</span>
                        </label>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-between">
                    <a href="index.php?controller=product&action=detail&id=<?= $product['product_id'] ?>" class="text-blue-500 hover:underline">« Quay lại sản phẩm</a>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">Đặt hàng</button>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php require_once 'app/views/footer.php'; ?>
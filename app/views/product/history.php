<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử đơn hàng - Kheo Báng Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }
        
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 1rem 0;
        }
        
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 1rem;
        }
        
        .logo a {
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
            text-decoration: none;
        }
        
        nav ul {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        nav ul li {
            margin-left: 1.5rem;
        }
        
        nav ul li a {
            color: white;
            text-decoration: none;
        }
        
        .footer {
            background-color: #2c3e50;
            color: white;
            padding: 2rem 0;
            margin-top: 2rem;
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        
        .footer-links {
            display: flex;
            list-style: none;
            margin: 1rem 0 0;
            padding: 0;
        }
        
        .footer-links li {
            margin: 0 1rem;
        }
        
        .footer-links li a {
            color: white;
            text-decoration: none;
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
                    <li><a href="#">Liên Hệ</a></li>
                    <li><a href="index.php?controller=Product&action=gio_hang">Giỏ Hàng</a></li>
                    <?php
                    if (isset($_SESSION['user'])) {
                        echo '<li>
                                <a href="index.php?controller=user&action=profile">
                                    <img src="http://localhost/WEB_MSB/public/assets/images/avatar.jpg" alt="Profile" width="30" style="border-radius: 50%; vertical-align: middle;" />
                                </a>
                            </li>';
                    } else {
                        echo '<li>
                                <a href="index.php?controller=user&action=login">Đăng nhập</a>
                            </li>';
                    }
                    ?>  
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-2xl font-bold mb-6">Lịch sử đơn hàng</h1>
            
            <?php if (empty($orders)): ?>
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
                    <p>Bạn chưa có đơn hàng nào. <a href="index.php?controller=product&action=index" class="text-blue-600 hover:underline">Tiếp tục mua sắm</a>.</p>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="bg-white shadow-md rounded-lg mb-6 overflow-hidden">
                        <div class="bg-gray-100 px-4 py-3 border-b">
                            <div class="flex justify-between items-center">
                                <h2 class="text-lg font-semibold">Đơn hàng #<?php echo $order['id']; ?></h2>
                                <span class="text-sm text-gray-600">
                                    <?php 
                                    if (isset($order['order_date'])) {
                                        echo date('d/m/Y H:i', strtotime($order['order_date']));
                                    } elseif (isset($order['created_at'])) {
                                        echo date('d/m/Y H:i', strtotime($order['created_at']));
                                    } else {
                                        echo date('d/m/Y H:i');
                                    }
                                    ?>
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mt-2 text-sm">
                                <div>
                                    <p><strong>Tên:</strong> <?php echo $order['customer_name']; ?></p>
                                    <p><strong>Địa chỉ:</strong> <?php echo $order['customer_address']; ?></p>
                                </div>
                                <div>
                                    <p><strong>Điện thoại:</strong> <?php echo $order['customer_phone']; ?></p>
                                    <p><strong>Phương thức thanh toán:</strong> <?php echo $order['payment_method']; ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <h3 class="font-semibold mb-2">Chi tiết đơn hàng:</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số lượng</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đơn giá</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php if (isset($order['details']) && !empty($order['details'])): ?>
                                            <?php foreach ($order['details'] as $detail): ?>
                                                <tr>
                                                    <td class="px-4 py-2 whitespace-nowrap"><?php echo $detail['product_name']; ?></td>
                                                    <td class="px-4 py-2 whitespace-nowrap"><?php echo $detail['quantity']; ?></td>
                                                    <td class="px-4 py-2 whitespace-nowrap"><?php echo number_format($detail['price'], 0, ',', '.'); ?> đ</td>
                                                    <td class="px-4 py-2 whitespace-nowrap"><?php echo number_format($detail['price'] * $detail['quantity'], 0, ',', '.'); ?> đ</td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="px-4 py-2 text-center">Không có chi tiết đơn hàng</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-gray-50">
                                            <td colspan="3" class="px-4 py-2 text-right font-semibold">Tổng tiền:</td>
                                            <td class="px-4 py-2 font-semibold"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> đ</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        
                        <?php if (!empty($order['notes'])): ?>
                        <div class="px-4 py-2 bg-gray-50 border-t">
                            <p><strong>Ghi chú:</strong> <?php echo $order['notes']; ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="mt-6">
                <a href="index.php?controller=product&action=index" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">
                    Tiếp tục mua sắm
                </a>
            </div>
        </div>
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
</body>
</html>
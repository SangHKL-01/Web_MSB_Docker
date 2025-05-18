<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Đơn Hàng - MSB Shop</title>
    <link rel="stylesheet" href="/public/assets/CSS/homepage.css">
    <link rel="stylesheet" href="/public/assets/CSS/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        .admin-sidebar {
            width: 250px;
            background-color: #343a40;
            color: white;
            padding: 20px 0;
        }
        .admin-sidebar h2 {
            padding: 0 20px;
            margin-bottom: 20px;
        }
        .admin-sidebar ul {
            list-style: none;
            padding: 0;
        }
        .admin-sidebar li {
            margin-bottom: 5px;
        }
        .admin-sidebar a {
            display: block;
            padding: 10px 20px;
            color: #fff;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .admin-sidebar a:hover, .admin-sidebar a.active {
            background-color: #495057;
        }
        .admin-content {
            flex: 1;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .order-detail {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .order-id {
            font-size: 24px;
            font-weight: bold;
        }
        .order-date {
            color: #6c757d;
        }
        .order-status {
            display: flex;
            align-items: center;
        }
        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            margin-right: 10px;
        }
        .status-pending, .status-đang-xử-lý {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-completed, .status-hoàn-thành {
            background-color: #d4edda;
            color: #155724;
        }
        .status-shipped, .status-đang-giao {
            background-color: #cce5ff;
            color: #004085;
        }
        .status-cancelled, .status-đã-hủy {
            background-color: #f8d7da;
            color: #721c24;
        }
        .customer-info, .order-items {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 15px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }
        .info-item {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: 500;
            color: #6c757d;
            display: block;
            margin-bottom: 5px;
        }
        .info-value {
            color: #212529;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        th {
            background-color: #f8f9fa;
            font-weight: 500;
        }
        tr:hover {
            background-color: #f8f9fa;
        }
        .order-total {
            text-align: right;
            font-size: 18px;
            font-weight: 500;
            margin-top: 20px;
        }
        .order-actions {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        .btn {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0069d9;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
            border: none;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
            border: none;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <h2>Quản Trị</h2>
            <ul>
                <li><a href="index.php?controller=Admin&action=index">Tổng Quan</a></li>
                <li><a href="index.php?controller=Admin&action=orders" class="active">Đơn Hàng</a></li>
                <li><a href="index.php?controller=Admin&action=users">Người Dùng</a></li>
                <li><a href="index.php?controller=User&action=logout">Đăng Xuất</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="admin-content">
            <h1>Chi Tiết Đơn Hàng</h1>
            
            <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success'] ?>
                <?php unset($_SESSION['success']); ?>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error'] ?>
                <?php unset($_SESSION['error']); ?>
            </div>
            <?php endif; ?>
            
            <div class="order-detail">
                <!-- Order Header -->
                <div class="order-header">
                    <div>
                        <div class="order-id">Đơn hàng #<?= $order['id'] ?></div>
                        <div class="order-date">Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></div>
                    </div>
                    <div class="order-status">
                        <span class="status status-<?= strtolower(str_replace(' ', '-', $order['status'])) ?>">
                            <?= $order['status'] ?>
                        </span>
                        <form method="POST" action="index.php?controller=Admin&action=updateOrderStatus" style="display: inline-block;">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <select name="status" style="margin-right: 10px;">
                                <option value="đang xử lý" <?= $order['status'] == 'đang xử lý' ? 'selected' : '' ?>>Đang xử lý</option>
                                <option value="đang giao" <?= $order['status'] == 'đang giao' ? 'selected' : '' ?>>Đang giao</option>
                                <option value="hoàn thành" <?= $order['status'] == 'hoàn thành' ? 'selected' : '' ?>>Hoàn thành</option>
                                <option value="đã hủy" <?= $order['status'] == 'đã hủy' ? 'selected' : '' ?>>Đã hủy</option>
                            </select>
                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                        </form>
                    </div>
                </div>
                
                <!-- Customer Information -->
                <div class="customer-info">
                    <h3 class="section-title">Thông Tin Khách Hàng</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Họ và tên:</span>
                            <span class="info-value"><?= htmlspecialchars($order['customer_name']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Số điện thoại:</span>
                            <span class="info-value"><?= htmlspecialchars($order['customer_phone']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Địa chỉ:</span>
                            <span class="info-value"><?= htmlspecialchars($order['customer_address']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Phương thức thanh toán:</span>
                            <span class="info-value"><?= htmlspecialchars($order['payment_method'] ?? 'Thanh toán khi nhận hàng') ?></span>
                        </div>
                    </div>
                    <?php if (!empty($order['notes'])): ?>
                    <div class="info-item" style="margin-top: 10px;">
                        <span class="info-label">Ghi chú đơn hàng:</span>
                        <span class="info-value"><?= nl2br(htmlspecialchars($order['notes'])) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Order Items -->
                <div class="order-items">
                    <h3 class="section-title">Danh Sách Sản Phẩm</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Giá</th>
                                <th>Số lượng</th>
                                <th style="text-align: right;">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($order_details)): ?>
                                <?php foreach ($order_details as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                                        <td><?= number_format($item['price'], 0, ',', '.') ?> VNĐ</td>
                                        <td><?= $item['quantity'] ?></td>
                                        <td style="text-align: right;"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> VNĐ</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align: center;">Không có sản phẩm nào</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="order-total">
                        Tổng tiền: <strong><?= number_format($order['total_amount'], 0, ',', '.') ?> VNĐ</strong>
                    </div>
                </div>
                
                <!-- Order Actions -->
                <div class="order-actions">
                    <a href="index.php?controller=Admin&action=orders" class="btn btn-secondary">Quay lại danh sách</a>
                    <?php if ($order['status'] !== 'đã hủy' && $order['status'] !== 'hoàn thành'): ?>
                        <button class="btn btn-danger" onclick="confirmCancel()">Hủy đơn hàng</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function printOrder() {
            window.print();
        }
        
        function confirmCancel() {
            if (confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')) {
                document.querySelector('select[name="status"]').value = 'đã hủy';
                document.querySelector('form').submit();
            }
        }
    </script>
</body>
</html> 
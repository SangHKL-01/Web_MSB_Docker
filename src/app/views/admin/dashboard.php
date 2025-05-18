<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Quản Trị - MSB Shop</title>
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
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .card-title {
            font-size: 16px;
            color: #6c757d;
            margin-bottom: 10px;
        }
        .card-value {
            font-size: 28px;
            font-weight: bold;
            color: #343a40;
        }
        .recent-orders {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .recent-orders h3 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #343a40;
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
        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-shipped {
            background-color: #cce5ff;
            color: #004085;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
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
                <li><a href="index.php?controller=Admin&action=index" class="active">Tổng Quan</a></li>
                <li><a href="index.php?controller=Admin&action=orders">Đơn Hàng</a></li>
                <li><a href="index.php?controller=Admin&action=users">Người Dùng</a></li>
                <li><a href="index.php?controller=User&action=logout">Đăng Xuất</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="admin-content">
            <h1>Tổng Quan Hệ Thống</h1>
            
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
            
            <!-- Dashboard Cards -->
            <div class="dashboard-cards">
                <div class="card">
                    <div class="card-title">Tổng Đơn Hàng</div>
                    <div class="card-value"><?= $total_orders ?></div>
                </div>
                <div class="card">
                    <div class="card-title">Tổng Người Dùng</div>
                    <div class="card-value"><?= $total_users ?></div>
                </div>
                <div class="card">
                    <div class="card-title">Tổng Doanh Thu</div>
                    <div class="card-value"><?= number_format($total_revenue, 0, ',', '.') ?> VNĐ</div>
                </div>
            </div>
            
            <!-- Recent Orders -->
            <div class="recent-orders">
                <h3>Đơn Hàng Gần Đây</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Mã Đơn</th>
                            <th>Khách Hàng</th>
                            <th>Tổng Tiền</th>
                            <th>Trạng Thái</th>
                            <th>Ngày Tạo</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recent_orders)): ?>
                            <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td>#<?= $order['id'] ?></td>
                                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                    <td><?= number_format($order['total_amount'], 0, ',', '.') ?> VNĐ</td>
                                    <td>
                                        <span class="status status-<?= strtolower(str_replace(' ', '-', $order['status'])) ?>">
                                            <?= $order['status'] ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                    <td>
                                        <a href="index.php?controller=Admin&action=orderDetail&id=<?= $order['id'] ?>">Chi tiết</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">Không có đơn hàng nào</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <div style="text-align: right; margin-top: 15px;">
                    <a href="index.php?controller=Admin&action=orders">Xem tất cả đơn hàng</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 
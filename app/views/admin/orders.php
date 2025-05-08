<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đơn Hàng - Kheo Báng Shop</title>
    <link rel="stylesheet" href="http://localhost/WEB_MSB/public/assets/CSS/homepage.css">
    <link rel="stylesheet" href="http://localhost/WEB_MSB/public/assets/CSS/admin.css">
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
        .orders-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
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
        .search-filter {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        .search-filter input, .search-filter select {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }
        .search-filter button {
            padding: 8px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-filter button:hover {
            background-color: #0069d9;
        }
        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }
        .pagination a {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 5px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            color: #007bff;
            text-decoration: none;
        }
        .pagination a.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        .pagination a:hover:not(.active) {
            background-color: #e9ecef;
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
            <h1>Quản Lý Đơn Hàng</h1>
            
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
            
            <!-- Filter & Search -->
            <div class="search-filter">
                <input type="text" placeholder="Tìm theo tên khách hàng..." id="searchInput">
                <select id="statusFilter">
                    <option value="">Tất cả trạng thái</option>
                    <option value="đang xử lý">Đang xử lý</option>
                    <option value="đang giao">Đang giao</option>
                    <option value="hoàn thành">Hoàn thành</option>
                    <option value="đã hủy">Đã hủy</option>
                </select>
                <button onclick="filterOrders()">Lọc</button>
            </div>
            
            <!-- Orders Table -->
            <div class="orders-container">
                <table id="ordersTable">
                    <thead>
                        <tr>
                            <th>Mã Đơn</th>
                            <th>Khách Hàng</th>
                            <th>Số Điện Thoại</th>
                            <th>Tổng Tiền</th>
                            <th>Phương Thức Thanh Toán</th>
                            <th>Trạng Thái</th>
                            <th>Ngày Tạo</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?= $order['id'] ?></td>
                                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                    <td><?= htmlspecialchars($order['customer_phone']) ?></td>
                                    <td><?= number_format($order['total_amount'], 0, ',', '.') ?> VNĐ</td>
                                    <td><?= htmlspecialchars($order['payment_method'] ?? 'COD') ?></td>
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
                                <td colspan="8" style="text-align: center;">Không có đơn hàng nào</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
    
    <script>
        function filterOrders() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
            const table = document.getElementById('ordersTable');
            const rows = table.getElementsByTagName('tr');
            
            // Skip header row (index 0)
            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const customerNameCell = row.cells[1];
                const statusCell = row.cells[5];
                
                if (!customerNameCell || !statusCell) continue;
                
                const customerName = customerNameCell.textContent.toLowerCase();
                const status = statusCell.textContent.trim().toLowerCase();
                
                const matchesSearch = searchTerm === '' || customerName.includes(searchTerm);
                const matchesStatus = statusFilter === '' || status === statusFilter;
                
                if (matchesSearch && matchesStatus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        }
    </script>
</body>
</html> 
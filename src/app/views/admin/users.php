<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Người Dùng - MSB Shop</title>
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
        .users-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .users-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
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
        .btn-danger {
            background-color: #dc3545;
            color: white;
            border: none;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
            border: none;
        }
        .btn-warning:hover {
            background-color: #e0a800;
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
        .role {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .role-admin {
            background-color: #ffc107;
            color: #212529;
        }
        .role-user {
            background-color: #17a2b8;
            color: white;
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
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        .action-buttons a, .action-buttons button {
            padding: 5px 8px;
            font-size: 12px;
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
                <li><a href="index.php?controller=Admin&action=orders">Đơn Hàng</a></li>
                <li><a href="index.php?controller=Admin&action=users" class="active">Người Dùng</a></li>
                <li><a href="index.php?controller=User&action=logout">Đăng Xuất</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="admin-content">
            <h1>Quản Lý Người Dùng</h1>
            
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
            
            <div class="users-container">
                <div class="users-header">
                    <h2>Danh Sách Người Dùng</h2>
                    <a href="index.php?controller=Admin&action=addUser" class="btn btn-primary">Thêm Người Dùng</a>
                </div>
                
                <!-- Filter & Search -->
                <div class="search-filter">
                    <input type="text" placeholder="Tìm kiếm người dùng..." id="searchInput">
                    <select id="roleFilter">
                        <option value="">Tất cả vai trò</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                    <button onclick="filterUsers()">Lọc</button>
                </div>
                
                <!-- Users Table -->
                <table id="usersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Họ Tên</th>
                            <th>Vai Trò</th>
                            <th>Ngày Tạo</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td><?= htmlspecialchars($user['username']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= htmlspecialchars($user['fullname'] ?? '') ?></td>
                                    <td>
                                        <span class="role role-<?= strtolower($user['role'] ?? 'user') ?>">
                                            <?= ucfirst($user['role'] ?? 'user') ?>
                                        </span>
                                    </td>
                                    <td><?= isset($user['created_at']) ? date('d/m/Y', strtotime($user['created_at'])) : 'N/A' ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="index.php?controller=Admin&action=editUser&id=<?= $user['id'] ?>" class="btn btn-warning">Sửa</a>
                                            <?php if ($user['id'] != $_SESSION['user']['id']): ?>
                                                <a href="javascript:confirmDelete(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>')" class="btn btn-danger">Xóa</a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">Không có người dùng nào</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
            </div>
        </div>
    </div>
    
    <script>
        function confirmDelete(userId, username) {
            if (confirm(`Bạn có chắc chắn muốn xóa người dùng ${username}?`)) {
                window.location.href = `index.php?controller=Admin&action=deleteUser&id=${userId}`;
            }
        }
        
        function filterUsers() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const roleFilter = document.getElementById('roleFilter').value.toLowerCase();
            const table = document.getElementById('usersTable');
            const rows = table.getElementsByTagName('tr');
            
            // Skip header row (index 0)
            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const usernameCell = row.cells[1];
                const emailCell = row.cells[2];
                const fullnameCell = row.cells[3];
                const roleCell = row.cells[4];
                
                if (!usernameCell || !emailCell || !fullnameCell || !roleCell) continue;
                
                const username = usernameCell.textContent.toLowerCase();
                const email = emailCell.textContent.toLowerCase();
                const fullname = fullnameCell.textContent.toLowerCase();
                const role = roleCell.textContent.trim().toLowerCase();
                
                const matchesSearch = searchTerm === '' || 
                    username.includes(searchTerm) || 
                    email.includes(searchTerm) || 
                    fullname.includes(searchTerm);
                const matchesRole = roleFilter === '' || role === roleFilter;
                
                if (matchesSearch && matchesRole) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        }
    </script>
</body>
</html> 
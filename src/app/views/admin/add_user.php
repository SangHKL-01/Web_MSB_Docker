<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Người Dùng Mới - MSB Shop</title>
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
        .form-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        .form-header {
            margin-bottom: 20px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 15px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        .form-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-family: inherit;
            font-size: 14px;
        }
        .form-input:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        .form-select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            background-color: #fff;
            font-family: inherit;
            font-size: 14px;
        }
        .form-textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-family: inherit;
            font-size: 14px;
            min-height: 100px;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s;
            border: none;
            font-size: 14px;
            font-family: inherit;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-primary:hover {
            background-color: #0069d9;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .form-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
            gap: 10px;
        }
        .required::after {
            content: ' *';
            color: #dc3545;
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
        /* Thêm CSS cho xác thực số điện thoại */
        .form-input.error {
            border-color: #dc3545;
        }
        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: none;
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
            <h1>Thêm Người Dùng Mới</h1>
            
            <div class="form-container">
                <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <?= $error ?>
                </div>
                <?php endif; ?>
                
                <div class="form-header">
                    <h2>Thông Tin Người Dùng</h2>
                    <p>Vui lòng điền đầy đủ thông tin bên dưới để tạo người dùng mới.</p>
                </div>
                
                <form method="POST" action="index.php?controller=Admin&action=addUser">
                    <div class="form-group">
                        <label for="username" class="form-label required">Tên đăng nhập</label>
                        <input type="text" id="username" name="username" class="form-input" required
                               value="<?= isset($data['username']) ? htmlspecialchars($data['username']) : '' ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label required">Email</label>
                        <input type="email" id="email" name="email" class="form-input" required
                               value="<?= isset($data['email']) ? htmlspecialchars($data['email']) : '' ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label required">Mật khẩu</label>
                        <input type="password" id="password" name="password" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="fullname" class="form-label">Họ và tên</label>
                        <input type="text" id="fullname" name="fullname" class="form-input"
                               value="<?= isset($data['fullname']) ? htmlspecialchars($data['fullname']) : '' ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <input type="text" id="phone" name="phone" class="form-input"
                               value="<?= isset($data['phone']) ? htmlspecialchars($data['phone']) : '' ?>">
                        <div id="phoneError" class="error-message">Số điện thoại phải chỉ chứa các ký tự số và có 10-11 số.</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address" class="form-label">Địa chỉ</label>
                        <textarea id="address" name="address" class="form-textarea"><?= isset($data['address']) ? htmlspecialchars($data['address']) : '' ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="role" class="form-label">Vai trò</label>
                        <select id="role" name="role" class="form-select">
                            <option value="user" <?= isset($data['role']) && $data['role'] == 'user' ? 'selected' : '' ?>>User</option>
                            <option value="admin" <?= isset($data['role']) && $data['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <a href="index.php?controller=Admin&action=users" class="btn btn-secondary">Hủy</a>
                        <button type="submit" class="btn btn-primary">Thêm người dùng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.getElementById('phone');
            const phoneError = document.getElementById('phoneError');
            const form = document.querySelector('form');

            // Hàm kiểm tra số điện thoại
            function validatePhone(phone) {
                // Kiểm tra số điện thoại chỉ chứa các ký tự số
                const numberPattern = /^[0-9]+$/;
                if (phone && !numberPattern.test(phone)) {
                    return false;
                }
                
                // Kiểm tra độ dài số điện thoại (10-11 số)
                if (phone && (phone.length < 10 || phone.length > 11)) {
                    return false;
                }
                
                return true;
            }

            // Kiểm tra khi người dùng nhập
            phoneInput.addEventListener('input', function() {
                const phoneValue = this.value.trim();
                
                if (phoneValue === '') {
                    // Nếu trường rỗng, không hiển thị lỗi
                    phoneInput.classList.remove('error');
                    phoneError.style.display = 'none';
                    return;
                }
                
                if (!validatePhone(phoneValue)) {
                    phoneInput.classList.add('error');
                    phoneError.style.display = 'block';
                } else {
                    phoneInput.classList.remove('error');
                    phoneError.style.display = 'none';
                }
            });

            // Kiểm tra khi submit form
            form.addEventListener('submit', function(event) {
                const phoneValue = phoneInput.value.trim();
                
                // Nếu có nhập số điện thoại và không hợp lệ
                if (phoneValue !== '' && !validatePhone(phoneValue)) {
                    event.preventDefault(); // Ngăn form submit
                    phoneInput.classList.add('error');
                    phoneError.style.display = 'block';
                    phoneInput.focus();
                }
            });
        });
    </script>
</body>
</html>
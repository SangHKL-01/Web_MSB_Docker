<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên Mật Khẩu</title>
    <link rel="stylesheet" href="/public/assets/CSS/style2.css">
</head>
<body>
    <form action="" method="POST">
        <h2>Quên mật khẩu</h2>
        <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <?php if (!empty($success)) echo "<p style='color:green;'>$success</p>"; ?>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <button type="submit">Gửi yêu cầu</button>
        <div style="margin-top: 16px; text-align: center;">
            <a href="index.php?controller=user&action=login" style="color: #007bff; text-decoration: underline;">← Quay lại đăng nhập</a>
        </div>
    </form>
</body>
</html> 
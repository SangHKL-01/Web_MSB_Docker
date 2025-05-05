<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi Mật Khẩu</title>
    <link rel="stylesheet" href="public/assets/CSS/style2.css"> <!-- Liên kết đến file CSS -->
</head>
<body>
    <form action="" method="POST">
        <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <h2>Đổi Mật Khẩu</h2>

        <label for="password">Mật khẩu cũ</label>
        <input type="password" id="password" name="password" required>

        <label for="new_password">Mật khẩu mới</label>
        <input type="password" id="new_password" name="new_password" required>

        <label for="confirm_password">Xác nhận mật khẩu mới</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit" name="submit">Đổi mật khẩu</button>
    </form>
</body>
</html>

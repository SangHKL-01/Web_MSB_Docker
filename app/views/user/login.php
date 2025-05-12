


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="public/assets/CSS/style2.css">
</head>
<form method="POST" action="">

    <?php if (isset($error)): ?>
    <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <label for="username">Username:</label>
    <input type="text" id="username" name="username" placeholder="Enter your Username" required>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" placeholder="Enter your Password" required>
    
    <div class="submit">
        <button type="submit">
            Submit
        </button>
    </div>
    <div class="register">
        <a href="index.php?controller=user&action=register">Create user</a>
    </div>
</form>
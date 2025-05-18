<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng ký</title>
  <link rel="stylesheet" href="/public/assets/CSS/style2.css">
</head>
<body>

  <form method="POST" action="">
    <?php if (isset($error)): ?>
      <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <label for="username">Username:</label>
    <input type="text" id="username" name="username" placeholder="Enter your Username" required>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" placeholder="Enter your Password" required>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" placeholder="Enter your Email" required>

    <button type="submit">Submit</button>
  </form>

</body>
</html>
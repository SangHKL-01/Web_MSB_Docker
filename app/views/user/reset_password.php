<style>
.reset-password-form {
    max-width: 400px;
    margin: 40px auto;
    padding: 30px 25px 20px 25px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.08);
    font-family: Arial, sans-serif;
}
.reset-password-form h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}
.reset-password-form label {
    display: block;
    margin-bottom: 6px;
    color: #444;
    font-weight: 500;
}
.reset-password-form input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 18px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 15px;
}
.reset-password-form button {
    width: 100%;
    padding: 10px;
    background: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.2s;
}
.reset-password-form button:hover {
    background: #0056b3;
}
.reset-password-form p {
    text-align: center;
}
</style>
<div class="reset-password-form">
    <form method="post">
        <h2>Đặt lại mật khẩu</h2>
        <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <label>Mật khẩu mới:</label>
        <input type="password" name="password" required>
        <label>Xác nhận mật khẩu mới:</label>
        <input type="password" name="confirm_password" required>
        <button type="submit">Đổi mật khẩu</button>
    </form>
</div> 
<?php
$servername = "localhost"; // MySQL server địa chỉ
$username = "root";        // Tên người dùng mặc định (trong XAMPP, nó là "root")
$password = "";            // Mật khẩu mặc định là trống trong XAMPP
$dbname = "db_product"; // Tên cơ sở dữ liệu bạn muốn kết nối

// Tạo kết nối
$conn2 = new mysqli($servername, $username, $password, $dbname);
?>
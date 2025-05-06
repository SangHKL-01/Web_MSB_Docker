<?php
/**
 * Script tự động thiết lập cơ sở dữ liệu
 * Chạy file này khi cài đặt ứng dụng lần đầu
 */

// Thông tin kết nối database
$host = 'localhost';
$username = 'root';
$password = '';

// Kết nối đến MySQL (không chọn database)
$conn = new mysqli($host, $username, $password);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

echo "<h1>Đang thiết lập cơ sở dữ liệu...</h1>";

// Đọc nội dung các file SQL
$db_web_sql = file_get_contents('db_web_setup.sql');
$db_product_sql = file_get_contents('db_product_setup.sql');

// Chạy script db_web_setup.sql
echo "<h2>Thiết lập database db_web:</h2>";
if (executeMultipleQueries($conn, $db_web_sql)) {
    echo "<p style='color: green;'>✓ Thiết lập db_web thành công</p>";
} else {
    echo "<p style='color: red;'>✗ Lỗi khi thiết lập db_web: " . $conn->error . "</p>";
}

// Chạy script db_product_setup.sql
echo "<h2>Thiết lập database db_product:</h2>";
if (executeMultipleQueries($conn, $db_product_sql)) {
    echo "<p style='color: green;'>✓ Thiết lập db_product thành công</p>";
} else {
    echo "<p style='color: red;'>✗ Lỗi khi thiết lập db_product: " . $conn->error . "</p>";
}

// Tạo thư mục uploads nếu chưa tồn tại
echo "<h2>Tạo các thư mục cần thiết:</h2>";
$directories = [
    'uploads',
    'uploads/avatars',
    'uploads/products'
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        if (mkdir($dir, 0777, true)) {
            echo "<p style='color: green;'>✓ Đã tạo thư mục $dir</p>";
        } else {
            echo "<p style='color: red;'>✗ Không thể tạo thư mục $dir</p>";
        }
    } else {
        echo "<p style='color: blue;'>ℹ Thư mục $dir đã tồn tại</p>";
    }
}

echo "<h2>Thiết lập hoàn tất!</h2>";
echo "<p>Bạn có thể truy cập ứng dụng ngay bây giờ.</p>";
echo "<p><a href='index.php' style='padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px;'>Đi đến trang chủ</a></p>";

// Đóng kết nối
$conn->close();

/**
 * Hàm thực thi nhiều câu lệnh SQL cùng lúc
 */
function executeMultipleQueries($connection, $sqlScript) {
    // Tách script thành các câu lệnh riêng biệt
    $statements = explode(';', $sqlScript);
    $success = true;
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            if (!$connection->query($statement . ';')) {
                echo "<p style='color: red;'>Lỗi: " . $connection->error . "<br>SQL: " . htmlspecialchars($statement) . "</p>";
                $success = false;
            }
        }
    }
    
    return $success;
}
?> 
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
$db_user_sql = file_get_contents('db_user_setup.sql');
$db_product_sql = file_get_contents('db_product_setup.sql');

// Hàm kiểm tra database đã tồn tại chưa
function databaseExists($conn, $dbName) {
    $result = $conn->query("SHOW DATABASES LIKE '" . $conn->real_escape_string($dbName) . "'");
    return $result && $result->num_rows > 0;
}

// Hàm kiểm tra bảng đã tồn tại trong database chưa
function tableExists($conn, $dbName, $tableName) {
    $result = $conn->query("SELECT 1 FROM information_schema.tables WHERE table_schema = '" . $conn->real_escape_string($dbName) . "' AND table_name = '" . $conn->real_escape_string($tableName) . "' LIMIT 1");
    return $result && $result->num_rows > 0;
}

// Kiểm tra db_user và bảng users
if (databaseExists($conn, 'db_user') && tableExists($conn, 'db_user', 'users')) {
    echo "<h2>Database db_user và bảng users đã tồn tại. Không cần khởi tạo lại.</h2>";
} else {
    echo "<h2>Thiết lập database db_user:</h2>";
    if (executeMultipleQueries($conn, $db_user_sql)) {
        echo "<p style='color: green;'>✓ Thiết lập db_user thành công</p>";
    } else {
        echo "<p style='color: red;'>✗ Lỗi khi thiết lập db_user: " . $conn->error . "</p>";
    }
}

// Kiểm tra db_product và bảng products
if (databaseExists($conn, 'db_product') && tableExists($conn, 'db_product', 'products')) {
    echo "<h2>Database db_product và các bảng đã tồn tại. Không cần khởi tạo lại.</h2>";
} else {
    echo "<h2>Thiết lập database db_product:</h2>";
    if (executeMultipleQueries($conn, $db_product_sql)) {
        echo "<p style='color: green;'>✓ Thiết lập db_product thành công</p>";
    } else {
        echo "<p style='color: red;'>✗ Lỗi khi thiết lập db_product: " . $conn->error . "</p>";
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
    $statements = explode(';', $sqlScript);
    $success = true;
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            if (!$connection->query($statement . ';')) {
                // Bỏ qua lỗi nếu database/table đã tồn tại
                $error = $connection->error;
                if (
                    strpos($error, 'database exists') !== false ||
                    strpos($error, 'already exists') !== false
                ) {
                    // Không báo lỗi, tiếp tục
                    continue;
                }
                echo "<p style='color: red;'>Lỗi: " . $error . "<br>SQL: " . htmlspecialchars($statement) . "</p>";
                $success = false;
            }
        }
    }
    return $success;
}
?> 
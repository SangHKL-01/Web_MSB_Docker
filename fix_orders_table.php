<?php
/**
 * Script để sửa cấu trúc bảng orders, thêm cột order_date nếu thiếu
 * Thực thi file này bằng cách truy cập http://localhost/WEB_MSB/fix_orders_table.php
 */

// Hiển thị lỗi để dễ debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Kiểm tra và sửa cấu trúc bảng orders</h1>";

try {
    // Kết nối đến database
    $conn = new mysqli("localhost", "root", "", "db_product");
    
    // Kiểm tra kết nối
    if ($conn->connect_error) {
        die("<p style='color:red'>Kết nối thất bại: " . $conn->connect_error . "</p>");
    }
    
    echo "<p style='color:green'>Kết nối thành công đến db_product!</p>";
    
    // Kiểm tra bảng orders đã tồn tại chưa
    $check_orders = $conn->query("SHOW TABLES LIKE 'orders'");
    if ($check_orders->num_rows == 0) {
        // Tạo bảng orders nếu chưa tồn tại
        $create_orders = "CREATE TABLE orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id VARCHAR(255) NOT NULL,
            total_amount DECIMAL(10, 2) DEFAULT 0,
            status VARCHAR(50) DEFAULT 'đang xử lý',
            payment_method VARCHAR(100) DEFAULT 'Thanh toán khi nhận hàng',
            customer_name VARCHAR(255) NOT NULL DEFAULT 'Khách hàng',
            customer_phone VARCHAR(20) DEFAULT '',
            customer_address TEXT,
            notes TEXT,
            order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if ($conn->query($create_orders)) {
            echo "<p style='color:green'>Đã tạo mới bảng orders thành công!</p>";
        } else {
            echo "<p style='color:red'>Lỗi khi tạo bảng orders: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>Bảng orders đã tồn tại, kiểm tra cấu trúc...</p>";
        
        // Kiểm tra các cột
        $check_columns = $conn->query("SHOW COLUMNS FROM orders");
        $existing_columns = [];
        
        while ($row = $check_columns->fetch_assoc()) {
            $existing_columns[] = $row['Field'];
        }
        
        echo "<p>Các cột hiện có: " . implode(", ", $existing_columns) . "</p>";
        
        // Kiểm tra và thêm cột order_date nếu chưa tồn tại
        if (!in_array('order_date', $existing_columns)) {
            $add_order_date = "ALTER TABLE orders ADD COLUMN order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
            
            if ($conn->query($add_order_date)) {
                echo "<p style='color:green'>Đã thêm cột order_date vào bảng orders!</p>";
                
                // Cập nhật giá trị order_date từ created_at nếu có
                if (in_array('created_at', $existing_columns)) {
                    $update_order_date = "UPDATE orders SET order_date = created_at";
                    
                    if ($conn->query($update_order_date)) {
                        echo "<p style='color:green'>Đã cập nhật giá trị order_date từ created_at!</p>";
                    } else {
                        echo "<p style='color:red'>Lỗi khi cập nhật order_date: " . $conn->error . "</p>";
                    }
                }
            } else {
                echo "<p style='color:red'>Lỗi khi thêm cột order_date: " . $conn->error . "</p>";
            }
        } else {
            echo "<p style='color:green'>Cột order_date đã tồn tại!</p>";
        }
        
        // Kiểm tra và thêm các cột thông tin khách hàng nếu chưa có
        $required_columns = [
            'customer_name' => 'VARCHAR(255) NOT NULL DEFAULT "Khách hàng"',
            'customer_phone' => 'VARCHAR(20) DEFAULT ""',
            'customer_address' => 'TEXT',
            'notes' => 'TEXT'
        ];
        
        foreach ($required_columns as $column => $definition) {
            if (!in_array($column, $existing_columns)) {
                $add_column = "ALTER TABLE orders ADD COLUMN $column $definition";
                
                if ($conn->query($add_column)) {
                    echo "<p style='color:green'>Đã thêm cột $column vào bảng orders!</p>";
                } else {
                    echo "<p style='color:red'>Lỗi khi thêm cột $column: " . $conn->error . "</p>";
                }
            } else {
                echo "<p style='color:green'>Cột $column đã tồn tại!</p>";
            }
        }
    }
    
    // Kiểm tra cấu trúc bảng order_details
    $check_details = $conn->query("SHOW TABLES LIKE 'order_details'");
    
    if ($check_details->num_rows == 0) {
        // Tạo bảng order_details nếu chưa tồn tại
        $create_details = "CREATE TABLE order_details (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            product_id INT DEFAULT NULL,
            product_name VARCHAR(255) NOT NULL,
            quantity INT NOT NULL DEFAULT 1,
            price DECIMAL(10, 2) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if ($conn->query($create_details)) {
            echo "<p style='color:green'>Đã tạo mới bảng order_details thành công!</p>";
        } else {
            echo "<p style='color:red'>Lỗi khi tạo bảng order_details: " . $conn->error . "</p>";
        }
    } else {
        echo "<p style='color:green'>Bảng order_details đã tồn tại!</p>";
    }
    
    echo "<h2>Hoàn thành!</h2>";
    echo "<p>Trở về <a href='index.php'>trang chủ</a> hoặc kiểm tra <a href='index.php?controller=product&action=history'>lịch sử đơn hàng</a></p>";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p style='color:red'>Lỗi: " . $e->getMessage() . "</p>";
} 
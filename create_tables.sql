-- Script để tạo cấu trúc database cho đơn hàng

-- Tạo database nếu chưa tồn tại
CREATE DATABASE IF NOT EXISTS db_product;

-- Sử dụng database
USE db_product;

-- Kiểm tra nếu bảng orders đã tồn tại
SET @table_exists = 0;
SELECT COUNT(*) INTO @table_exists FROM information_schema.tables 
WHERE table_schema = 'db_product' AND table_name = 'orders';

-- Nếu bảng chưa tồn tại, tạo mới
SET @query = IF(@table_exists > 0, 'SELECT "Bảng orders đã tồn tại."', 'CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT "đang xử lý",
    payment_method VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)');

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Kiểm tra nếu cột payment_method chưa tồn tại trong bảng orders
SET @column_exists = 0;
SELECT COUNT(*) INTO @column_exists FROM information_schema.columns 
WHERE table_schema = 'db_product' AND table_name = 'orders' AND column_name = 'payment_method';

-- Nếu cột chưa tồn tại, thêm vào
SET @query = IF(@column_exists > 0, 'SELECT "Cột payment_method đã tồn tại."', 'ALTER TABLE orders ADD COLUMN payment_method VARCHAR(100) AFTER status');

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tạo bảng chi tiết đơn hàng (order_details)
CREATE TABLE IF NOT EXISTS order_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10, 2) NOT NULL
);

-- Tạo bảng giỏ hàng (carts) nếu chưa tồn tại
CREATE TABLE IF NOT EXISTS carts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT,
    product_name VARCHAR(255),
    name VARCHAR(255),
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10, 2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

SELECT 'Quá trình sửa lỗi đã hoàn tất. Bảng orders và cột payment_method đã được kiểm tra và cập nhật nếu cần.' AS Message;

-- Hướng dẫn sử dụng:
-- 1. Mở phpMyAdmin: http://localhost/phpmyadmin
-- 2. Vào tab "SQL"
-- 3. Sao chép và dán toàn bộ script này
-- 4. Nhấn nút "Go" hoặc "Thực hiện" để chạy script 
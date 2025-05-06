-- Script thiết lập database db_product
-- Tạo database
CREATE DATABASE db_product;

-- Sử dụng database
USE db_product;

-- Tạo bảng products
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    category VARCHAR(100),
    stock INT DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tạo bảng carts
CREATE TABLE carts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT,
    product_name VARCHAR(255),
    name VARCHAR(255),
    name_product VARCHAR(255),
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10, 2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tạo bảng orders
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'đang xử lý',
    payment_method VARCHAR(100),
    customer_name VARCHAR(255) DEFAULT 'Khách hàng',
    customer_phone VARCHAR(20) DEFAULT '',
    customer_address TEXT,
    notes TEXT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tạo bảng order_details
CREATE TABLE order_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10, 2) NOT NULL
);

-- Thêm dữ liệu mẫu vào bảng products
INSERT INTO products (name, description, price, image, category, stock)
VALUES 
('Điện thoại Samsung Galaxy S21', 'Điện thoại Samsung Galaxy S21 mới nhất với camera tuyệt vời', 20990000, 'product1.jpg', 'Điện thoại', 15),
('Laptop MacBook Pro M1', 'MacBook Pro với chip M1 mạnh mẽ, màn hình Retina', 35990000, 'product2.jpg', 'Laptop', 8),
('Tai nghe Bluetooth Apple AirPods Pro', 'Tai nghe không dây với chống ồn chủ động', 5990000, 'product3.jpg', 'Phụ kiện', 20),
('Đồng hồ thông minh Apple Watch Series 7', 'Đồng hồ thông minh với màn hình luôn bật', 10990000, 'product4.jpg', 'Đồng hồ thông minh', 12),
('Máy tính bảng iPad Air', 'iPad Air mỏng nhẹ với chip A14 Bionic', 16990000, 'product5.jpg', 'Máy tính bảng', 10);

-- Thông báo hoàn thành
SELECT 'Thiết lập database db_product và các bảng thành công!' AS Message; 
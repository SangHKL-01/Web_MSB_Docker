-- Script thiết lập database db_user
-- Tạo database nếu chưa tồn tại
CREATE DATABASE IF NOT EXISTS db_user;

-- Sử dụng database
USE db_user;

-- Tạo bảng users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role VARCHAR(20) DEFAULT 'user',
    fullname VARCHAR(100),
    ngay_sinh DATE,
    gioi_tinh VARCHAR(10),
    phone VARCHAR(20),
    address VARCHAR(255),
    avatar VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    reset_token VARCHAR(64) DEFAULT NULL,
    reset_token_expiry DATETIME DEFAULT NULL
);

-- Kiểm tra và tạo thư mục avatars
-- (Lưu ý: Trong SQL không thể tạo thư mục, cần thực hiện bằng PHP hoặc thủ công)

-- Thêm dữ liệu mẫu vào bảng users
INSERT INTO users (username, password, email, role, fullname, ngay_sinh, gioi_tinh, phone, address, created_at)
VALUES 
('admin', '$2y$10$B5/X4PsJG3e2DawJbLhlZ.OTp9ai5dLPPoEEnKbd3a95f03aEbTuq', 'admin@example.com', 'admin', 'Quản trị viên', '1990-01-01', 'Nam', '0123456789', 'Hà Nội', NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- Thông báo hoàn thành
SELECT 'Thiết lập database db_user và bảng users thành công!' AS Message; 
-- Sử dụng database db_product
USE db_product;

-- Kiểm tra xem bảng carts đã tồn tại chưa, nếu có thì xóa
DROP TABLE IF EXISTS carts;

-- Tạo bảng carts
CREATE TABLE carts (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) NOT NULL,
  product_name VARCHAR(255) NOT NULL,
  quantity INT(11) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thông báo hoàn thành
SELECT 'Bảng carts đã được tạo thành công!' AS Message; 
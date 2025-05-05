-- Sử dụng database db_product
USE db_product;

-- Kiểm tra cấu trúc bảng hiện tại
DESCRIBE carts;

-- Nếu bảng carts không tồn tại, hãy tạo nó
CREATE TABLE IF NOT EXISTS carts (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) NOT NULL,
  product_name VARCHAR(255) NOT NULL,
  quantity INT(11) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Nếu bảng đã tồn tại nhưng không có cột product_name, hãy thêm nó vào
-- Kiểm tra xem cột product_name đã tồn tại trong bảng carts chưa
SELECT COUNT(*) INTO @product_name_exists 
FROM information_schema.columns 
WHERE table_schema = 'db_product' AND table_name = 'carts' AND column_name = 'product_name';

-- Thêm cột nếu nó không tồn tại
SET @query = IF(@product_name_exists = 0, 'ALTER TABLE carts ADD COLUMN product_name VARCHAR(255) NOT NULL AFTER user_id', 'SELECT "Column product_name already exists."');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt; 
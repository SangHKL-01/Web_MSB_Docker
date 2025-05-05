-- Sử dụng database DB_WEB
USE DB_WEB;

-- Kiểm tra xem cột phone đã tồn tại trong bảng users chưa
SELECT COUNT(*) INTO @exists FROM information_schema.columns 
WHERE table_schema = 'DB_WEB' AND table_name = 'users' AND column_name = 'phone';

-- Nếu chưa tồn tại thì thêm cột phone
SET @query = IF(@exists = 0, 'ALTER TABLE users ADD COLUMN phone VARCHAR(20) DEFAULT NULL', 'SELECT "Cột phone đã tồn tại."');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Thông báo hoàn thành
SELECT 'Cập nhật cấu trúc bảng users thành công!' AS message; 
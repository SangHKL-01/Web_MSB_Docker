<?php
require_once 'app/models/BaseModel.php';

include 'db/db_products.php';

class Product_Model extends BaseModel {
    
    public function __construct() {
        parent::__construct("products", true);
        // Ensure tables exist when model is instantiated
        $this->initializeOrderTables();
    }
    
    // Initialize all necessary order-related tables
    private function initializeOrderTables() {
        // Sử dụng kết nối từ Database class
        $productDb = Database::getProductInstance();
        $conn = $productDb->getConnection();
        
        // Kiểm tra kết nối
        if (!$conn) {
            error_log("Database connection error in initializeOrderTables");
            return false;
        }
        
        // Kiểm tra và tạo database nếu chưa tồn tại
        try {
            // Kết nối không có database để tạo database nếu cần
            $tempConn = new mysqli("localhost", "root", "");
            if ($tempConn->connect_error) {
                error_log("Connection failed: " . $tempConn->connect_error);
                return false;
            }
            
            // Kiểm tra xem database có tồn tại không
            $dbResult = $tempConn->query("SHOW DATABASES LIKE 'db_product'");
            if ($dbResult->num_rows == 0) {
                // Tạo database nếu không tồn tại
                if (!$tempConn->query("CREATE DATABASE db_product")) {
                    error_log("Error creating database: " . $tempConn->error);
                    return false;
                }
                error_log("Database db_product created successfully");
            }
            
            $tempConn->close();
        } catch (Exception $e) {
            error_log("Error checking/creating database: " . $e->getMessage());
        }
        
        // Kiểm tra và tạo bảng orders nếu chưa tồn tại
        $check_orders = $conn->query("SHOW TABLES LIKE 'orders'");
        if ($check_orders && $check_orders->num_rows == 0) {
            $create_orders = "CREATE TABLE orders (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                total_amount DECIMAL(10, 2) NOT NULL,
                status VARCHAR(50) DEFAULT 'đang xử lý',
                payment_method VARCHAR(100),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            
            if (!$conn->query($create_orders)) {
                error_log("Error creating orders table: " . $conn->error);
                return false;
            }
            
            error_log("Successfully created orders table");
        } else {
            // Bảng đã tồn tại, kiểm tra xem cột payment_method có tồn tại không
            $check_payment_method = $conn->query("SHOW COLUMNS FROM orders LIKE 'payment_method'");
            if ($check_payment_method && $check_payment_method->num_rows == 0) {
                // Thêm cột payment_method nếu chưa tồn tại
                $add_column = "ALTER TABLE orders ADD COLUMN payment_method VARCHAR(100) AFTER status";
                if (!$conn->query($add_column)) {
                    error_log("Error adding payment_method column: " . $conn->error);
                }
                error_log("Added payment_method column to orders table");
            }
            
            // Kiểm tra xem cột order_date có tồn tại không
            $check_order_date = $conn->query("SHOW COLUMNS FROM orders LIKE 'order_date'");
            if ($check_order_date && $check_order_date->num_rows == 0) {
                // Thêm cột order_date nếu chưa tồn tại và bảng có cột created_at
                $check_created_at = $conn->query("SHOW COLUMNS FROM orders LIKE 'created_at'");
                if ($check_created_at && $check_created_at->num_rows > 0) {
                    // Thêm order_date và sao chép giá trị từ created_at
                    $add_column = "ALTER TABLE orders ADD COLUMN order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
                    if ($conn->query($add_column)) {
                        $update_column = "UPDATE orders SET order_date = created_at";
                        $conn->query($update_column);
                        error_log("Added order_date column to orders table and copied values from created_at");
                    } else {
                        error_log("Error adding order_date column: " . $conn->error);
                    }
                } else {
                    // Thêm order_date nếu không có cột created_at
                    $add_column = "ALTER TABLE orders ADD COLUMN order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
                    if (!$conn->query($add_column)) {
                        error_log("Error adding order_date column: " . $conn->error);
                    } else {
                        error_log("Added order_date column to orders table");
                    }
                }
            }
            
            // Kiểm tra các trường thông tin khách hàng
            $required_customer_columns = [
                'customer_name' => 'VARCHAR(255) NOT NULL DEFAULT "Khách hàng"',
                'customer_phone' => 'VARCHAR(20) DEFAULT ""',
                'customer_address' => 'TEXT',
                'notes' => 'TEXT'
            ];
            
            foreach ($required_customer_columns as $column => $definition) {
                $check_column = $conn->query("SHOW COLUMNS FROM orders LIKE '$column'");
                if ($check_column && $check_column->num_rows == 0) {
                    $add_column = "ALTER TABLE orders ADD COLUMN $column $definition";
                    if (!$conn->query($add_column)) {
                        error_log("Error adding $column column: " . $conn->error);
                    } else {
                        error_log("Added $column column to orders table");
                    }
                }
            }
        }
        
        // Kiểm tra và tạo bảng order_details nếu chưa tồn tại
        $check_details = $conn->query("SHOW TABLES LIKE 'order_details'");
        if ($check_details && $check_details->num_rows == 0) {
            $create_details = "CREATE TABLE order_details (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_id INT NOT NULL,
                product_id INT,
                product_name VARCHAR(255) NOT NULL,
                quantity INT NOT NULL DEFAULT 1,
                price DECIMAL(10, 2) NOT NULL
            )";
            
            if (!$conn->query($create_details)) {
                error_log("Error creating order_details table: " . $conn->error);
                return false;
            }
            
            error_log("Successfully created order_details table");
        }
        
        return true;
    }
    
    public function Get_product($id) {
        return $this->getById($id);
    }
    
    public function List_product($user_id) {
        $productDb = Database::getProductInstance();
        $conn = $productDb->getConnection();
        
        if (!$conn) {
            error_log("Database connection error in List_product");
            return [];
        }
        
        $user_id = $conn->real_escape_string($user_id);
        
        // Kiểm tra bảng carts đã tồn tại chưa
        $checkTableSQL = "SHOW TABLES LIKE 'carts'";
        $tableExists = $conn->query($checkTableSQL);
        
        if (!$tableExists || $tableExists->num_rows == 0) {
            error_log("Table carts does not exist");
            return [];
        }
        
        // Kiểm tra cột price đã tồn tại trong bảng carts chưa
        $checkPriceColumnSQL = "SHOW COLUMNS FROM carts LIKE 'price'";
        $priceColumnExists = $conn->query($checkPriceColumnSQL);
        
        // Kiểm tra cột product_id đã tồn tại trong bảng carts chưa
        $checkProductIdColumnSQL = "SHOW COLUMNS FROM carts LIKE 'product_id'";
        $productIdColumnExists = $conn->query($checkProductIdColumnSQL);
        
        // Truy vấn cơ bản để lấy danh sách sản phẩm trong giỏ hàng
        $sql = "SELECT * FROM carts WHERE user_id = '$user_id'";
        $result = $conn->query($sql);
        
        if (!$result) {
            error_log("Error retrieving cart items: " . $conn->error);
            return [];
        }
        
        $products = [];
        while ($row = $result->fetch_assoc()) {
            // Nếu không có giá trong giỏ hàng hoặc giá = 0, cần lấy giá từ bảng products
            if ((!$priceColumnExists || $priceColumnExists->num_rows == 0 || $row['price'] == 0) && 
                ($productIdColumnExists && $productIdColumnExists->num_rows > 0 && isset($row['product_id']))) {
                
                $product_id = $conn->real_escape_string($row['product_id']);
                $productSql = "SELECT id, price, name FROM products WHERE id = '$product_id'";
                $productResult = $conn->query($productSql);
                
                if ($productResult && $productResult->num_rows > 0) {
                    $productInfo = $productResult->fetch_assoc();
                    $row['price'] = $productInfo['price'];
                    $row['product_id'] = $productInfo['id']; // Đảm bảo có product_id
                    
                    // Nếu name_product không tồn tại hoặc rỗng, sử dụng tên từ bảng products
                    if (empty($row['name_product'])) {
                        $row['name_product'] = $productInfo['name'];
                    }
                    
                    error_log("Retrieved price {$productInfo['price']} for product ID {$product_id}");
                    
                    // Cập nhật giá trong giỏ hàng nếu cột price tồn tại
                    if ($priceColumnExists && $priceColumnExists->num_rows > 0) {
                        $updateSql = "UPDATE carts SET price = '{$productInfo['price']}' WHERE id = '{$row['id']}'";
                        $conn->query($updateSql);
                    }
                }
            }
            
            // Đảm bảo các trường cần thiết luôn tồn tại
            if (!isset($row['price']) || $row['price'] == 0) {
                $row['price'] = 10000; // Giá mặc định nếu không tìm thấy
                error_log("Setting default price for cart item ID: {$row['id']}");
            }
            
            $products[] = $row;
        }
        
        return $products;
    }

    public function history($user_id) {
        // Sử dụng kết nối từ Database class
        $productDb = Database::getProductInstance();
        $conn = $productDb->getConnection();
        
        // Kiểm tra kết nối
        if (!$conn) {
            error_log("Database connection error in history");
            return [];
        }
        
        // Đảm bảo bảng đã được tạo
        if (!$this->initializeOrderTables()) {
            error_log("Could not initialize order tables in history");
            return [];
        }
        
        // Lấy tất cả đơn hàng của người dùng
        $user_id = $conn->real_escape_string($user_id);
        
        // Kiểm tra xem cột order_date có tồn tại không
        $checkOrderDateColumn = "SHOW COLUMNS FROM orders LIKE 'order_date'";
        $orderDateExists = $conn->query($checkOrderDateColumn);
        
        // Kiểm tra xem cột created_at có tồn tại không
        $checkCreatedAtColumn = "SHOW COLUMNS FROM orders LIKE 'created_at'";
        $createdAtExists = $conn->query($checkCreatedAtColumn);
        
        // Quyết định cột nào dùng để sắp xếp
        if ($orderDateExists && $orderDateExists->num_rows > 0) {
            $sql = "SELECT o.* FROM orders o WHERE o.user_id = '$user_id' ORDER BY o.order_date DESC";
        } elseif ($createdAtExists && $createdAtExists->num_rows > 0) {
            $sql = "SELECT o.* FROM orders o WHERE o.user_id = '$user_id' ORDER BY o.created_at DESC";
        } else {
            // Nếu không có cột thời gian nào, không sắp xếp
            $sql = "SELECT o.* FROM orders o WHERE o.user_id = '$user_id'";
        }
        
        $result = $conn->query($sql);
        
        if (!$result) {
            error_log("SQL Error in history: " . $conn->error);
            error_log("SQL Query: " . $sql);
            return [];
        }
        
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            // Lấy chi tiết đơn hàng
            $order_id = $row['id'];
            $details_sql = "SELECT * FROM order_details WHERE order_id = '$order_id'";
            $details_result = $conn->query($details_sql);
            
            $items = [];
            if ($details_result && $details_result->num_rows > 0) {
                while ($detail = $details_result->fetch_assoc()) {
                    $items[] = $detail;
                }
            }
            
            $row['items'] = $items;
            
            // Đảm bảo các trường thông tin khách hàng luôn có trong kết quả
            if (!isset($row['customer_name'])) {
                $row['customer_name'] = 'Khách hàng';
            }
            if (!isset($row['customer_phone'])) {
                $row['customer_phone'] = '';
            }
            if (!isset($row['customer_address'])) {
                $row['customer_address'] = '';
            }
            if (!isset($row['payment_method'])) {
                $row['payment_method'] = 'Thanh toán khi nhận hàng';
            }
            if (!isset($row['notes'])) {
                $row['notes'] = '';
            }
            
            $orders[] = $row;
        }
        
        return $orders;
    }

    // Thêm sản phẩm vào giỏ hàng
    public function insert_cart($user_id, $name_product, $quantity, $price = 0, $product_id = null) {
        $productDb = Database::getProductInstance();
        $conn = $productDb->getConnection();
        
        if (!$conn) {
            error_log("Database connection error in insert_cart");
            return false;
        }
        
        // Escape dữ liệu
        $user_id = $conn->real_escape_string($user_id);
        $name_product = $conn->real_escape_string($name_product);
        $quantity = $conn->real_escape_string($quantity);
        $price = $conn->real_escape_string($price);
        $product_id = $product_id ? $conn->real_escape_string($product_id) : null;
        
        // Đảm bảo bảng carts tồn tại với cấu trúc phù hợp
        $this->ensureCartTable($conn);
        
        // Nếu không có product_id, thử lấy từ tên sản phẩm
        if (!$product_id) {
            $query = "SELECT id, price FROM products WHERE name = '$name_product' LIMIT 1";
            $result = $conn->query($query);
            
            if ($result && $result->num_rows > 0) {
                $productInfo = $result->fetch_assoc();
                $product_id = $productInfo['id'];
                
                // Sử dụng giá từ bảng products nếu giá truyền vào là 0
                if ($price == 0 || empty($price)) {
                    $price = $productInfo['price'];
                    error_log("Retrieved price $price from products table for product: $name_product");
                }
            }
        } else {
            // Nếu có product_id nhưng giá = 0, lấy giá từ bảng products
            if ($price == 0 || empty($price)) {
                $query = "SELECT price FROM products WHERE id = '$product_id' LIMIT 1";
                $result = $conn->query($query);
                
                if ($result && $result->num_rows > 0) {
                    $productInfo = $result->fetch_assoc();
                    $price = $productInfo['price'];
                    error_log("Retrieved price $price for product ID: $product_id");
                }
            }
        }
        
        // Kiểm tra sản phẩm đã tồn tại trong giỏ hàng chưa
        $checkExist = "SELECT id, quantity FROM carts WHERE user_id = '$user_id' AND product_id = '$product_id'";
        $existResult = $conn->query($checkExist);
        
        if ($existResult && $existResult->num_rows > 0) {
            // Nếu sản phẩm đã tồn tại, cập nhật số lượng
            $existItem = $existResult->fetch_assoc();
            $newQuantity = $existItem['quantity'] + $quantity;
            $cartId = $existItem['id'];
            
            $updateSql = "UPDATE carts SET quantity = '$newQuantity' WHERE id = '$cartId'";
            $result = $conn->query($updateSql);
            
            if (!$result) {
                error_log("SQL Error in updating cart: " . $conn->error);
                error_log("SQL Query: " . $updateSql);
                return false;
            }
            
            return true;
        }
        
        // Thêm mới sản phẩm vào giỏ hàng
        $insertSql = "INSERT INTO carts (user_id, product_id, product_name, quantity, price) 
                     VALUES ('$user_id', " . ($product_id ? "'$product_id'" : "NULL") . ", '$name_product', '$quantity', '$price')";
        
        $result = $conn->query($insertSql);
        
        if (!$result) {
            error_log("SQL Error in insert_cart: " . $conn->error);
            error_log("SQL Query: " . $insertSql);
            return false;
        }
        
        error_log("Added product to cart: ID=$product_id, Name=$name_product, Price=$price");
        return true;
    }
    
    // Tạo bảng carts nếu chưa tồn tại
    private function ensureCartTable($conn) {
        // Kiểm tra bảng carts đã tồn tại chưa
        $checkTableSQL = "SHOW TABLES LIKE 'carts'";
        $tableExists = $conn->query($checkTableSQL);
        
        if (!$tableExists || $tableExists->num_rows == 0) {
            $createTableSQL = "CREATE TABLE carts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id VARCHAR(50) NOT NULL,
                product_id INT DEFAULT NULL,
                product_name VARCHAR(255) NOT NULL,
                quantity INT NOT NULL DEFAULT 1,
                price DECIMAL(10,2) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            
            if (!$conn->query($createTableSQL)) {
                error_log("Error creating carts table: " . $conn->error);
                return false;
            }
            
            error_log("Created carts table successfully");
        }
        
        // Kiểm tra cột product_id đã tồn tại chưa
        $checkProdIdColumn = "SHOW COLUMNS FROM carts LIKE 'product_id'";
        $prodIdExists = $conn->query($checkProdIdColumn);
        
        if (!$prodIdExists || $prodIdExists->num_rows == 0) {
            $addColumnSQL = "ALTER TABLE carts ADD COLUMN product_id INT DEFAULT NULL AFTER user_id";
            if (!$conn->query($addColumnSQL)) {
                error_log("Error adding product_id column: " . $conn->error);
            }
        }
        
        // Kiểm tra cột price đã tồn tại chưa
        $checkPriceColumn = "SHOW COLUMNS FROM carts LIKE 'price'";
        $priceExists = $conn->query($checkPriceColumn);
        
        if (!$priceExists || $priceExists->num_rows == 0) {
            $addColumnSQL = "ALTER TABLE carts ADD COLUMN price DECIMAL(10,2) DEFAULT 0 AFTER quantity";
            if (!$conn->query($addColumnSQL)) {
                error_log("Error adding price column: " . $conn->error);
            }
        }
        
        return true;
    }

    // Tìm kiếm sản phẩm theo từ khóa
    public function searchProducts($keyword) {
        // Sử dụng kết nối từ Database class
        $productDb = Database::getProductInstance();
        $conn = $productDb->getConnection();
        
        // Kiểm tra kết nối
        if (!$conn) {
            error_log("Database connection error in searchProducts");
            return [];
        }
        
        // Escape từ khóa để tránh SQL Injection
        $keyword = $conn->real_escape_string($keyword);
        
        // Tìm kiếm trong tên và mô tả sản phẩm
        $sql = "SELECT * FROM products WHERE name LIKE '%$keyword%' OR description LIKE '%$keyword%'";
        $result = $conn->query($sql);
        
        if (!$result) {
            error_log("SQL Error in searchProducts: " . $conn->error);
            error_log("SQL Query: " . $sql);
            return [];
        }
        
        $products = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
        
        return $products;
    }
    
    // Lỗ hổng: File upload không kiểm tra loại tệp
    public function uploadProductImage($file) {
        $target_dir = "uploads/products/";
        $target_file = $target_dir . basename($file["name"]);
        
        // Lỗ hổng: Không kiểm tra loại tệp - cho phép tải lên PHP shell
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return $target_file;
        }
        
        return false;
    }
    
    // Lỗ hổng: Command Injection
    public function generateProductThumbnail($image_path, $width, $height) {
        // Lỗ hổng command injection thông qua đầu vào người dùng không được lọc
        $output_path = "uploads/products/thumbnails/" . basename($image_path);
        $command = "convert $image_path -resize {$width}x{$height} $output_path";
        
        // Thực thi lệnh hệ thống mà không lọc đầu vào
        system($command);
        
        return $output_path;
    }
    
    // Lỗ hổng: Insecure Direct Object Reference (IDOR)
    public function getUserProduct($product_id, $user_id) {
        // Không kiểm tra xem sản phẩm có thuộc về người dùng hay không
        return $this->getById($product_id);
    }
    
    // Lỗ hổng: Blind SQL Injection trong sắp xếp/lọc sản phẩm
    public function getProductsWithSorting($sort_column, $sort_direction) {
        // Không kiểm tra/escape các tham số sắp xếp
        $sql = "SELECT * FROM $this->table ORDER BY $sort_column $sort_direction";
        $result = $this->db->query($sql);
        $data = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        
        return $data;
    }
    
    // Lưu sản phẩm đã xem gần đây
    public function saveRecentlyViewedProduct($product_id) {
        // Kiểm tra và tạo mảng các sản phẩm đã xem
        $recent_products = isset($_COOKIE['recent_products']) ? $_COOKIE['recent_products'] : '';
        $recent_array = $recent_products ? explode(',', $recent_products) : [];
        
        // Kiểm tra xem sản phẩm đã tồn tại trong danh sách chưa
        $product_id = (int)$product_id; // Đảm bảo product_id là số nguyên
        
        // Nếu sản phẩm đã tồn tại, xóa nó khỏi mảng để thêm vào đầu danh sách
        if (in_array($product_id, $recent_array)) {
            $recent_array = array_diff($recent_array, [$product_id]);
        }
        
        // Thêm sản phẩm mới vào đầu danh sách
        array_unshift($recent_array, $product_id);
        
        // Giới hạn chỉ lưu tối đa 10 sản phẩm gần nhất
        if (count($recent_array) > 10) {
            $recent_array = array_slice($recent_array, 0, 10);
        }
        
        // Chuyển mảng thành chuỗi và lưu vào cookie
        $recent_products = implode(',', $recent_array);
        
        // Đặt cookie với thời hạn 30 ngày
        setcookie('recent_products', $recent_products, time() + 3600 * 24 * 30, '/', '', false, true);
        
        return $recent_array; // Trả về mảng các sản phẩm đã xem gần đây
    }
    
    // Lấy danh sách sản phẩm đã xem gần đây
    public function getRecentlyViewedProducts() {
        $recent_products = isset($_COOKIE['recent_products']) ? $_COOKIE['recent_products'] : '';
        
        if (empty($recent_products)) {
            return [];
        }
        
        $recent_array = explode(',', $recent_products);
        $products = [];
        
        // Kết nối database
        $productDb = Database::getProductInstance();
        $conn = $productDb->getConnection();
        
        if (!$conn) {
            error_log("Database connection error in getRecentlyViewedProducts");
            return [];
        }
        
        // Lấy thông tin chi tiết cho mỗi sản phẩm
        foreach ($recent_array as $product_id) {
            $product_id = (int)$product_id; // Đảm bảo product_id là số nguyên
            
            $query = "SELECT * FROM products WHERE id = $product_id LIMIT 1";
            $result = $conn->query($query);
            
            if ($result && $result->num_rows > 0) {
                $products[] = $result->fetch_assoc();
            }
        }
        
        return $products;
    }

    // Xóa sản phẩm khỏi giỏ hàng
    public function remove_from_cart($cart_id, $user_id) {
        // Sử dụng database product
        $productDb = Database::getProductInstance();
        
        // Xóa sản phẩm từ giỏ hàng, đảm bảo chỉ xóa sản phẩm của người dùng hiện tại
        $sql = "DELETE FROM carts WHERE id = '$cart_id' AND user_id = '$user_id'";
        $result = $productDb->query($sql);
        
        return $result;
    }

    // Tạo đơn hàng mới
    public function createOrder($data) {
        $productDb = Database::getProductInstance();
        $conn = $productDb->getConnection();
        
        if (!$conn) {
            error_log("Database connection error in createOrder");
            return false;
        }
        
        // Kiểm tra bảng orders đã tồn tại chưa, nếu chưa thì tạo
        $checkTableSQL = "SHOW TABLES LIKE 'orders'";
        $tableExists = $conn->query($checkTableSQL);
        
        if (!$tableExists || $tableExists->num_rows == 0) {
            $createTableSQL = "CREATE TABLE orders (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id VARCHAR(255) NOT NULL,
                total_amount DECIMAL(10,2) DEFAULT 0,
                customer_name VARCHAR(255) NOT NULL,
                customer_phone VARCHAR(20) NOT NULL,
                customer_address TEXT NOT NULL,
                payment_method VARCHAR(50) DEFAULT 'COD',
                notes TEXT,
                order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            
            if (!$conn->query($createTableSQL)) {
                error_log("Error creating orders table: " . $conn->error);
                return false;
            }
        }
        
        // Escape dữ liệu
        $user_id = $conn->real_escape_string($data['user_id']);
        $total_amount = isset($data['total_amount']) ? $conn->real_escape_string($data['total_amount']) : '0';
        $customer_name = $conn->real_escape_string($data['customer_name']);
        $customer_phone = $conn->real_escape_string($data['customer_phone']);
        $customer_address = $conn->real_escape_string($data['customer_address']);
        $payment_method = isset($data['payment_method']) ? $conn->real_escape_string($data['payment_method']) : 'COD';
        $notes = isset($data['notes']) ? $conn->real_escape_string($data['notes']) : '';
        
        // Kiểm tra cột payment_method đã tồn tại chưa
        $checkPaymentColumn = "SHOW COLUMNS FROM orders LIKE 'payment_method'";
        $paymentColumnExists = $conn->query($checkPaymentColumn);
        
        // Kiểm tra cột total_amount đã tồn tại chưa
        $checkTotalColumn = "SHOW COLUMNS FROM orders LIKE 'total_amount'";
        $totalColumnExists = $conn->query($checkTotalColumn);
        
        if ($paymentColumnExists && $paymentColumnExists->num_rows > 0 && 
            $totalColumnExists && $totalColumnExists->num_rows > 0) {
            // Có cả hai cột
            $sql = "INSERT INTO orders (user_id, total_amount, customer_name, customer_phone, customer_address, payment_method, notes) 
                    VALUES ('$user_id', '$total_amount', '$customer_name', '$customer_phone', '$customer_address', '$payment_method', '$notes')";
        } elseif ($totalColumnExists && $totalColumnExists->num_rows > 0) {
            // Chỉ có cột total_amount
            $sql = "INSERT INTO orders (user_id, total_amount, customer_name, customer_phone, customer_address, notes) 
                    VALUES ('$user_id', '$total_amount', '$customer_name', '$customer_phone', '$customer_address', '$notes')";
        } elseif ($paymentColumnExists && $paymentColumnExists->num_rows > 0) {
            // Chỉ có cột payment_method
            $sql = "INSERT INTO orders (user_id, customer_name, customer_phone, customer_address, payment_method, notes) 
                    VALUES ('$user_id', '$customer_name', '$customer_phone', '$customer_address', '$payment_method', '$notes')";
        } else {
            // Không có cả hai cột
            $sql = "INSERT INTO orders (user_id, customer_name, customer_phone, customer_address, notes) 
                    VALUES ('$user_id', '$customer_name', '$customer_phone', '$customer_address', '$notes')";
        }
        
        if ($conn->query($sql)) {
            $order_id = $conn->insert_id;
            return $order_id;
        } else {
            error_log("Error creating order: " . $conn->error);
            error_log("SQL Query: " . $sql);
            return false;
        }
    }
    
    // Tạo chi tiết đơn hàng
    public function createOrderDetail($data) {
        $productDb = Database::getProductInstance();
        $conn = $productDb->getConnection();
        
        if (!$conn) {
            error_log("Database connection error in createOrderDetail");
            return false;
        }
        
        // Kiểm tra bảng order_details đã tồn tại chưa, nếu chưa thì tạo
        $checkTableSQL = "SHOW TABLES LIKE 'order_details'";
        $tableExists = $conn->query($checkTableSQL);
        
        if (!$tableExists || $tableExists->num_rows == 0) {
            $createTableSQL = "CREATE TABLE order_details (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_id INT NOT NULL,
                product_id INT DEFAULT NULL,
                product_name VARCHAR(255) NOT NULL,
                quantity INT NOT NULL,
                price DECIMAL(10,2) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            
            if (!$conn->query($createTableSQL)) {
                error_log("Error creating order_details table: " . $conn->error);
                return false;
            }
        }
        
        // Escape dữ liệu
        $order_id = $conn->real_escape_string($data['order_id']);
        $product_name = $conn->real_escape_string($data['product_name']);
        $quantity = $conn->real_escape_string($data['quantity']);
        $price = isset($data['price']) ? $conn->real_escape_string($data['price']) : '0';
        $product_id = isset($data['product_id']) ? $conn->real_escape_string($data['product_id']) : 'NULL';
        
        // LUÔN lấy giá từ bảng products nếu có product_id, bất kể giá truyền vào là bao nhiêu
        if ($product_id != 'NULL') {
            $productSql = "SELECT price FROM products WHERE id = '$product_id'";
            $productResult = $conn->query($productSql);
            
            if ($productResult && $productResult->num_rows > 0) {
                $productInfo = $productResult->fetch_assoc();
                $price = $conn->real_escape_string($productInfo['price']);
                error_log("Using price $price from products table for order detail of product ID $product_id");
            } else {
                error_log("Warning: Product with ID $product_id not found in products table");
            }
        }
        
        // Nếu vẫn không có giá, ghi log cảnh báo
        if ($price == 0 || empty($price)) {
            error_log("Warning: Creating order detail with zero price for product: $product_name");
            
            // Thử tìm giá dựa trên tên sản phẩm
            $productNameSql = "SELECT price FROM products WHERE name = '$product_name' LIMIT 1";
            $productNameResult = $conn->query($productNameSql);
            
            if ($productNameResult && $productNameResult->num_rows > 0) {
                $productInfo = $productNameResult->fetch_assoc();
                $price = $conn->real_escape_string($productInfo['price']);
                error_log("Found price $price by product name for order detail");
            } else {
                // Đặt giá mặc định
                $price = 10000;
                error_log("Setting default price 10000 for product: $product_name");
            }
        }
        
        // Kiểm tra cột price đã tồn tại chưa
        $checkPriceColumn = "SHOW COLUMNS FROM order_details LIKE 'price'";
        $priceColumnExists = $conn->query($checkPriceColumn);
        
        // Kiểm tra cột product_id đã tồn tại chưa
        $checkProductIdColumn = "SHOW COLUMNS FROM order_details LIKE 'product_id'";
        $productIdColumnExists = $conn->query($checkProductIdColumn);
        
        // Tạo SQL dựa trên cấu trúc bảng
        if ($productIdColumnExists && $productIdColumnExists->num_rows > 0) {
            // Bảng có cột product_id
            if ($priceColumnExists && $priceColumnExists->num_rows > 0) {
                // Bảng có cả cột price và product_id
                $sql = "INSERT INTO order_details (order_id, product_id, product_name, quantity, price) 
                        VALUES ('$order_id', $product_id, '$product_name', '$quantity', '$price')";
            } else {
                // Bảng chỉ có cột product_id
                $sql = "INSERT INTO order_details (order_id, product_id, product_name, quantity) 
                        VALUES ('$order_id', $product_id, '$product_name', '$quantity')";
            }
        } else {
            // Bảng không có cột product_id
            if ($priceColumnExists && $priceColumnExists->num_rows > 0) {
                // Bảng chỉ có cột price
                $sql = "INSERT INTO order_details (order_id, product_name, quantity, price) 
                        VALUES ('$order_id', '$product_name', '$quantity', '$price')";
            } else {
                // Bảng không có cả hai cột
                $sql = "INSERT INTO order_details (order_id, product_name, quantity) 
                        VALUES ('$order_id', '$product_name', '$quantity')";
            }
        }
        
        if ($conn->query($sql)) {
            $insert_id = $conn->insert_id;
            error_log("Created order detail ID $insert_id with price $price");
            return $insert_id;
        } else {
            error_log("Error creating order detail: " . $conn->error);
            error_log("SQL Query: " . $sql);
            return false;
        }
    }
    
    // Xóa giỏ hàng sau khi đặt hàng
    public function clearCart($user_id) {
        // Sử dụng kết nối từ Database class
        $productDb = Database::getProductInstance();
        $conn = $productDb->getConnection();
        
        // Kiểm tra kết nối
        if (!$conn) {
            error_log("Database connection error in clearCart");
            return false;
        }
        
        // Escape dữ liệu
        $user_id = $conn->real_escape_string($user_id);
        
        $sql = "DELETE FROM carts WHERE user_id = '$user_id'";
        $result = $conn->query($sql);
        
        if (!$result) {
            error_log("SQL Error in clearCart: " . $conn->error);
            error_log("SQL Query: " . $sql);
        }
        
        return $result;
    }

    // Lấy danh sách đơn hàng của người dùng
    public function getOrdersByUser($user_id) {
        $productDb = Database::getProductInstance();
        $conn = $productDb->getConnection();
        
        if (!$conn) {
            error_log("Database connection error in getOrdersByUser");
            return [];
        }
        
        // Escape dữ liệu
        $user_id = $conn->real_escape_string($user_id);
        
        // Kiểm tra xem cột order_date có tồn tại không
        $checkOrderDateColumn = "SHOW COLUMNS FROM orders LIKE 'order_date'";
        $orderDateExists = $conn->query($checkOrderDateColumn);
        
        // Kiểm tra xem cột created_at có tồn tại không
        $checkCreatedAtColumn = "SHOW COLUMNS FROM orders LIKE 'created_at'";
        $createdAtExists = $conn->query($checkCreatedAtColumn);
        
        // Quyết định cột nào dùng để sắp xếp
        if ($orderDateExists && $orderDateExists->num_rows > 0) {
            $sql = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY order_date DESC";
        } elseif ($createdAtExists && $createdAtExists->num_rows > 0) {
            $sql = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY created_at DESC";
        } else {
            // Nếu không có cột thời gian nào, không sắp xếp
            $sql = "SELECT * FROM orders WHERE user_id = '$user_id'";
        }
        
        $result = $conn->query($sql);
        
        if (!$result) {
            error_log("Error retrieving orders: " . $conn->error);
            error_log("SQL Query: " . $sql);
            return [];
        }
        
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            // Đảm bảo các trường thông tin khách hàng luôn có trong kết quả
            if (!isset($row['customer_name'])) {
                $row['customer_name'] = 'Khách hàng';
            }
            if (!isset($row['customer_phone'])) {
                $row['customer_phone'] = '';
            }
            if (!isset($row['customer_address'])) {
                $row['customer_address'] = '';
            }
            if (!isset($row['payment_method'])) {
                $row['payment_method'] = 'Thanh toán khi nhận hàng';
            }
            if (!isset($row['notes'])) {
                $row['notes'] = '';
            }
            
            $orders[] = $row;
        }
        
        return $orders;
    }
    
    // Lấy chi tiết đơn hàng
    public function getOrderDetails($order_id) {
        $productDb = Database::getProductInstance();
        $conn = $productDb->getConnection();
        
        if (!$conn) {
            error_log("Database connection error in getOrderDetails");
            return [];
        }
        
        $order_id = $conn->real_escape_string($order_id);
        $sql = "SELECT * FROM order_details WHERE order_id = '$order_id'";
        $result = $conn->query($sql);
        
        if ($result) {
            $items = [];
            while ($row = $result->fetch_assoc()) {
                // Kiểm tra nếu giá = 0 và có product_id, lấy giá từ bảng products
                if (($row['price'] == 0 || empty($row['price'])) && isset($row['product_id']) && !empty($row['product_id'])) {
                    $productId = $conn->real_escape_string($row['product_id']);
                    $productSql = "SELECT price FROM products WHERE id = '$productId'";
                    $productResult = $conn->query($productSql);
                    
                    if ($productResult && $productResult->num_rows > 0) {
                        $productInfo = $productResult->fetch_assoc();
                        $row['price'] = $productInfo['price'];
                        
                        // Cập nhật giá trong bảng order_details để lần sau không cần truy vấn lại
                        $updateSql = "UPDATE order_details SET price = '{$productInfo['price']}' WHERE id = '{$row['id']}'";
                        $conn->query($updateSql);
                        
                        error_log("Updated price for order detail ID {$row['id']} to {$productInfo['price']}");
                    }
                }
                
                // Nếu vẫn không có giá, đặt giá mặc định
                if ($row['price'] == 0 || empty($row['price'])) {
                    error_log("Warning: Order detail ID {$row['id']} has zero price for product: {$row['product_name']}");
                    $row['price'] = 10000; // Đặt giá mặc định 10,000 VND
                }
                
                $items[] = $row;
            }
            return $items;
        } else {
            error_log("Error querying order details: " . $conn->error);
            return [];
        }
    }
}
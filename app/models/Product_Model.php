<?php
require_once 'app/models/BaseModel.php';

class Product_Model extends BaseModel {
    
    public function __construct() {
        parent::__construct("products", true);
        // Đã có sẵn các bảng, không cần tự động tạo bảng nữa
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
        
        // Truy vấn lấy danh sách sản phẩm trong giỏ hàng
        $stmt = $conn->prepare("SELECT * FROM carts WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result) {
            error_log("Error retrieving cart items: " . $conn->error);
            return [];
        }
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }
    // Thêm sản phẩm vào giỏ hàng
    public function insert_cart($user_id, $name_product, $quantity, $price = 0, $product_id) {
        $productDb = Database::getProductInstance();
        $conn = $productDb->getConnection();
        
        if (!$conn) {
            error_log("Database connection error in insert_cart");
            return [
                'status' => false,
                'message' => 'Lỗi kết nối cơ sở dữ liệu.'
            ];
        }
        
        // Escape dữ liệu
        $user_id = $conn->real_escape_string($user_id);
        $name_product = $conn->real_escape_string($name_product);
        $quantity = (int)$quantity; // Đảm bảo số lượng là số nguyên
        $price = (float)$price; // Đảm bảo giá là số thực
        $product_id = $conn->real_escape_string($product_id);
        
        // Kiểm tra sản phẩm đã tồn tại trong giỏ hàng chưa (prepared statement)
        $checkExistStmt = $conn->prepare("SELECT id, quantity FROM carts WHERE user_id = ? AND product_id = ?");
        $checkExistStmt->bind_param("ii", $user_id, $product_id);
        $checkExistStmt->execute();
        $existResult = $checkExistStmt->get_result();
        
        // Lấy số lượng tồn kho hiện tại của sản phẩm (prepared statement)
        $stockStmt = $conn->prepare("SELECT stock FROM products WHERE id = ? LIMIT 1");
        $stockStmt->bind_param("i", $product_id);
        $stockStmt->execute();
        $stockResult = $stockStmt->get_result();
        
        if ($stockResult && $stockResult->num_rows > 0) {
            $stockRow = $stockResult->fetch_assoc();
            $stock = (int)$stockRow['stock'];
        } else {
            error_log("Không tìm thấy sản phẩm với ID: $product_id");
            return [
                'status' => false,
                'message' => 'Không tìm thấy sản phẩm.'
            ];
        }
         
        //đã tồn tại trong giỏ hàng, cập nhật số lượng
        if ($existResult && $existResult->num_rows > 0) {
            $existItem = $existResult->fetch_assoc();
            $checkQuantity = $existItem['quantity'] + $quantity;
            if($checkQuantity > $stock){
                error_log("Số lượng sản phẩm vượt quá số lượng trong kho");
                return [
                    'status' => false,
                    'message' => 'Số lượng sản phẩm vượt quá số lượng trong kho. Vui lòng chọn số lượng nhỏ hơn!'
                ];
            }else{
                $newQuantity = $checkQuantity;
            }
            $cartId = $existItem['id'];
            
            $updateStmt = $conn->prepare("UPDATE carts SET quantity = ? WHERE id = ?");
            $updateStmt->bind_param("ii", $newQuantity, $cartId);
            $result = $updateStmt->execute();
            
            if (!$result) {
                error_log("SQL Error in updating cart: " . $conn->error);
                return [
                    'status' => false,
                    'message' => 'Lỗi khi cập nhật giỏ hàng.'
                ];
            }
            
            return [
                'status' => true,
                'message' => 'Thêm sản phẩm vào giỏ hàng thành công!'
            ];
        }
        // Nếu sản phẩm chưa có trong giỏ, kiểm tra số lượng yêu cầu
        if ($quantity > $stock) {
            error_log("Số lượng sản phẩm vượt quá số lượng trong kho");
            return [
                'status' => false,
                'message' => 'Số lượng sản phẩm vượt quá số lượng trong kho. Vui lòng chọn số lượng nhỏ hơn!'
            ];
        }
        // Thêm mới sản phẩm vào giỏ hàng (prepared statement)
        $insertStmt = $conn->prepare("INSERT INTO carts (user_id, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)");
        $insertStmt->bind_param("iisid", $user_id, $product_id, $name_product, $quantity, $price);
        $result = $insertStmt->execute();
        
        if (!$result) {
            error_log("SQL Error in insert_cart: " . $conn->error);
            return [
                'status' => false,
                'message' => 'Lỗi khi thêm sản phẩm vào giỏ hàng.'
            ];
        }
        
        error_log("Added product to cart: ID=$product_id, Name=$name_product, Price=$price");
        return [
            'status' => true,
            'message' => 'Thêm sản phẩm vào giỏ hàng thành công!'
        ];
    }
    
    // Tìm kiếm sản phẩm theo từ khóa (prepared statement)
    public function searchProducts($keyword) {
        $productDb = Database::getProductInstance();
        $conn = $productDb->getConnection();
        if (!$conn) {
            error_log("Database connection error in searchProducts");
            return [];
        }
        $like = "%$keyword%";
        $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ?");
        $stmt->bind_param("ss", $like, $like);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result) {
            error_log("SQL Error in searchProducts: " . $conn->error);
            return [];
        }
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }
    
    // Lỗ hổng: File upload không kiểm tra loại tệp
    public function uploadProductImage($file) {
        $target_dir = "uploads/products/";
        $extension = pathinfo($file["name"], PATHINFO_EXTENSION);
        $new_filename = 'product_' . uniqid() . '.' . $extension;
        $target_file = $target_dir . $new_filename;
        // Lỗ hổng: Không kiểm tra loại tệp - cho phép tải lên PHP shell
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return $target_file;
        }
        return false;
    }
    
    
    public function getProductsWithSorting($sort_column, $sort_direction) {
        // Whitelist các cột được phép sắp xếp
        $allowed_columns = ['id', 'name', 'price', 'quantity', 'created_at'];
        $allowed_directions = ['ASC', 'DESC'];
        
        // Kiểm tra sort_column hợp lệ
        if (!in_array($sort_column, $allowed_columns)) {
            $sort_column = 'id'; // mặc định
        }
        // Kiểm tra sort_direction hợp lệ
        $sort_direction = strtoupper($sort_direction);
        if (!in_array($sort_direction, $allowed_directions)) {
            $sort_direction = 'ASC'; // mặc định
        }
        
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
            $stmt = $conn->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                $products[] = $result->fetch_assoc();
            }
        }
        
        return $products;
    }

    // Xóa sản phẩm khỏi giỏ hàng (prepared statement)
    public function remove_from_cart($cart_id, $user_id) {
        $productDb = Database::getProductInstance();
        $conn = $productDb->getConnection();
        $stmt = $conn->prepare("DELETE FROM carts WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cart_id, $user_id);
        return $stmt->execute();
    }

    // Tạo đơn hàng mới
    public function createOrder($data) {
        $productDb = Database::getProductInstance();
        $conn = $productDb->getConnection();
        
        if (!$conn) {
            error_log("Database connection error in createOrder");
            return false;
        }
        
        // Escape dữ liệu
        $user_id = $conn->real_escape_string($data['user_id']);
        $total_amount = isset($data['total_amount']) ? $conn->real_escape_string($data['total_amount']) : '0';
        $customer_name = $conn->real_escape_string($data['customer_name']);
        $customer_phone = $conn->real_escape_string($data['customer_phone']);
        $customer_address = $conn->real_escape_string($data['customer_address']);
        $payment_method = isset($data['payment_method']) ? $conn->real_escape_string($data['payment_method']) : 'COD';
        $notes = isset($data['notes']) ? $conn->real_escape_string($data['notes']) : '';
        
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, customer_name, customer_phone, customer_address, payment_method, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("idsssss", $user_id, $total_amount, $customer_name, $customer_phone, $customer_address, $payment_method, $notes);

        // Thực thi câu lệnh
        if ($stmt->execute()) {  
            $order_id = $conn->insert_id; // Lấy ID của đơn hàng vừa tạo để sử dụng cho chi tiết đơn hàng
            return $order_id;
        } else {
            error_log("Error creating order: " . $conn->error);
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
    
        // Escape dữ liệu
        $order_id = $conn->real_escape_string($data['order_id']);
        $product_name = $conn->real_escape_string($data['product_name']);
        $quantity = $conn->real_escape_string($data['quantity']);
        $price = isset($data['price']) ? $conn->real_escape_string($data['price']) : '0';
        $product_id = isset($data['product_id']) ? $conn->real_escape_string($data['product_id']) : 'NULL';
        
        // LUÔN lấy giá từ bảng products nếu có product_id, bất kể giá truyền vào là bao nhiêu
        if ($product_id != 'NULL') {
            $productIdInt = (int)$data['product_id'];
            $productStmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
            $productStmt->bind_param("i", $productIdInt);
            $productStmt->execute();
            $productResult = $productStmt->get_result();
            if ($productResult && $productResult->num_rows > 0) {
                $productInfo = $productResult->fetch_assoc();
                $price = $productInfo['price'];
                error_log("Using price $price from products table for order detail of product ID $product_id");
            } else {
                error_log("Warning: Product with ID $product_id not found in products table");
            }
        }
        // Nếu vẫn không có giá, ghi log cảnh báo
        if ($price == 0 || empty($price)) {
            error_log("Warning: Creating order detail with zero price for product: $product_name");
            // Thử tìm giá dựa trên tên sản phẩm
            $productNameStmt = $conn->prepare("SELECT price FROM products WHERE name = ? LIMIT 1");
            $productNameStmt->bind_param("s", $product_name);
            $productNameStmt->execute();
            $productNameResult = $productNameStmt->get_result();
            if ($productNameResult && $productNameResult->num_rows > 0) {
                $productInfo = $productNameResult->fetch_assoc();
                $price = $productInfo['price'];
                error_log("Found price $price by product name for order detail");
            } else {
                // Đặt giá mặc định
                $price = 10000;
                error_log("Setting default price 10000 for product: $product_name");
            }
        }
        // Thực hiện insert chi tiết đơn hàng bằng prepared statement
        $insertStmt = $conn->prepare("INSERT INTO order_details (order_id, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)");
        $order_id_int = (int)$order_id;
        $product_id_int = $product_id != 'NULL' ? (int)$product_id : null;
        $quantity_int = (int)$quantity;
        $price_float = (float)$price;
        $insertStmt->bind_param("iisis", $order_id_int, $product_id_int, $product_name, $quantity_int, $price_float);
        if ($insertStmt->execute()) {
            $insert_id = $conn->insert_id;
            error_log("Created order detail ID $insert_id with price $price");
            return $insert_id;
        } else {
            error_log("Error creating order detail: " . $conn->error);
            return false;
        }
    }
    
    // Xóa giỏ hàng sau khi đặt hàng (prepared statement)
    public function clearCart($user_id) {
        $productDb = Database::getProductInstance();
        $conn = $productDb->getConnection();
        if (!$conn) {
            error_log("Database connection error in clearCart");
            return false;
        }
        $stmt = $conn->prepare("DELETE FROM carts WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $result = $stmt->execute();
        if (!$result) {
            error_log("SQL Error in clearCart: " . $conn->error);
        }
        return $result;
    }

    // Lấy danh sách đơn hàng của người dùng (prepared statement)
    public function getOrdersByUser($user_id) {
        $productDb = Database::getProductInstance();
        $conn = $productDb->getConnection();
        if (!$conn) {
            error_log("Database connection error in getOrdersByUser");
            return [];
        }
        $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result) {
            error_log("Error retrieving orders: " . $conn->error);
            return [];
        }
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        return $orders;
    }
    
    // Lấy chi tiết đơn hàng (prepared statement)
    public function getOrderDetails($order_id) {
        $productDb = Database::getProductInstance();
        $conn = $productDb->getConnection();
        if (!$conn) {
            error_log("Database connection error in getOrderDetails");
            return [];
        }
        $stmt = $conn->prepare("SELECT * FROM order_details WHERE order_id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $items = [];
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
            return $items;
        } else {
            error_log("Error querying order details: " . $conn->error);
            return [];
        }
    }
    // Cập nhật số lượng sản phẩm (prepared statement)
    public function updateProductQuantity($product_id, $quantity) {
        $productDb = Database::getProductInstance();
        $conn = $productDb->getConnection();
        if (!$conn) {
            error_log("Database connection error in updateProductQuantity");
            return false;
        }
        $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $product = $result->fetch_assoc();
            $new_quantity = max(0, $product['stock'] - $quantity);
            $updateStmt = $conn->prepare("UPDATE products SET stock = ? WHERE id = ?");
            $updateStmt->bind_param("ii", $new_quantity, $product_id);
            $updateResult = $updateStmt->execute();
            return $updateResult;
        }
        return false;
    }

    // Cập nhật đơn hàng (ví dụ: trạng thái)
    public function updateOrder($order_id, $data) {
        $productDb = Database::getProductInstance();
        $conn = $productDb->getConnection();
        if (!$conn) {
            error_log("Database connection error in updateOrder");
            return false;
        }
        $order_id = $conn->real_escape_string($order_id);
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "$key='" . $conn->real_escape_string($value) . "'";
        }
        if (empty($set)) return false;
        $setStr = implode(", ", $set);
        $sql = "UPDATE orders SET $setStr WHERE id='$order_id'";
        $result = $conn->query($sql);
        if (!$result) {
            error_log("Error updating order: " . $conn->error);
            error_log("SQL Query: " . $sql);
        }
        return $result;
    }
}
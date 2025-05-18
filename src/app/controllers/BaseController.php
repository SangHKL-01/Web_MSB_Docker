<?php
require_once __DIR__ . '/../libraries/db.php';

class BaseController {
    
    // Thêm hàm helper để xử lý đường dẫn assets
    protected function getAssetPath($path) {
        return '/public/assets/' . ltrim($path, '/');
    }

    protected function getUploadPath($path) {
        return '/uploads/' . ltrim($path, '/');
    }
    
    // Hiển thị view với dữ liệu
    protected function view($view, $data = []) {
        // Thêm các helper function vào data
        $data['getAssetPath'] = [$this, 'getAssetPath'];
        $data['getUploadPath'] = [$this, 'getUploadPath'];
        
        // Extract data để sử dụng trong view
        extract($data);
        
        // Kiểm tra xem tệp view có tồn tại không
        $view_file = "app/views/$view.php";
        if (file_exists($view_file)) {
            require_once $view_file;
        } else {
            die("View không tồn tại: $view");
        }
    }
    
    // Chuyển hướng
    protected function redirect($url) {
        // Kiểm tra nếu URL đã bắt đầu bằng http hoặc https, hoặc đã chứa index.php
        if (strpos($url, 'http') !== 0 && strpos($url, 'index.php') !== 0) {
            // Nếu là đường dẫn tương đối, thêm 'index.php?controller=' vào trước
            if (strpos($url, 'index.php?') !== 0) {
                // Kiểm tra nếu URL chứa controller/action
                if (strpos($url, '/') !== false) {
                    list($controller, $action) = explode('/', $url);
                    $url = "index.php?controller=$controller&action=$action";
                } else {
                    // Nếu chỉ có controller
                    $url = "index.php?controller=$url";
                }
            }
        }
        
        header("Location: $url");
        exit();
    }
    
    // Lấy dữ liệu POST
    protected function getPostData() {
        return $_POST;
    }
    
    // Lấy dữ liệu GET
    protected function getQueryData() {
        return $_GET;
    }
    
    // Kiểm tra xem người dùng đã đăng nhập chưa
    protected function isLoggedIn() {
        return isset($_SESSION['user']);
    }
    
    // Lấy thông tin người dùng đã đăng nhập
    protected function getLoggedInUser() {
        return isset($_SESSION['user']) ? $_SESSION['user'] : null;
    }
    
    // Yêu cầu đăng nhập
    protected function requireLogin() {
        if (!$this->isLoggedIn()) {
            $this->redirect('user/login');
        }
    }
   
    // Thêm dữ liệu vào session
    protected function setSession($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    // Lấy dữ liệu từ session
    protected function getSession($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }
    
    // Xóa dữ liệu khỏi session
    protected function unsetSession($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    protected function validateCSRF() {
        // Cố tình không thực hiện kiểm tra CSRF
        return true;
    }
    
    protected function getCartItemCount() {
        if (!$this->isLoggedIn()) {
            return 0;
        }
        
        $user = $this->getLoggedInUser();
        
        // Kết nối đến database
        $productDb = Database::getProductInstance();
        $conn = $productDb->getConnection();
        
        if (!$conn) {
            return 0;
        }
        
        $user_id = $user['id'];
        
        // Lấy tổng số sản phẩm trong giỏ hàng sử dụng prepared statement
        $sql = "SELECT SUM(quantity) as total FROM carts WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $row = $result->fetch_assoc()) {
                return $row['total'] ? $row['total'] : 0;
            }
            $stmt->close();
        }
        
        return 0;
    }
}
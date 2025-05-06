<?php
require_once 'app/controllers/BaseController.php';
require_once 'app/models/Product_Model.php';
require_once 'app/models/AdminModel.php';

class AdminController extends BaseController {
    private $productModel;
    private $userModel;
    
    public function __construct() {
        $this->productModel = new Product_Model();
        $this->userModel = new AdminModel();
    }
    
    // Phương thức mặc định - Dashboard
    public function index() {
        $this->requireAdmin();
        
        // Lấy tổng số đơn hàng
        $orders = $this->getAllOrders();
        $total_orders = count($orders);
        
        // Lấy tổng số người dùng
        $users = $this->userModel->getAll();
        $total_users = count($users);
        
        // Tính tổng doanh thu
        $total_revenue = 0;
        foreach ($orders as $order) {
            $total_revenue += $order['total_amount'];
        }
        
        $this->view('admin/dashboard', [
            'total_orders' => $total_orders,
            'total_users' => $total_users,
            'total_revenue' => $total_revenue,
            'recent_orders' => array_slice($orders, 0, 5) // 5 đơn hàng gần nhất
        ]);
    }
    
    // Xem tất cả đơn hàng
    public function orders() {
        $this->requireAdmin();
        
        $orders = $this->getAllOrders();
        
        $this->view('admin/orders', [
            'orders' => $orders
        ]);
    }
    
    // Xem chi tiết đơn hàng
    public function orderDetail() {
        $this->requireAdmin();
        
        $queryData = $this->getQueryData();
        
        if (!isset($queryData['id'])) {
            $this->redirect('admin/orders');
            return;
        }
        
        $order_id = $queryData['id'];
        
        // Tìm đơn hàng
        $orders = $this->getAllOrders();
        $order = null;
        foreach ($orders as $o) {
            if ($o['id'] == $order_id) {
                $order = $o;
                break;
            }
        }
        
        if (!$order) {
            $this->redirect('admin/orders');
            return;
        }
        
        // Lấy chi tiết đơn hàng
        $order_details = $this->productModel->getOrderDetails($order_id);
        
        $this->view('admin/order_detail', [
            'order' => $order,
            'order_details' => $order_details
        ]);
    }
    
    // Cập nhật trạng thái đơn hàng
    public function updateOrderStatus() {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/orders');
            return;
        }
        
        $postData = $this->getPostData();
        
        if (!isset($postData['order_id']) || !isset($postData['status'])) {
            $this->redirect('admin/orders');
            return;
        }
        
        $order_id = $postData['order_id'];
        $status = $postData['status'];
        
        // Cập nhật trạng thái đơn hàng trong database
        $result = $this->updateOrder($order_id, ['status' => $status]);
        
        if ($result) {
            $_SESSION['success'] = "Đã cập nhật trạng thái đơn hàng thành công.";
        } else {
            $_SESSION['error'] = "Không thể cập nhật trạng thái đơn hàng.";
        }
        
        $this->redirect('admin/orderDetail?id=' . $order_id);
    }
    
    // Quản lý người dùng - liệt kê tất cả
    public function users() {
        $this->requireAdmin();
        
        $users = $this->userModel->getAll();
        
        $this->view('admin/users', [
            'users' => $users
        ]);
    }
    
    // Thêm người dùng mới
    public function addUser() {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = $this->getPostData();
            
            // Kiểm tra thông tin cơ bản
            if (empty($postData['username']) || empty($postData['email']) || empty($postData['password'])) {
                $this->view('admin/add_user', [
                    'error' => 'Vui lòng điền đầy đủ thông tin bắt buộc.',
                    'data' => $postData
                ]);
                return;
            }
            
            // Kiểm tra username và email đã tồn tại chưa
            if ($this->userModel->isUsernameExists($postData['username'])) {
                $this->view('admin/add_user', [
                    'error' => 'Tên đăng nhập đã tồn tại.',
                    'data' => $postData
                ]);
                return;
            }
            
            if ($this->userModel->isEmailExists($postData['email'])) {
                $this->view('admin/add_user', [
                    'error' => 'Email đã tồn tại.',
                    'data' => $postData
                ]);
                return;
            }
            
            // Mã hóa mật khẩu
            $hashed_password = password_hash($postData['password'], PASSWORD_DEFAULT);
            
            // Tạo dữ liệu người dùng mới
            $userData = [
                'username' => $postData['username'],
                'email' => $postData['email'],
                'password' => $hashed_password,
                'fullname' => $postData['fullname'] ?? '',
                'phone' => $postData['phone'] ?? '',
                'address' => $postData['address'] ?? '',
                'role' => $postData['role'] ?? 'user'
            ];
            
            // Thêm người dùng mới
            $result = $this->userModel->insert($userData);
            
            if ($result) {
                $_SESSION['success'] = "Thêm người dùng mới thành công.";
                $this->redirect('admin/users');
            } else {
                $this->view('admin/add_user', [
                    'error' => 'Không thể thêm người dùng mới.',
                    'data' => $postData
                ]);
            }
        } else {
            $this->view('admin/add_user');
        }
    }
    
    // Sửa thông tin người dùng
    public function editUser() {
        $this->requireAdmin();
        
        $queryData = $this->getQueryData();
        
        if (!isset($queryData['id'])) {
            $this->redirect('admin/users');
            return;
        }
        
        $user_id = $queryData['id'];
        $user = $this->userModel->getById($user_id);
        
        if (!$user) {
            $this->redirect('admin/users');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = $this->getPostData();
            
            // Kiểm tra thông tin cơ bản
            if (empty($postData['username']) || empty($postData['email'])) {
                $this->view('admin/edit_user', [
                    'error' => 'Vui lòng điền đầy đủ thông tin bắt buộc.',
                    'user' => $user
                ]);
                return;
            }
            
            // Kiểm tra username đã tồn tại chưa (nếu khác username hiện tại)
            if ($postData['username'] !== $user['username'] && $this->userModel->isUsernameExists($postData['username'])) {
                $this->view('admin/edit_user', [
                    'error' => 'Tên đăng nhập đã tồn tại.',
                    'user' => $user
                ]);
                return;
            }
            
            // Kiểm tra email đã tồn tại chưa (nếu khác email hiện tại)
            if ($postData['email'] !== $user['email'] && $this->userModel->isEmailExists($postData['email'])) {
                $this->view('admin/edit_user', [
                    'error' => 'Email đã tồn tại.',
                    'user' => $user
                ]);
                return;
            }
            
            // Cập nhật dữ liệu người dùng
            $userData = [
                'username' => $postData['username'],
                'email' => $postData['email'],
                'fullname' => $postData['fullname'] ?? '',
                'phone' => $postData['phone'] ?? '',
                'address' => $postData['address'] ?? '',
                'role' => $postData['role'] ?? 'user',
            ];
            
            // Nếu có cập nhật mật khẩu
            if (!empty($postData['password'])) {
                $userData['password'] = $postData['password'];
            }
            
            // Cập nhật thông tin người dùng
            $result = $this->userModel->update($user_id, $userData);
            
            if ($result) {
                $_SESSION['success'] = "Cập nhật thông tin người dùng thành công.";
                $this->redirect('admin/users');
            } else {
                $this->view('admin/edit_user', [
                    'error' => 'Không thể cập nhật thông tin người dùng.',
                    'user' => $user
                ]);
            }
        } else {
            $this->view('admin/edit_user', [
                'user' => $user
            ]);
        }
    }
    
    // Xóa người dùng
    public function deleteUser() {
        $this->requireAdmin();
        
        $queryData = $this->getQueryData();
        
        if (!isset($queryData['id'])) {
            $this->redirect('admin/users');
            return;
        }
        
        $user_id = $queryData['id'];
        
        // Không cho phép xóa tài khoản admin đang đăng nhập
        if ($user_id == $this->getLoggedInUser()['id']) {
            $_SESSION['error'] = "Không thể xóa tài khoản của chính bạn.";
            $this->redirect('admin/users');
            return;
        }
        
        // Xóa người dùng
        $result = $this->userModel->delete($user_id);
        
        if ($result) {
            $_SESSION['success'] = "Xóa người dùng thành công.";
        } else {
            $_SESSION['error'] = "Không thể xóa người dùng.";
        }
        
        $this->redirect('admin/users');
    }
    
    // Kiểm tra và yêu cầu quyền admin
    private function requireAdmin() {
        if (!$this->isLoggedIn()) {
            $this->redirect('user/login');
            exit;
        }
        
        $user = $this->getLoggedInUser();
        
        if (!isset($user['role']) || $user['role'] !== 'admin') {
            $_SESSION['error'] = "Bạn không có quyền truy cập trang quản trị.";
            $this->redirect('home/index');
            exit;
        }
    }
    
    // Lấy tất cả đơn hàng từ cơ sở dữ liệu
    private function getAllOrders() {
        // Sử dụng kết nối từ Database class
        $productDb = Database::getProductInstance();
        $conn = $productDb->getConnection();
        
        if (!$conn) {
            error_log("Database connection error in getAllOrders");
            return [];
        }
        
        $sql = "SELECT * FROM orders ORDER BY created_at DESC";
        $result = $conn->query($sql);
        
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
    
    // Cập nhật thông tin đơn hàng
    private function updateOrder($order_id, $data) {
        // Sử dụng kết nối từ Database class
        $productDb = Database::getProductInstance();
        $conn = $productDb->getConnection();
        
        if (!$conn) {
            error_log("Database connection error in updateOrder");
            return false;
        }
        
        // Tạo các cặp column=value cho câu lệnh SET
        $setClause = [];
        foreach ($data as $column => $value) {
            $value = $conn->real_escape_string($value);
            $setClause[] = "$column = '$value'";
        }
        $setClause = implode(', ', $setClause);
        
        $order_id = $conn->real_escape_string($order_id);
        $sql = "UPDATE orders SET $setClause WHERE id = '$order_id'";
        
        $result = $conn->query($sql);
        
        if (!$result) {
            error_log("Error updating order: " . $conn->error);
            return false;
        }
        
        return true;
    }
} 
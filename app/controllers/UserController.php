<?php
require_once 'app/controllers/BaseController.php';
require_once 'app/models/UserModel.php';

class UserController extends BaseController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
    }
    
    // Hiển thị trang đăng nhập và xử lý đăng nhập
    public function login() {
        // Kiểm tra nếu form đã được gửi qua POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $postData = $this->getPostData();
            
            $username = $postData['username'];
            $password = $postData['password'];

            $user = $this->userModel->authenticate($username, $password);

            if ($user) {
                $this->setSession('user', $user);
                
                if (isset($postData['remember_me'])) {
                    setcookie('remembered_user', $username, time() + 30 * 24 * 60 * 60, '/');
                }
                
                // Kiểm tra quyền và chuyển hướng người dùng admin đến trang quản trị
                if (isset($user['role']) && $user['role'] === 'admin') {
                    $this->redirect('admin/index');
                } else {
                    $this->redirect('index.php');
                }
            } else {
                $error = "Tên đăng nhập hoặc mật khẩu không đúng!";
                $this->view('user/login', ['error' => $error]);
            }
        } else {
            $this->view('user/login');
        }
    }
    
    // Hiển thị trang đăng ký và xử lý đăng ký
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $postData = $this->getPostData();
            
            $username = $postData['username'];
            $password = $postData['password'];
            $email = $postData['email'];
            
            // kiểm tra xem user và email đã tồn tại chưa.
            $user = $this->userModel->Get_user($username);
            $existingEmail = $this->userModel->getByField('email', $email);

            if ($user) {
                $error = "Tài khoản đã tồn tại";
                $this->view('user/register', ['error' => $error]);
            }elseif ($existingEmail) {
                $error = "Email đã tồn tại";
                $this->view('user/register', ['error' => $error]);
            } elseif (strlen($password) < 6) {
                $error = "Mật khẩu phải có ít nhất 6 ký tự";
                $this->view('user/register', ['error' => $error]);
            }
            else {
                $result = $this->userModel->register($username, $password, $email);
                if ($result) {
                    $this->redirect('user/login');
                } else {
                    $error = "Đăng ký thất bại";
                    $this->view('user/register', ['error' => $error]);
                }
            }
        } else {
            $this->view('user/register');
        }
    }
    
    // Đăng xuất
    public function logout() {
        session_unset(); // Xóa tất cả các biến session
        session_destroy(); // Hủy session
        
        $this->redirect('index.php');
    }
    
    // Hiển thị trang hồ sơ người dùng
    public function profile() {
        $this->requireLogin();
        
        $user = $this->getLoggedInUser(); // Lấy thông tin người dùng đã đăng nhập
        $userData = $this->userModel->Get_user($user['username']);
        
        $this->view('user/profile', ['user' => $userData]);
    }
    
    // Đổi mật khẩu
    public function forgot_password() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $postData = $this->getPostData();
            
            $password = $postData['password'];
            $new_password = $postData['new_password'];
            $confirm_password = $postData['confirm_password'];
            
            $user = $this->getLoggedInUser();
            $userData = $this->userModel->Get_user($user['username']);
            
            if ($userData['password'] === $password) {
                if (strlen($new_password) < 6) {
                    $error = "Mật khẩu phải có ít nhất 6 ký tự";
                    $this->view('user/forget_password', ['error' => $error]);
                } elseif ($new_password !== $confirm_password) {
                    $error = "Xác nhận mật khẩu không khớp";
                    $this->view('user/forget_password', ['error' => $error]);
                } else {
                    $result = $this->userModel->forget_password($user['username'], $new_password);
                    $this->redirect('index.php?controller=user&action=login');
                } 
            } else {
                $error = "Mật khẩu hiện tại không đúng";
                $this->view('user/forget_password', ['error' => $error]);
            }
        } else {
            $this->view('user/forget_password');
        }
    }
    
    // Cập nhật thông tin hồ sơ
    public function change_profile() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = $this->getPostData();
            
            //  lọc đầu vào - để tránh XSS
            $fullname = htmlspecialchars($postData['fullname']);
            $phone = htmlspecialchars($postData['phone']);
            $gioi_tinh = htmlspecialchars($postData['gioi_tinh']);
            $ngay_sinh = htmlspecialchars($postData['ngay_sinh']);
            
            $user = $this->getLoggedInUser();
            
            $this->userModel->change_profile($fullname, $ngay_sinh, $gioi_tinh, $phone, $user['username']);
            
            $this->redirect('user/profile');
        }
    }
    
    // Lỗ hổng: Path Traversal trong xem avatar
    public function viewAvatar() {
        if (isset($_GET['file'])) {
            // Lỗ hổng: không kiểm tra/lọc tên tệp - Path Traversal
            $file = $_GET['file'];
            $avatar = $this->userModel->getUserAvatar($file);
            
            header('Content-Type: image/jpeg');
            echo $avatar;
            exit;
        }
    }
    
    public function upload_avatar() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
            $user = $this->getLoggedInUser();
            $result = $this->userModel->uploadAvatar($_FILES['avatar'], $user['id']);
            
            if ($result['status']) {
                $_SESSION['success'] = $result['message'];
                // Cập nhật thông tin avatar trong session nếu cần
                if (isset($_SESSION['user'])) {
                    $_SESSION['user']['avatar'] = $result['filename'];
                }
            } else {
                $_SESSION['error'] = $result['message'];
            }
        } else {
            $_SESSION['error'] = "Không có file được tải lên.";
        }
        
        $this->redirect('user/profile');
    }
}
?>
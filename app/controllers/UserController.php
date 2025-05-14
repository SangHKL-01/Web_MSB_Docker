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
    public function change_password() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $postData = $this->getPostData();
            
            $password = $postData['password'];
            $new_password = $postData['new_password'];
            $confirm_password = $postData['confirm_password'];
            
            $user = $this->getLoggedInUser();
            $userData = $this->userModel->Get_user($user['username']);
            
            if (password_verify($password, $userData['password'])) {
                if (strlen($new_password) < 6) {
                    $error = "Mật khẩu phải có ít nhất 6 ký tự";
                    $this->view('user/change_password', ['error' => $error]);
                } elseif ($new_password !== $confirm_password) {
                    $error = "Xác nhận mật khẩu không khớp";
                    $this->view('user/change_password', ['error' => $error]);
                } else {
                    $result = $this->userModel->change_password($user['username'], $new_password);
                    $this->redirect('index.php?controller=user&action=login');
                } 
            } else {
                $error = "Mật khẩu hiện tại không đúng";
                $this->view('user/change_password', ['error' => $error]);
            }
        } else {
            $this->view('user/change_password');
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
            
            // Validate số điện thoại
            if (!preg_match('/^[0-9]{10,11}$/', $phone)) {
                $_SESSION['error'] = "Số điện thoại phải chỉ chứa các ký tự số và có 10-11 số.";
                $user = $this->getLoggedInUser();
                $userData = $this->userModel->Get_user($user['username']);
                $this->view('user/profile', ['user' => $userData]);
                return;
            }
            $user = $this->getLoggedInUser();
            $this->userModel->change_profile($fullname, $ngay_sinh, $gioi_tinh, $phone, $user['username']);
            $this->redirect('user/profile');
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

    // Chức năng quên mật khẩu
    public function forgotPassword() {
        require_once __DIR__ . '/../models/AdminModel.php';
        require_once __DIR__ . '/../libraries/Mailer.php';
        $userModel = new AdminModel();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->view('user/forgot_password', ['error' => 'Email không hợp lệ!']);
                return;
            }
            $user = $userModel->getByEmail($email);
            if (!$user) {
                $this->view('user/forgot_password', ['error' => 'Email không tồn tại trong hệ thống!']);
                return;
            }
            // Tạo token
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));
            // Lưu token vào DB
            $userModel->saveResetToken($user['id'], $token, $expiry);
            // Gửi email
            $scriptName = dirname($_SERVER['SCRIPT_NAME']);
            $resetLink = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $scriptName . "/index.php?controller=User&action=resetPassword&token=$token";
            $subject = "Đặt lại mật khẩu";
            $body = "Nhấn vào liên kết sau để đặt lại mật khẩu: <a href='$resetLink'>$resetLink</a>";
            $sendResult = Mailer::send($email, $subject, $body);
            if ($sendResult) {
                $this->view('user/forgot_password', ['success' => 'Vui lòng kiểm tra email để đặt lại mật khẩu!']);
            } else {
                $this->view('user/forgot_password', ['error' => 'Không thể gửi email. Vui lòng thử lại sau!']);
            }
            return;
        }
        $this->view('user/forgot_password');
    }

    // Đặt lại mật khẩu qua token
    public function resetPassword() {
        $userModel = $this->userModel;
        $token = $_GET['token'] ?? '';
        if (!$token) {
            echo "Liên kết không hợp lệ!";
            return;
        }
        // Tìm user theo token
        $user = $userModel->findByField('reset_token', $token);
        if (!$user || strtotime($user['reset_token_expiry']) < time()) {
            echo "Liên kết đã hết hạn hoặc không hợp lệ!";
            return;
        }
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            if (strlen($password) < 6) {
                $error = "Mật khẩu phải có ít nhất 6 ký tự";
            } elseif ($password !== $confirm) {
                $error = "Xác nhận mật khẩu không khớp";
            } else {
                // Cập nhật mật khẩu mới (hash) và xóa token
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $userModel->update($user['id'], [
                    'password' => $hashedPassword,
                    'reset_token' => null,
                    'reset_token_expiry' => null
                ]);
                echo "Đặt lại mật khẩu thành công! Bạn có thể <a href='index.php?controller=user&action=login'>đăng nhập</a>.";
                return;
            }
        }
        // Hiển thị form đổi mật khẩu qua view
        $this->view('user/reset_password', ['error' => $error]);
    }
}
?>
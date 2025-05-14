<?php
require_once 'app/models/BaseModel.php';

class AdminModel extends BaseModel {
    
    public function __construct() {
        parent::__construct("users");
    }
    
    // Kiểm tra username đã tồn tại chưa (prepared statement)
    public function isUsernameExists($username) {
        $stmt = $this->db->getConnection()->prepare("SELECT id FROM {$this->table} WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result && $result->num_rows > 0;
    }
    
    // Kiểm tra email đã tồn tại chưa (prepared statement)
    public function isEmailExists($email) {
        $stmt = $this->db->getConnection()->prepare("SELECT id FROM {$this->table} WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result && $result->num_rows > 0;
    }
    
    // Tìm người dùng theo username (prepared statement)
    public function findByUsername($username) {
        $stmt = $this->db->getConnection()->prepare("SELECT * FROM {$this->table} WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }
    
    // Tìm người dùng theo email (prepared statement)
    public function findByEmail($email) {
        $stmt = $this->db->getConnection()->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }
    
    // Xác minh đăng nhập
    public function authenticate($username, $password) {
        $user = $this->findByUsername($username);
        
        if (!$user) {
            // Thử tìm theo email
            $user = $this->findByEmail($username);
        }
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return null;
    }
    
    // Đăng ký người dùng mới
    public function register($userData) {
        // Kiểm tra username và email
        if ($this->isUsernameExists($userData['username'])) {
            return ['status' => false, 'message' => 'Tên đăng nhập đã tồn tại'];
        }
        
        if ($this->isEmailExists($userData['email'])) {
            return ['status' => false, 'message' => 'Email đã tồn tại'];
        }
        
        // Mã hóa mật khẩu
        $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
        
        // Đặt quyền mặc định là user nếu không được chỉ định
        if (!isset($userData['role'])) {
            $userData['role'] = 'user';
        }
        
        // Thêm thời gian tạo tài khoản
        $userData['created_at'] = date('Y-m-d H:i:s');
        
        // Thêm người dùng vào database
        $result = $this->insert($userData);
        
        if ($result) {
            return ['status' => true, 'message' => 'Đăng ký thành công', 'user_id' => $result];
        } else {
            return ['status' => false, 'message' => 'Đăng ký thất bại'];
        }
    }
    
    // Cập nhật thông tin người dùng
    public function updateProfile($user_id, $data) {
        // Kiểm tra nếu thay đổi username
        if (isset($data['username']) && $this->isUsernameExists($data['username'])) {
            $current_user = $this->getById($user_id);
            if ($current_user['username'] !== $data['username']) {
                return ['status' => false, 'message' => 'Tên đăng nhập đã tồn tại'];
            }
        }
        
        // Kiểm tra nếu thay đổi email
        if (isset($data['email']) && $this->isEmailExists($data['email'])) {
            $current_user = $this->getById($user_id);
            if ($current_user['email'] !== $data['email']) {
                return ['status' => false, 'message' => 'Email đã tồn tại'];
            }
        }
        
        // Nếu có cập nhật mật khẩu
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            // Xóa trường password nếu nó rỗng
            unset($data['password']);
        }
        
        // Cập nhật thông tin người dùng
        $result = $this->update($user_id, $data);
        
        if ($result) {
            return ['status' => true, 'message' => 'Cập nhật thông tin thành công'];
        } else {
            return ['status' => false, 'message' => 'Cập nhật thông tin thất bại'];
        }
    }

    // Lấy người dùng theo email (alias cho findByEmail)
    public function getByEmail($email) {
        return $this->findByEmail($email);
    }

    // Lưu token đặt lại mật khẩu
    public function saveResetToken($user_id, $token, $expiry) {
        $stmt = $this->db->getConnection()->prepare("UPDATE {$this->table} SET reset_token = ?, reset_token_expiry = ? WHERE id = ?");
        $stmt->bind_param("ssi", $token, $expiry, $user_id);
        return $stmt->execute();
    }
} 
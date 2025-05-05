<?php
require_once 'app/models/BaseModel.php';

class UserModel extends BaseModel {

    public function __construct() {
        parent::__construct("users", false); // Sử dụng DB_WEB database
    }

    // Lỗ hổng: SQL Injection thông qua truy vấn trực tiếp
    public function authenticate($username, $password) {
        // Không escape các tham số đầu vào
        $sql = "SELECT * FROM $this->table WHERE username = '$username' AND password = '$password'";
        $result = $this->db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    // Lỗ hổng: Mật khẩu được lưu dưới dạng plain text
    public function register($username, $password, $email) {
        // Lỗ hổng XSS: không lọc đầu vào
        $data = [
            'username' => $username,
            'password' => $password, // Lưu mật khẩu dưới dạng plain text
            'email' => $email,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->insert($data);
    }
    
    // Lỗ hổng: SQL Injection
    public function Get_user($username) {
        return $this->getByField('username', $username);
    }
    
    // Lỗ hổng: Không xác thực đầu vào
    public function forget_password($username, $new_password) {
        // Lỗ hổng: không kiểm tra độ mạnh của mật khẩu
        $data = [
            'password' => $new_password
        ];
        
        $user = $this->Get_user($username);
        if ($user) {
            return $this->update($user['id'], $data);
        }
        
        return false;
    }
    
    // Lỗ hổng: SQL Injection và XSS thông qua đầu vào người dùng
    public function change_profile($fullname, $ngay_sinh, $gioi_tinh, $phone, $username) {
        // Không lọc đầu vào có thể dẫn đến XSS
        $data = [
            'fullname' => $fullname,
            'ngay_sinh' => $ngay_sinh,
            'gioi_tinh' => $gioi_tinh,
            'phone' => $phone
        ];
        
        $user = $this->Get_user($username);
        if ($user) {
            return $this->update($user['id'], $data);
        }
        
        return false;
    }
    
    // Lỗ hổng bảo mật: eval() với đầu vào người dùng (RCE)
    public function executeCustomQuery($query_string) {
        // Lỗ hổng Remote Code Execution cực kỳ nguy hiểm
        return eval($query_string);
    }
    
    // Lỗ hổng: Path Traversal
    public function getUserAvatar($filename) {
        // Cho phép đọc file tùy ý trên server
        $file_path = "uploads/avatars/" . $filename;
        return file_get_contents($file_path);
    }
    
    // Lấy thông tin sản phẩm - sử dụng product database
    public function Get_product($name) {
        // Dùng product database để lấy thông tin sản phẩm
        $productDb = Database::getProductInstance();
        $sql = "SELECT * FROM products WHERE name = '$name'";
        $result = $productDb->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
}
?>
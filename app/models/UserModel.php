<?php
require_once 'app/models/BaseModel.php';

class UserModel extends BaseModel {

    public function __construct() {
        parent::__construct("users", false); // Sử dụng db_user database
    }

    // Sử dụng prepared statement để chống SQL Injection
    public function authenticate($username, $password) {
        $stmt = $this->db->getConnection()->prepare("SELECT * FROM $this->table WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
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
    
    // Sử dụng prepared statement để chống SQL Injection
    public function Get_user($username) {
        $stmt = $this->db->getConnection()->prepare("SELECT * FROM $this->table WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }
    
    public function forget_password($username, $new_password) {
        $data = [
            'password' => $new_password
        ];
        
        $user = $this->Get_user($username);
        if ($user) {
            return $this->update($user['id'], $data);
        }
        
        return false;
    }
    

    public function change_profile($fullname, $ngay_sinh, $gioi_tinh, $phone, $username) {
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
    
    
    public function uploadAvatar($file, $user_id) {
        // Kiểm tra lỗi upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['status' => false, 'message' => 'Lỗi khi tải lên file.'];
        }
        
        // Kiểm tra loại file
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($file_info, $file['tmp_name']);
        finfo_close($file_info);
        
        if (!in_array($file_type, $allowed_types)) {
            return ['status' => false, 'message' => 'Chỉ chấp nhận file ảnh (JPEG, PNG, GIF).'];
        }
        
        // Tạo tên file mới để tránh trùng lặp
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = 'avatar_' . $user_id .'.' . $extension;
        

        
        $target_file = 'uploads/avatars/' . $new_filename;
        
        // Di chuyển file tải lên vào thư mục đích
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            // Cập nhật đường dẫn avatar trong database
            $data = ['avatar' => $new_filename];
            $result = $this->update($user_id, $data);
            
            if ($result) {
                return ['status' => true, 'message' => 'Cập nhật avatar thành công.', 'filename' => $new_filename];
            } else {
                return ['status' => false, 'message' => 'Không thể cập nhật thông tin avatar trong database.'];
            }
        } else {
            return ['status' => false, 'message' => 'Không thể lưu file avatar.'];
        }
    }

    // Tìm user theo trường bất kỳ
    public function findByField($field, $value) {
        $stmt = $this->db->getConnection()->prepare("SELECT * FROM $this->table WHERE $field = ?");
        $stmt->bind_param("s", $value);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }
}
?>
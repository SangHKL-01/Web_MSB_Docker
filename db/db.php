<?php
class Database {
    private static $userInstance = null;
    private static $productInstance = null;
    private $conn;
    private $dbName;
    

    private function __construct($dbname) {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $this->dbName = $dbname;
        
        // Tạo kết nối
        $this->conn = new mysqli($servername, $username, $password, $dbname);
        
        // Kiểm tra kết nối
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }
    
    // Lấy instance cho users database (db_user)
    public static function getUserInstance() {
        if (self::$userInstance == null) {
            self::$userInstance = new Database("db_user");
        }
        return self::$userInstance;
    }
    
    // Lấy instance cho products database (db_product)
    public static function getProductInstance() {
        if (self::$productInstance == null) {
            self::$productInstance = new Database("db_product");
        }
        return self::$productInstance;
    }
    
    // Lấy kết nối
    public function getConnection() {
        return $this->conn;
    }
    
    // Thực thi truy vấn - Lỗ hổng SQL Injection cố ý không dùng prepared statement
    public function query($sql) {
        return $this->conn->query($sql);
    }
    
    // Escape string - không được sử dụng triệt để trong ứng dụng
    public function escape($string) {
        return $this->conn->real_escape_string($string);
    }
    
    // Lấy tên database hiện tại
    public function getDatabaseName() {
        return $this->dbName;
    }
}
?>
<?php
require_once 'db/db.php';

class BaseModel {
    protected $db;
    protected $table;
    protected $isProductDb;
    
    public function __construct($table, $isProductDb = false) {
        $this->isProductDb = $isProductDb;
        if ($isProductDb) {
            $this->db = Database::getProductInstance();
        } else {
            $this->db = Database::getUserInstance();
        }
        $this->table = $table;
    }
    
    // Lấy tất cả bản ghi
    public function getAll() {
        $sql = "SELECT * FROM $this->table";
        $result = $this->db->query($sql);
        $data = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }
    
    // Lấy một bản ghi theo id
    public function getById($id) {
        // Lỗ hổng SQL Injection: truyền trực tiếp biến vào câu truy vấn
        $sql = "SELECT * FROM $this->table WHERE id = $id";
        $result = $this->db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    // Lấy bản ghi theo điều kiện
    public function getByField($field, $value) {
        // Lỗ hổng SQL Injection: truyền trực tiếp biến vào câu truy vấn
        $sql = "SELECT * FROM $this->table WHERE $field = '$value'";
        $result = $this->db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    // Thêm bản ghi mới
    public function insert($data) {
        $columns = implode(", ", array_keys($data));
        $values = implode("', '", array_values($data));
        
        // Lỗ hổng SQL Injection: không escape dữ liệu đầu vào
        $sql = "INSERT INTO $this->table ($columns) VALUES ('$values')";
        
        return $this->db->query($sql);
    }
    
    // Cập nhật bản ghi
    public function update($id, $data) {
        $setClause = [];
        foreach ($data as $key => $value) {
            $setClause[] = "$key = '$value'";
        }
        $setString = implode(", ", $setClause);
        
        // Lỗ hổng SQL Injection: không escape dữ liệu đầu vào
        $sql = "UPDATE $this->table SET $setString WHERE id = $id";
        
        return $this->db->query($sql);
    }
    
    // Xóa bản ghi
    public function delete($id) {
        // Lỗ hổng SQL Injection: truyền trực tiếp biến vào câu truy vấn
        $sql = "DELETE FROM $this->table WHERE id = $id";
        
        return $this->db->query($sql);
    }
}
?> 
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
    
    // Lấy một bản ghi theo id (prepared statement)
    public function getById($id) {
        $stmt = $this->db->getConnection()->prepare("SELECT * FROM $this->table WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }
    
    // Lấy bản ghi theo điều kiện (prepared statement)
    public function getByField($field, $value) {
        $stmt = $this->db->getConnection()->prepare("SELECT * FROM $this->table WHERE $field = ?");
        $stmt->bind_param("s", $value);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }
    
    // Thêm bản ghi mới (prepared statement)
    public function insert($data) {
        $columns = implode(", ", array_keys($data));
        $placeholders = rtrim(str_repeat('?, ', count($data)), ', ');
        $types = str_repeat('s', count($data));
        $stmt = $this->db->getConnection()->prepare("INSERT INTO $this->table ($columns) VALUES ($placeholders)");
        $stmt->bind_param($types, ...array_values($data));
        return $stmt->execute();
    }
    
    // Cập nhật bản ghi (prepared statement)
    public function update($id, $data) {
        $setClause = [];
        foreach ($data as $key => $value) {
            $setClause[] = "$key = ?";
        }
        $setString = implode(", ", $setClause);
        $types = str_repeat('s', count($data)) . 'i';
        $values = array_values($data);
        $values[] = $id;
        $stmt = $this->db->getConnection()->prepare("UPDATE $this->table SET $setString WHERE id = ?");
        $stmt->bind_param($types, ...$values);
        return $stmt->execute();
    }
    
    // Xóa bản ghi (prepared statement)
    public function delete($id) {
        $stmt = $this->db->getConnection()->prepare("DELETE FROM $this->table WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?> 
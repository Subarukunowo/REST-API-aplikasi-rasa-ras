<?php
class Kategori {
    public $conn;
    public $table = "kategori";
    
    public $id;
    public $nama;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM {$this->table} ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO {$this->table} (nama) VALUES (:nama)";
        $stmt = $this->conn->prepare($query);
        
        // Sanitize input
        $this->nama = htmlspecialchars(strip_tags($this->nama));
        
        $stmt->bindParam(':nama', $this->nama);
        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE {$this->table} SET nama = :nama WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        // Sanitize input
        $this->nama = htmlspecialchars(strip_tags($this->nama));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        $stmt->bindParam(':nama', $this->nama);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }
    
    public function nameExists() {
        $query = "SELECT id FROM {$this->table} WHERE nama = ? AND id != ?";
        $stmt = $this->conn->prepare($query);
        
        $this->nama = htmlspecialchars(strip_tags($this->nama));
        $this->id = isset($this->id) ? $this->id : 0;
        
        $stmt->bindParam(1, $this->nama);
        $stmt->bindParam(2, $this->id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    public function getTotalCount() {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?>
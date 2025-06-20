<?php
class JenisHidangan {
    public $conn;
    public $table = 'jenis_hidangan';
    
    public $id;
    public $nama;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Method untuk mendapatkan semua data
    public function getAll() {
        $query = "SELECT * FROM {$this->table} ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    // Method untuk mendapatkan data berdasarkan ID
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->id = $row['id'];
            $this->nama = $row['nama'];
            return true;
        }
        return false;
    }
    
    // Method untuk membuat data baru
    public function create() {
        $query = "INSERT INTO {$this->table} (nama) VALUES (:nama)";
        $stmt = $this->conn->prepare($query);
        
        // Sanitize input
        $this->nama = htmlspecialchars(strip_tags($this->nama));
        
        // Bind data
        $stmt->bindParam(':nama', $this->nama);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Method untuk update data
    public function update() {
        $query = "UPDATE {$this->table} SET nama = :nama WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        // Sanitize input
        $this->nama = htmlspecialchars(strip_tags($this->nama));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Bind data
        $stmt->bindParam(':nama', $this->nama);
        $stmt->bindParam(':id', $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Method untuk delete data
    public function delete() {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        // Sanitize input
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Bind data
        $stmt->bindParam(1, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Method untuk check apakah nama sudah ada
    public function nameExists() {
        $query = "SELECT id FROM {$this->table} WHERE nama = ? AND id != ?";
        $stmt = $this->conn->prepare($query);
        
        $this->nama = htmlspecialchars(strip_tags($this->nama));
        $this->id = isset($this->id) ? $this->id : 0;
        
        $stmt->bindParam(1, $this->nama);
        $stmt->bindParam(2, $this->id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }
}
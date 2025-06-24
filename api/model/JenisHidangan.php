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
    
    // Method untuk mendapatkan data berdasarkan ID (return row data)
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Method untuk load data ke properties object
    public function loadById($id) {
        $row = $this->getById($id);
        
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
        
        return $stmt->execute();
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
        
        return $stmt->execute();
    }
    
    // Method untuk delete data
    public function delete() {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        // Sanitize input
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Bind data
        $stmt->bindParam(1, $this->id);
        
        return $stmt->execute();
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
        
        return $stmt->rowCount() > 0;
    }
    
    // Method untuk count total records
    public function getTotalCount() {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?>
<?php
// Fixed Notifikasi.php model
class Notifikasi {
    public $conn;
    public $table = "notifikasi";
    
    public $id;
    public $user_id;
    public $pesan;
    public $is_read;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT n.*, u.username 
                  FROM {$this->table} n
                  LEFT JOIN users u ON n.user_id = u.id
                  ORDER BY n.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT n.*, u.username 
                  FROM {$this->table} n
                  LEFT JOIN users u ON n.user_id = u.id
                  WHERE n.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUserId($user_id, $limit = 0) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE user_id = ? 
                  ORDER BY created_at DESC";
        
        if ($limit > 0) {
            $query .= " LIMIT " . $limit;
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt;
    }

    public function getUnreadByUser($user_id) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE user_id = ? AND is_read = 0 
                  ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt;
    }

    // Fixed create method - now accepts data parameter and validates foreign keys
    public function create($data = null) {
        // If data is provided, populate object properties
        if ($data) {
            $this->user_id = isset($data->user_id) ? $data->user_id : null;
            $this->pesan = isset($data->pesan) ? $data->pesan : null;
            $this->is_read = isset($data->is_read) ? $data->is_read : 0;
        }

        // Validate required fields
        if (empty($this->user_id)) {
            throw new Exception("User ID is required");
        }
        if (empty($this->pesan)) {
            throw new Exception("Pesan is required");
        }

        // Validate foreign keys
        $errors = $this->validateForeignKeys();
        if (!empty($errors)) {
            throw new Exception(implode(", ", $errors));
        }

        $query = "INSERT INTO {$this->table} (user_id, pesan, is_read, created_at) 
                  VALUES (:user_id, :pesan, :is_read, NOW())";
        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->pesan = htmlspecialchars(strip_tags($this->pesan));
        $this->is_read = isset($this->is_read) ? $this->is_read : 0;

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':pesan', $this->pesan);
        $stmt->bindParam(':is_read', $this->is_read);

        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE {$this->table} SET 
                    pesan = :pesan, 
                    is_read = :is_read 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->pesan = htmlspecialchars(strip_tags($this->pesan));
        $this->is_read = isset($this->is_read) ? $this->is_read : 0;

        $stmt->bindParam(':pesan', $this->pesan);
        $stmt->bindParam(':is_read', $this->is_read);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function markAsRead($id) {
        $query = "UPDATE {$this->table} SET is_read = 1 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        $id = htmlspecialchars(strip_tags($id));
        $stmt->bindParam(1, $id);
        
        return $stmt->execute();
    }

    public function markAllAsReadForUser($user_id) {
        $query = "UPDATE {$this->table} SET is_read = 1 WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        
        $user_id = htmlspecialchars(strip_tags($user_id));
        $stmt->bindParam(1, $user_id);
        
        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);
        
        return $stmt->execute();
    }

    public function countUnreadForUser($user_id) {
        $query = "SELECT COUNT(*) as total FROM {$this->table} 
                  WHERE user_id = ? AND is_read = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Enhanced validation with better error handling
    public function validateForeignKeys() {
        $errors = array();
        
        // Check user_id
        if ($this->user_id) {
            $query = "SELECT id FROM users WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$this->user_id]);
            if ($stmt->rowCount() == 0) {
                $errors[] = "User dengan ID {$this->user_id} tidak ditemukan";
            }
        }
        
        return $errors;
    }
    
    public function getTotalCount() {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Helper method to get all valid user IDs
    public function getValidUserIds() {
        $query = "SELECT id, username FROM users ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
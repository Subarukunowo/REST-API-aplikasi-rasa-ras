<?php
class Users {
    public $conn;
    public $table = 'users';
    
    public $id;
    public $username;
    public $password;
    public $email;
    public $role;
    public $is_blocked;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT id, username, email, role, is_blocked, created_at FROM {$this->table} ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT id, username, email, role, is_blocked, created_at FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO {$this->table} (username, password, email, role, is_blocked, created_at) 
                  VALUES (:username, :password, :email, :role, :is_blocked, NOW())";
        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->role = htmlspecialchars(strip_tags($this->role));
        $this->is_blocked = isset($this->is_blocked) ? $this->is_blocked : 0;

        // Hash password
        $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);

        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':is_blocked', $this->is_blocked);

        return $stmt->execute();
    }

    public function update() {
        // Check if password should be updated
        if (!empty($this->password)) {
            $query = "UPDATE {$this->table} SET 
                        username = :username, 
                        password = :password, 
                        email = :email, 
                        role = :role, 
                        is_blocked = :is_blocked 
                      WHERE id = :id";
        } else {
            $query = "UPDATE {$this->table} SET 
                        username = :username, 
                        email = :email, 
                        role = :role, 
                        is_blocked = :is_blocked 
                      WHERE id = :id";
        }
        
        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->role = htmlspecialchars(strip_tags($this->role));
        $this->is_blocked = isset($this->is_blocked) ? $this->is_blocked : 0;
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':is_blocked', $this->is_blocked);
        $stmt->bindParam(':id', $this->id);

        if (!empty($this->password)) {
            $this->password = htmlspecialchars(strip_tags($this->password));
            $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $hashed_password);
        }

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);
        
        return $stmt->execute();
    }

    public function login($username, $password) {
        $query = "SELECT id, username, password, email, role, is_blocked FROM {$this->table} 
                  WHERE username = ? AND is_blocked = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $username);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row && password_verify($password, $row['password'])) {
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->role = $row['role'];
            $this->is_blocked = $row['is_blocked'];
            return true;
        }
        return false;
    }

    public function usernameExists() {
        $query = "SELECT id FROM {$this->table} WHERE username = ? AND id != ?";
        $stmt = $this->conn->prepare($query);
        
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->id = isset($this->id) ? $this->id : 0;
        
        $stmt->bindParam(1, $this->username);
        $stmt->bindParam(2, $this->id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    public function emailExists() {
        $query = "SELECT id FROM {$this->table} WHERE email = ? AND id != ?";
        $stmt = $this->conn->prepare($query);
        
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->id = isset($this->id) ? $this->id : 0;
        
        $stmt->bindParam(1, $this->email);
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
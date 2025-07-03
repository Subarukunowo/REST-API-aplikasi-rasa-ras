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
public $token;
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

    // Sanitize input - KECUALI PASSWORD
    $this->username = htmlspecialchars(strip_tags($this->username));
    $this->email = htmlspecialchars(strip_tags($this->email));
    $this->role = htmlspecialchars(strip_tags($this->role));
    $this->is_blocked = isset($this->is_blocked) ? $this->is_blocked : 0;

    // Hash password LANGSUNG tanpa sanitization apapun
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

    // Sanitize input - KECUALI PASSWORD
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
        // Hash password LANGSUNG tanpa sanitization apapun
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
    try {
        $username = trim($username);

        $query = "SELECT id, username, password, email, role, is_blocked FROM {$this->table} 
                  WHERE username = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $username);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || intval($row['is_blocked']) === 1) {
            return false;
        }

        $stored_hash = $row['password'];
        $password_valid = password_verify($password, $stored_hash);

        if ($password_valid) {
            // Generate token random
            $this->token = bin2hex(random_bytes(32)); // 64 karakter

            // Simpan token ke database
            $update = $this->conn->prepare("UPDATE {$this->table} SET token = ? WHERE id = ?");
            $update->execute([$this->token, $row['id']]);

            // Set properti user
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->role = $row['role'];
            $this->is_blocked = intval($row['is_blocked']);

            return true;
        }

        return false;

    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        return false;
    }
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

    // Method helper untuk membuat pengguna dengan password yang di-hash (untuk testing)
    public function buatPenggunaTest($username, $password, $email = null, $role = 'pengguna') {
        $this->username = $username;
        $this->password = $password; // Akan di-hash di method create() TANPA htmlspecialchars
        $this->email = $email ?: $username . '@example.com';
        $this->role = $role;
        $this->is_blocked = 0;
        
        return $this->create();
    }

    // Method untuk reset password user tertentu (berguna untuk admin)
    public function resetPassword($user_id, $new_password) {
        try {
            // Hash password langsung tanpa htmlspecialchars
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            $query = "UPDATE {$this->table} SET password = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            
            return $stmt->execute([$hashed_password, $user_id]);
            
        } catch (Exception $e) {
            error_log("Reset password error: " . $e->getMessage());
            return false;
        }
    }

    // Method untuk change password (user mengubah password sendiri)
    public function changePassword($current_password, $new_password) {
        try {
            // Verifikasi password lama
            if (!isset($this->id)) {
                return false;
            }
            
            // Ambil password hash saat ini
            $query = "SELECT password FROM {$this->table} WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$this->id]);
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user_data || !password_verify($current_password, $user_data['password'])) {
                return false; // Password lama salah
            }
            
            // Update dengan password baru - hash langsung tanpa htmlspecialchars
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query = "UPDATE {$this->table} SET password = ? WHERE id = ?";
            $update_stmt = $this->conn->prepare($update_query);
            
            return $update_stmt->execute([$hashed_password, $this->id]);
            
        } catch (Exception $e) {
            error_log("Change password error: " . $e->getMessage());
            return false;
        }
    }

    // Method untuk mendapatkan user dengan pagination
    public function getAllPaginated($page = 1, $per_page = 10) {
        $offset = ($page - 1) * $per_page;
        
        $query = "SELECT id, username, email, role, is_blocked, created_at 
                  FROM {$this->table} 
                  ORDER BY created_at DESC 
                  LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $per_page, PDO::PARAM_INT);
        $stmt->bindParam(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method untuk block/unblock user
    public function toggleBlock($user_id) {
        try {
            $query = "UPDATE {$this->table} SET is_blocked = CASE WHEN is_blocked = 0 THEN 1 ELSE 0 END WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            
            return $stmt->execute([$user_id]);
            
        } catch (Exception $e) {
            error_log("Toggle block error: " . $e->getMessage());
            return false;
        }
    }
}
?>
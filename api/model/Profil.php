<?php
class Profil {
    public $conn;
    public $table = "profil";
    
    public $id;
    public $user_id;
    public $nama_lengkap;
    public $foto;
    public $bio;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT p.*, u.username 
                  FROM {$this->table} p
                  LEFT JOIN users u ON p.user_id = u.id
                  ORDER BY p.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT p.*, u.username 
                  FROM {$this->table} p
                  LEFT JOIN users u ON p.user_id = u.id
                  WHERE p.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUserId($user_id) {
        $query = "SELECT p.*, u.username 
                  FROM {$this->table} p
                  LEFT JOIN users u ON p.user_id = u.id
                  WHERE p.user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO {$this->table} (user_id, nama_lengkap, foto, bio, created_at) 
                  VALUES (:user_id, :nama_lengkap, :foto, :bio, NOW())";
        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->nama_lengkap = htmlspecialchars(strip_tags($this->nama_lengkap));
        $this->foto = htmlspecialchars(strip_tags($this->foto));
        $this->bio = htmlspecialchars(strip_tags($this->bio));

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':nama_lengkap', $this->nama_lengkap);
        $stmt->bindParam(':foto', $this->foto);
        $stmt->bindParam(':bio', $this->bio);

        return $stmt->execute();
    }

    public function update() {
    $fields = [];
    $params = [];

    if (!empty($this->nama_lengkap)) {
        $fields[] = "nama_lengkap = :nama_lengkap";
        $params[':nama_lengkap'] = htmlspecialchars(strip_tags($this->nama_lengkap));
    }

    if (!empty($this->foto)) {
        $fields[] = "foto = :foto";
        $params[':foto'] = htmlspecialchars(strip_tags($this->foto));
    }

    if (!empty($this->bio)) {
        $fields[] = "bio = :bio";
        $params[':bio'] = htmlspecialchars(strip_tags($this->bio));
    }

    // Tetap wajib
    $params[':id'] = htmlspecialchars(strip_tags($this->id));
    $params[':user_id'] = htmlspecialchars(strip_tags($this->user_id));

    // Jika tidak ada field yang akan diupdate, batalkan
    if (empty($fields)) {
        return false;
    }

    $query = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id AND user_id = :user_id";
    $stmt = $this->conn->prepare($query);

    return $stmt->execute($params);
}

   public function delete() {
    $query = "DELETE FROM {$this->table} WHERE id = ?";
    $stmt = $this->conn->prepare($query);
    
    $this->id = htmlspecialchars(strip_tags($this->id));
    
    $stmt->bindParam(1, $this->id);
    
    return $stmt->execute();
}

    public function userHasProfile($user_id) {
        $query = "SELECT id FROM {$this->table} WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    public function validateForeignKeys() {
        $errors = array();
        
        // Check user_id
        if ($this->user_id) {
            $query = "SELECT id FROM users WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$this->user_id]);
            if ($stmt->rowCount() == 0) {
                $errors[] = "User ID tidak valid";
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
}
?>
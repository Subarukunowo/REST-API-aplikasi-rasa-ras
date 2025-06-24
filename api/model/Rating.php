<?php
class Rating {
    public $conn;
    public $table = "rating";
    
    public $id;
    public $recipe_id;
    public $user_id;
    public $nilai;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT r.*, u.username, rs.nama_masakan 
                  FROM {$this->table} r
                  LEFT JOIN users u ON r.user_id = u.id
                  LEFT JOIN resep rs ON r.recipe_id = rs.id
                  ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT r.*, u.username, rs.nama_masakan 
                  FROM {$this->table} r
                  LEFT JOIN users u ON r.user_id = u.id
                  LEFT JOIN resep rs ON r.recipe_id = rs.id
                  WHERE r.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByRecipe($recipe_id) {
        $query = "SELECT r.*, u.username 
                  FROM {$this->table} r
                  LEFT JOIN users u ON r.user_id = u.id
                  WHERE r.recipe_id = ? 
                  ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $recipe_id);
        $stmt->execute();
        return $stmt;
    }

    public function getByUser($user_id) {
        $query = "SELECT r.*, rs.nama_masakan 
                  FROM {$this->table} r
                  LEFT JOIN resep rs ON r.recipe_id = rs.id
                  WHERE r.user_id = ? 
                  ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt;
    }

    public function getAverageRating($recipe_id) {
        $query = "SELECT AVG(nilai) as avg_rating, COUNT(*) as total_ratings 
                  FROM {$this->table} 
                  WHERE recipe_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $recipe_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
        // Check if user already rated this recipe
        if ($this->hasUserRated()) {
            return false; // User already rated this recipe
        }

        $query = "INSERT INTO {$this->table} (recipe_id, user_id, nilai, created_at) 
                  VALUES (:recipe_id, :user_id, :nilai, NOW())";
        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->recipe_id = htmlspecialchars(strip_tags($this->recipe_id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->nilai = htmlspecialchars(strip_tags($this->nilai));

        $stmt->bindParam(':recipe_id', $this->recipe_id);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':nilai', $this->nilai);

        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE {$this->table} SET nilai = :nilai 
                  WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->nilai = htmlspecialchars(strip_tags($this->nilai));

        $stmt->bindParam(':nilai', $this->nilai);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM {$this->table} WHERE id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        
        $stmt->bindParam(1, $this->id);
        $stmt->bindParam(2, $this->user_id);
        
        return $stmt->execute();
    }

    public function hasUserRated() {
        $query = "SELECT id FROM {$this->table} 
                  WHERE recipe_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->recipe_id);
        $stmt->bindParam(2, $this->user_id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    public function validateForeignKeys() {
        $errors = array();
        
        // Check recipe_id
        if ($this->recipe_id) {
            $query = "SELECT id FROM resep WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$this->recipe_id]);
            if ($stmt->rowCount() == 0) {
                $errors[] = "Recipe ID tidak valid";
            }
        }
        
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
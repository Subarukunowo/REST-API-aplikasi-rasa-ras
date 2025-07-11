<?php
class BahanMasakan {
    private $conn;
    public $table = 'bahan_masakan';

    public $id;
    public $recipe_id;
    public $nama_bahan;
    public $jumlah;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getByRecipe($recipe_id) {
        $query = "SELECT * FROM {$this->table} WHERE recipe_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $recipe_id);
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

    public function getAll() {
        $query = "SELECT * FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO {$this->table} (recipe_id, nama_bahan, jumlah)
                  VALUES (:recipe_id, :nama_bahan, :jumlah)";
        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->recipe_id = htmlspecialchars(strip_tags($this->recipe_id));
        $this->nama_bahan = htmlspecialchars(strip_tags($this->nama_bahan));
        $this->jumlah = htmlspecialchars(strip_tags($this->jumlah));

        $stmt->bindParam(':recipe_id', $this->recipe_id);
        $stmt->bindParam(':nama_bahan', $this->nama_bahan);
        $stmt->bindParam(':jumlah', $this->jumlah);

        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE {$this->table}
                  SET nama_bahan = :nama_bahan, jumlah = :jumlah
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->nama_bahan = htmlspecialchars(strip_tags($this->nama_bahan));
        $this->jumlah = htmlspecialchars(strip_tags($this->jumlah));

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':nama_bahan', $this->nama_bahan);
        $stmt->bindParam(':jumlah', $this->jumlah);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);
        
        return $stmt->execute();
    }
}
?>
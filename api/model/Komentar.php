<?php
class Komentar {
    private $conn;
    private $table = 'komentar';

    public $id;
    public $recipe_id;
    public $user_id;
    public $isi_komentar;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getByRecipe($recipe_id) {
        $query = "SELECT k.*, u.username FROM {$this->table} k
                  JOIN users u ON k.user_id = u.id
                  WHERE k.recipe_id = ? ORDER BY k.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $recipe_id);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO {$this->table} (recipe_id, user_id, isi_komentar, created_at)
                  VALUES (:recipe_id, :user_id, :isi_komentar, NOW())";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':recipe_id', $this->recipe_id);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':isi_komentar', $this->isi_komentar);

        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE {$this->table}
                  SET isi_komentar = :isi_komentar
                  WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':isi_komentar', $this->isi_komentar);

        return $stmt->execute();
    }
public function getById($id) {
    $query = "SELECT k.*, u.username FROM {$this->table} k
              JOIN users u ON k.user_id = u.id
              WHERE k.id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(1, $id);
    $stmt->execute();
    return $stmt;
}
    public function delete() {
        $query = "DELETE FROM {$this->table} WHERE id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->bindParam(2, $this->user_id);
        return $stmt->execute();
    }
}

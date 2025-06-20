<?php

class Rating {
    private $conn;
    private $table = "rating";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM $this->table";
        return $this->conn->query($query);
    }

    public function getById($id) {
        $query = "SELECT * FROM $this->table WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt;
    }

    public function create($data) {
        $query = "INSERT INTO $this->table (recipe_id, user_id, nilai) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$data['recipe_id'], $data['user_id'], $data['nilai']]);
    }

    public function update($id, $data) {
        $query = "UPDATE $this->table SET recipe_id = ?, user_id = ?, nilai = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$data['recipe_id'], $data['user_id'], $data['nilai'], $id]);
    }

    public function delete($id) {
        $query = "DELETE FROM $this->table WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}
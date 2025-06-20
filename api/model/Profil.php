<?php
class Profil {
    private $conn;
    private $table = "profil";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt;
    }

    public function create($user_id, $nama_lengkap, $foto, $bio) {
        $query = "INSERT INTO {$this->table} (user_id, nama_lengkap, foto, bio) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$user_id, $nama_lengkap, $foto, $bio]);
    }

    public function update($id, $user_id, $nama_lengkap, $foto, $bio) {
        $query = "UPDATE {$this->table} SET user_id = ?, nama_lengkap = ?, foto = ?, bio = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$user_id, $nama_lengkap, $foto, $bio, $id]);
    }

    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}

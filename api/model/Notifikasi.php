<?php
class Notifikasi {
    private $conn;
    private $table = "notifikasi";

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
        $query = "INSERT INTO $this->table (user_id, pesan, is_read, created_at) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$data['user_id'], $data['pesan'], $data['is_read'], $data['created_at']]);
    }

    public function update($id, $data) {
        $query = "UPDATE $this->table SET user_id = ?, pesan = ?, is_read = ?, created_at = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$data['user_id'], $data['pesan'], $data['is_read'], $data['created_at'], $id]);
    }

    public function delete($id) {
        $query = "DELETE FROM $this->table WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
    public function read() {
        $query = "SELECT * FROM notifikasi";
        return $this->conn->query($query);
    }
}
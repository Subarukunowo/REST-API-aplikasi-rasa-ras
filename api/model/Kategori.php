<?php
class Kategori {
    private $conn;
    private $table = "kategori";

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

    public function create($nama) {
        $query = "INSERT INTO {$this->table} (nama) VALUES (?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$nama]);
    }

    public function update($id, $nama) {
        $query = "UPDATE {$this->table} SET nama = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$nama, $id]);
    }

    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}

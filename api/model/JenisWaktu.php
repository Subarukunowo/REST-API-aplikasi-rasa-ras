<?php

class JenisWaktu {
    public $conn;
    public $table = 'jenis_waktu';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($nama) {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (nama) VALUES (?)");
        $result = $stmt->execute([$nama]);
        if ($result) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    public function delete($id) {
    $query = "DELETE FROM {$this->table} WHERE id = ?";
    $stmt = $this->conn->prepare($query);
    return $stmt->execute([$id]);
}
}
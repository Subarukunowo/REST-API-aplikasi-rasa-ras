<?php
class JenisWaktu {
    public $conn;
    public $table = 'jenis_waktu'; // sesuaikan dengan nama tabel Anda
    
    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt;
    }

    public function create($nama) {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (nama) VALUES (?)");
        return $stmt->execute([$nama]);
    }

    public function update($id, $nama) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET nama = ? WHERE id = ?");
        return $stmt->execute([$nama, $id]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

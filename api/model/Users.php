<?php
class Users {
    private $conn;
    public $id, $username, $password, $email, $role, $is_blocked, $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT * FROM users";
        return $this->conn->query($query);
    }

    public function create() {
        $stmt = $this->conn->prepare("INSERT INTO users(username, password, email, role, is_blocked, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssssi", $this->username, $this->password, $this->email, $this->role, $this->is_blocked);
        return $stmt->execute();
    }

    public function update() {
        $stmt = $this->conn->prepare("UPDATE users SET username=?, password=?, email=?, role=?, is_blocked=? WHERE id=?");
        $stmt->bind_param("ssssii", $this->username, $this->password, $this->email, $this->role, $this->is_blocked, $this->id);
        return $stmt->execute();
    }

    public function delete() {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id=?");
        $stmt->bind_param("i", $this->id);
        return $stmt->execute();
    }
}

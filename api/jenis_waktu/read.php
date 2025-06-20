<?php
header("Content-Type: application/json");
include_once '../conf/db_config.php';
include_once '../model/JenisWaktu.php';

try {
    $database = new Database();
    $db = $database->connect();
    
    // Inisialisasi object JenisWaktu
    $waktu = new JenisWaktu($db);
    
    if (isset($_GET['id'])) {
        $query = "SELECT * FROM {$waktu->table} WHERE id = ?";
        $stmt = $waktu->conn->prepare($query);
        $stmt->execute([$_GET['id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            echo json_encode(["error" => "Data tidak ditemukan"]);
        } else {
            echo json_encode($result);
        }
    } else {
        $query = "SELECT * FROM {$waktu->table}";
        $stmt = $waktu->conn->query($query);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
    }
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
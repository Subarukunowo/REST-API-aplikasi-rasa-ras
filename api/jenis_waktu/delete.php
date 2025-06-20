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
        $query = "DELETE FROM {$waktu->table} WHERE id = ?";
        $stmt = $waktu->conn->prepare($query);
        $result = $stmt->execute([$_GET['id']]);
        echo json_encode(["success" => $result]);
    } else {
        echo json_encode(["error" => "ID tidak diberikan"]);
    }
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
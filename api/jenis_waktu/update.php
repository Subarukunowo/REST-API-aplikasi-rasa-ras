<?php
header("Content-Type: application/json");
include_once '../conf/db_config.php';
include_once '../model/JenisWaktu.php';

try {
    $database = new Database();
    $db = $database->connect();
    
    // Inisialisasi object JenisWaktu
    $waktu = new JenisWaktu($db);
    
    $data = json_decode(file_get_contents("php://input"));
    
    if (!$data || !isset($data->nama) || !isset($data->id)) {
        echo json_encode(["error" => "Data tidak lengkap"]);
        exit;
    }
    
    $query = "UPDATE {$waktu->table} SET nama = :nama WHERE id = :id";
    $stmt = $waktu->conn->prepare($query);
    $stmt->bindParam(':nama', $data->nama);
    $stmt->bindParam(':id', $data->id);
    
    $result = $stmt->execute();
    echo json_encode(["success" => $result]);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
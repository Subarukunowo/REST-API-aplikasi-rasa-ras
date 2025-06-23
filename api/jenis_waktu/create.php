<?php

header("Content-Type: application/json");
include_once '../conf/db_config.php';
include_once '../model/JenisWaktu.php';

try {
    $database = new Database();
    $db = $database->connect();
    $waktu = new JenisWaktu($db);

    // Cek apakah input JSON atau form
    $data = json_decode(file_get_contents("php://input"));
    if (!$data) {
        // Coba ambil dari $_POST jika bukan JSON
        $data = (object)$_POST;
    }

    if (!isset($data->nama) || empty($data->nama)) {
        echo json_encode(["error" => "Data nama tidak diberikan"]);
        exit;
    }

    $query = "INSERT INTO {$waktu->table} (nama) VALUES (:nama)";
    $stmt = $waktu->conn->prepare($query);
    $stmt->bindParam(':nama', $data->nama);

    $result = $stmt->execute();

    if ($result) {
        echo json_encode([
            "success" => true,
            "id" => $waktu->conn->lastInsertId()
        ]);
    } else {
        echo json_encode(["success" => false]);
    }
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
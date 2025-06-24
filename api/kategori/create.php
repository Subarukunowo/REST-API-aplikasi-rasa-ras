<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include_once '../conf/db_config.php';
include_once "../model/Kategori.php";

$database = new Database();
$db = $database->connect();

// Ambil data JSON
$data = json_decode(file_get_contents("php://input"));

// Validasi input
if (empty($data->nama)) {
    http_response_code(400); // Bad Request
    echo json_encode([
        "success" => false,
        "message" => "Field 'nama' wajib diisi."
    ]);
    exit();
}

try {
    $stmt = $db->prepare("INSERT INTO kategori (nama) VALUES (?)");
    $result = $stmt->execute([$data->nama]);

    if ($result) {
        http_response_code(201); // Created
        echo json_encode([
            "success" => true,
            "message" => "Kategori berhasil ditambahkan."
        ]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode([
            "success" => false,
            "message" => "Gagal menambahkan kategori."
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Terjadi kesalahan: " . $e->getMessage()
    ]);
}

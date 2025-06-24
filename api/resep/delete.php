<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type");

// Cek apakah metode request adalah DELETE
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
    exit();
}

include_once '../conf/db_config.php';
include_once '../model/Resep.php';

try {
    $database = new Database();
    $db = $database->connect();
    $resep = new Resep($db);

    // Ambil input JSON
    $input = file_get_contents("php://input");
    $data = json_decode($input);

    if (json_last_error() !== JSON_ERROR_NONE || !isset($data->id) || !is_numeric($data->id)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID tidak valid atau format JSON salah"]);
        exit();
    }

    $resep->id = (int)$data->id;

    // Jalankan delete
    if ($resep->delete()) {
        http_response_code(200);
        echo json_encode(["success" => true, "message" => "Resep berhasil dihapus"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Gagal menghapus resep"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
}

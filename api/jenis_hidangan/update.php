<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT, PATCH");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/db_config.php';
include_once '../model/JenisHidangan.php';

// Check request method
if (!in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'PATCH'])) {
    http_response_code(405);
    echo json_encode(array(
        "success" => false,
        "message" => "Method tidak diizinkan"
    ));
    exit();
}

try {
    $database = new Database();
    $db = $database->connect();
    
    if (!$db) {
        throw new Exception("Koneksi database gagal");
    }
    
    $jenisHidangan = new JenisHidangan($db);
    
    // Get posted data
    $data = json_decode(file_get_contents("php://input"));
    
    // Validation
    if (empty($data->id)) {
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "ID harus diisi"
        ));
        exit();
    }
    
    if (empty($data->nama)) {
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "Nama jenis hidangan harus diisi"
        ));
        exit();
    }
    
    // Set ID and check if record exists
    $jenisHidangan->id = $data->id;
    if (!$jenisHidangan->getById($data->id)) {
        http_response_code(404);
        echo json_encode(array(
            "success" => false,
            "message" => "Data tidak ditemukan"
        ));
        exit();
    }
    
    // Set property values
    $jenisHidangan->nama = $data->nama;
    
    // Check if nama already exists (exclude current record)
    if ($jenisHidangan->nameExists()) {
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "Nama jenis hidangan sudah ada"
        ));
        exit();
    }
    
    // Update the record
    if ($jenisHidangan->update()) {
        http_response_code(200);
        echo json_encode(array(
            "success" => true,
            "message" => "Jenis hidangan berhasil diupdate",
            "data" => array(
                "id" => $jenisHidangan->id,
                "nama" => $jenisHidangan->nama
            )
        ));
    } else {
        http_response_code(503);
        echo json_encode(array(
            "success" => false,
            "message" => "Gagal mengupdate jenis hidangan"
        ));
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "Server error: " . $e->getMessage()
    ));
}
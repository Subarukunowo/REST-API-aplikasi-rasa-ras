<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(array(
        "success" => false,
        "message" => "Method tidak diizinkan"
    ));
    exit();
}

include_once '../conf/db_config.php';
include_once '../model/JenisHidangan.php';

try {
    $database = new Database();
    $db = $database->connect();
    
    if (!$db) {
        throw new Exception("Koneksi database gagal");
    }
    
    $jenisHidangan = new JenisHidangan($db);
    
    // Get posted data
    $input = file_get_contents("php://input");
    $data = json_decode($input);
    
    // Validate JSON input
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "Invalid JSON format"
        ));
        exit();
    }
    
    // Validation
    if (empty($data->nama) || trim($data->nama) === '') {
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "Nama jenis hidangan harus diisi"
        ));
        exit();
    }
    
    // Set property values
    $jenisHidangan->nama = trim($data->nama);
    
    // Check if nama already exists
    if ($jenisHidangan->nameExists()) {
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "Nama jenis hidangan sudah ada"
        ));
        exit();
    }
    
    // Create the record
    if ($jenisHidangan->create()) {
        $lastInsertId = $db->lastInsertId();
        
        http_response_code(201);
        echo json_encode(array(
            "success" => true,
            "message" => "Jenis hidangan berhasil dibuat",
            "data" => array(
                "id" => (int)$lastInsertId,
                "nama" => $jenisHidangan->nama
            )
        ));
    } else {
        http_response_code(500);
        echo json_encode(array(
            "success" => false,
            "message" => "Gagal membuat jenis hidangan"
        ));
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "Server error: " . $e->getMessage()
    ));
}
?>
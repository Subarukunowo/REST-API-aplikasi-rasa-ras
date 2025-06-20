<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/db_config.php';
include_once '../model/Resep.php';

// Check request method
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
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
    
    $resep = new Resep($db);
    
    // Get posted data
    $data = json_decode(file_get_contents("php://input"));
    
    // Validation required fields
    $required_fields = ['user_id', 'nama_masakan', 'kategori_id', 'waktu_memasak', 
                       'bahan_utama', 'deskripsi', 'level_kesulitan', 'waktu_id', 'hidangan_id'];
    
    $missing_fields = array();
    foreach ($required_fields as $field) {
        if (empty($data->$field)) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "Field berikut harus diisi: " . implode(', ', $missing_fields)
        ));
        exit();
    }
    
    // Validation level_kesulitan
    $valid_levels = ['Mudah', 'Sedang', 'Sulit'];
    if (!in_array($data->level_kesulitan, $valid_levels)) {
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "Level kesulitan harus: " . implode(', ', $valid_levels)
        ));
        exit();
    }
    
    // Validation waktu_memasak (should be positive integer)
    if (!is_numeric($data->waktu_memasak) || $data->waktu_memasak <= 0) {
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "Waktu memasak harus berupa angka positif (dalam menit)"
        ));
        exit();
    }
    
    // Set property values
    $resep->user_id = $data->user_id;
    $resep->nama_masakan = $data->nama_masakan;
    $resep->kategori_id = $data->kategori_id;
    $resep->waktu_memasak = $data->waktu_memasak;
    $resep->bahan_utama = $data->bahan_utama;
    $resep->deskripsi = $data->deskripsi;
    $resep->level_kesulitan = $data->level_kesulitan;
    $resep->waktu_id = $data->waktu_id;
    $resep->hidangan_id = $data->hidangan_id;
    $resep->video = isset($data->video) ? $data->video : null;
    
    // Validate foreign keys
    $validation_errors = $resep->validateForeignKeys();
    if (!empty($validation_errors)) {
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "Validation error",
            "errors" => $validation_errors
        ));
        exit();
    }
}
catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "Server error: " . $e->getMessage()
    ));
    exit();
}
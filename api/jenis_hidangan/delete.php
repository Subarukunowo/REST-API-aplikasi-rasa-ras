<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/db_config.php';
include_once '../model/JenisHidangan.php';

// Check request method
if ($_SERVER['REQUEST_METHOD'] != 'DELETE') {
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
    
    // Get ID from URL parameter or JSON body
    $id = 0;
    
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
    } else {
        // Try to get from JSON body
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->id)) {
            $id = intval($data->id);
        }
    }
    
    // Validation
    if (empty($id) || $id <= 0) {
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "ID harus diisi dan valid"
        ));
        exit();
    }
    
    // Check if record exists
    if (!$jenisHidangan->getById($id)) {
        http_response_code(404);
        echo json_encode(array(
            "success" => false,
            "message" => "Data tidak ditemukan"
        ));
        exit();
    }
    
    // Store data before deletion for response
    $deletedData = array(
        "id" => $jenisHidangan->id,
        "nama" => $jenisHidangan->nama
    );
    
    // Set ID and delete
    $jenisHidangan->id = $id;
    
    if ($jenisHidangan->delete()) {
        http_response_code(200);
        echo json_encode(array(
            "success" => true,
            "message" => "Jenis hidangan berhasil dihapus",
            "deleted_data" => $deletedData
        ));
    } else {
        http_response_code(503);
        echo json_encode(array(
            "success" => false,
            "message" => "Gagal menghapus jenis hidangan"
        ));
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "Server error: " . $e->getMessage()
    ));
}
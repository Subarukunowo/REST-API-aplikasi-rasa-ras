<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] != 'DELETE') {
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
    
    // Get ID from URL parameter or JSON body
    $id = 0;
    
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
    } else {
        // Try to get from JSON body
        $input = file_get_contents("php://input");
        $data = json_decode($input);
        
        if ($data && isset($data->id)) {
            $id = intval($data->id);
        }
    }
    
    // Validation
    if (empty($id) || $id <= 0) {
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "ID harus diisi dan berupa angka yang valid"
        ));
        exit();
    }
    
    // Check if record exists and get data before deletion
    $existing = $jenisHidangan->getById($id);
    if (!$existing) {
        http_response_code(404);
        echo json_encode(array(
            "success" => false,
            "message" => "Data tidak ditemukan"
        ));
        exit();
    }
    
    // Store data before deletion for response
    $deletedData = array(
        "id" => (int)$existing['id'],
        "nama" => $existing['nama']
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
        http_response_code(500);
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
?>
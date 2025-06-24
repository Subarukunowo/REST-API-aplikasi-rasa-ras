<?php
// Headers for CORS and JSON response
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

// Only allow DELETE method
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
    exit();
}

include_once '../conf/db_config.php';
include_once '../model/BahanMasakan.php';

try {
    $db = new Database();
    $conn = $db->connect();

    $bahan = new BahanMasakan($conn);
    
    // Get ID from URL parameter or JSON body
    $id = null;
    
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    } else {
        // Try to get from JSON body
        $input = file_get_contents("php://input");
        $data = json_decode($input);
        
        if ($data && isset($data->id)) {
            $id = $data->id;
        }
    }

    // Validate ID
    if (empty($id)) {
        http_response_code(400);
        echo json_encode([
            "message" => "Missing required field: id"
        ]);
        exit();
    }

    // Check if record exists
    $existing = $bahan->getById($id);
    if (!$existing) {
        http_response_code(404);
        echo json_encode(["message" => "Data tidak ditemukan"]);
        exit();
    }

    // Set object property
    $bahan->id = $id;

    // Delete the ingredient
    if ($bahan->delete()) {
        http_response_code(200);
        echo json_encode([
            "message" => "Bahan masakan berhasil dihapus",
            "success" => true
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "message" => "Gagal menghapus bahan masakan",
            "success" => false
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "message" => "Server error: " . $e->getMessage(),
        "success" => false
    ]);
}
?>
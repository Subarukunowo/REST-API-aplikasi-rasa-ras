<?php
// Headers for CORS and JSON response
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

// Only allow POST method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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
    
    // Get posted data
    $input = file_get_contents("php://input");
    $data = json_decode($input);

    // Validate JSON input
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(["message" => "Invalid JSON format"]);
        exit();
    }

    // Validate required fields
    if (empty($data->recipe_id) || empty($data->nama_bahan) || empty($data->jumlah)) {
        http_response_code(400);
        echo json_encode([
            "message" => "Missing required fields: recipe_id, nama_bahan, jumlah"
        ]);
        exit();
    }

    // Set object properties
    $bahan->recipe_id = $data->recipe_id;
    $bahan->nama_bahan = $data->nama_bahan;
    $bahan->jumlah = $data->jumlah;

    // Create the ingredient
    if ($bahan->create()) {
        http_response_code(201);
        echo json_encode([
            "message" => "Bahan masakan berhasil ditambahkan",
            "success" => true
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "message" => "Gagal menambahkan bahan masakan",
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
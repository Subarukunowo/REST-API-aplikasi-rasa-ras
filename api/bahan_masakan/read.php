<?php
// Headers for CORS and JSON response
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Only allow GET method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
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

    $data = [];

    if (isset($_GET['id'])) {
        // Get data by ID
        $result = $bahan->getById($_GET['id']);
        if ($result) {
            $data = $result;
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Data tidak ditemukan"]);
            exit();
        }
    } elseif (isset($_GET['recipe_id'])) {
        // Get data by recipe_id
        $stmt = $bahan->getByRecipe($_GET['recipe_id']);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        
        if (empty($data)) {
            http_response_code(404);
            echo json_encode(["message" => "Data tidak ditemukan untuk recipe_id tersebut"]);
            exit();
        }
    } else {
        // Get all data
        $stmt = $bahan->getAll();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
    }

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $data
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "message" => "Server error: " . $e->getMessage(),
        "success" => false
    ]);
}
?>
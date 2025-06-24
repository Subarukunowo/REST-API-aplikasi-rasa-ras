<?php
// ===== FILE: api/users/delete.php =====
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
    exit();
}

include_once '../conf/db_config.php';
include_once '../model/Users.php';

try {
    $database = new Database();
    $db = $database->connect();
    $user = new Users($db);
    
    // Get ID from URL or JSON body
    $id = null;
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
    } else {
        $input = file_get_contents("php://input");
        $data = json_decode($input);
        if ($data && isset($data->id)) {
            $id = intval($data->id);
        }
    }
    
    if (empty($id) || $id <= 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID harus diisi dan valid"]);
        exit();
    }
    
    // Check if user exists
    $existing = $user->getById($id);
    if (!$existing) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "User tidak ditemukan"]);
        exit();
    }
    
    $deletedData = [
        "id" => (int)$existing['id'],
        "username" => $existing['username'],
        "email" => $existing['email']
    ];
    
    $user->id = $id;
    
    if ($user->delete()) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "User berhasil dihapus",
            "deleted_data" => $deletedData
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Gagal menghapus user"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
}
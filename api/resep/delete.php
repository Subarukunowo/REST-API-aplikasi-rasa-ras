<?php
// ===== FILE: api/resep/delete.php =====
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
include_once '../model/Resep.php';

try {
    $database = new Database();
    $db = $database->connect();
    $resep = new Resep($db);
    
    // Get data from URL or JSON body
    $id = null;
    $user_id = null;
    
    if (isset($_GET['id']) && isset($_GET['user_id'])) {
        $id = intval($_GET['id']);
        $user_id = intval($_GET['user_id']);
    } else {
        $input = file_get_contents("php://input");
        $data = json_decode($input);
        if ($data && isset($data->id) && isset($data->user_id)) {
            $id = intval($data->id);
            $user_id = intval($data->user_id);
        }
    }
    
    if (empty($id) || empty($user_id)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID dan User ID harus diisi"]);
        exit();
    }
    
    // Check if recipe exists
    $existing = $resep->getById($id);
    if (!$existing) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Resep tidak ditemukan"]);
        exit();
    }
    
    // Check ownership
    if ($existing['user_id'] != $user_id) {
        http_response_code(403);
        echo json_encode(["success" => false, "message" => "Anda tidak memiliki akses untuk menghapus resep ini"]);
        exit();
    }
    
    $deletedData = [
        "id" => (int)$existing['id'],
        "nama_masakan" => $existing['nama_masakan'],
        "user_name" => $existing['user_name']
    ];
    
    $resep->id = $id;
    $resep->user_id = $user_id;
    
    if ($resep->delete()) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Resep berhasil dihapus",
            "deleted_data" => $deletedData
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Gagal menghapus resep"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
}
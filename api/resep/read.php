<?php
// ===== FILE: api/resep/read.php =====
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
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
    
    if (isset($_GET['id'])) {
        // Get single recipe
        $id = intval($_GET['id']);
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "ID tidak valid"]);
            exit();
        }
        
        $result = $resep->getById($id);
        if ($result) {
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "data" => $result
            ]);
        } else {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "Resep tidak ditemukan"]);
        }
    } elseif (isset($_GET['user_id'])) {
        // Get recipes by user
        $user_id = intval($_GET['user_id']);
        $stmt = $resep->getByUserId($user_id);
        $recipes = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $recipes[] = $row;
        }
        
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "total" => count($recipes),
            "data" => $recipes
        ]);
    } elseif (isset($_GET['search'])) {
        // Search recipes
        $keyword = $_GET['search'];
        $kategori_id = isset($_GET['kategori_id']) ? intval($_GET['kategori_id']) : null;
        $level_kesulitan = isset($_GET['level_kesulitan']) ? $_GET['level_kesulitan'] : null;
        
        $stmt = $resep->search($keyword, $kategori_id, $level_kesulitan);
        $recipes = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $recipes[] = $row;
        }
        
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "total" => count($recipes),
            "data" => $recipes
        ]);
    } else {
        // Get all recipes
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 0;
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        
        $stmt = $resep->getAll($limit, $offset);
        $recipes = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $recipes[] = $row;
        }
        
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "total" => count($recipes),
            "data" => $recipes
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
}
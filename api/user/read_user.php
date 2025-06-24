<?php
// ===== FILE: api/users/read.php =====
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
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
    
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "ID tidak valid"]);
            exit();
        }
        
        $result = $user->getById($id);
        
        if ($result) {
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "data" => [
                    "id" => (int)$result['id'],
                    "username" => $result['username'],
                    "email" => $result['email'],
                    "role" => $result['role'],
                    "is_blocked" => (int)$result['is_blocked'],
                    "created_at" => $result['created_at']
                ]
            ]);
        } else {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "User tidak ditemukan"]);
        }
    } else {
        $stmt = $user->getAll();
        $users = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = [
                "id" => (int)$row['id'],
                "username" => $row['username'],
                "email" => $row['email'],
                "role" => $row['role'],
                "is_blocked" => (int)$row['is_blocked'],
                "created_at" => $row['created_at']
            ];
        }
        
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "total" => count($users),
            "data" => $users
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
}

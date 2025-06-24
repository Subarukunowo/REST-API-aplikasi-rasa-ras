<?php
// ===== FILE: api/users/create.php =====
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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
    
    $input = file_get_contents("php://input");
    $data = json_decode($input);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid JSON format"]);
        exit();
    }
    
    // Validation
    if (empty($data->username) || empty($data->password) || empty($data->email)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Username, password, dan email harus diisi"]);
        exit();
    }
    
    // Validate email format
    if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Format email tidak valid"]);
        exit();
    }
    
    // Set properties
    $user->username = trim($data->username);
    $user->email = trim($data->email);
    $user->password = $data->password;
    $user->role = isset($data->role) ? $data->role : 'user';
    $user->is_blocked = isset($data->is_blocked) ? $data->is_blocked : 0;
    
    // Check if username exists
    if ($user->usernameExists()) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Username sudah digunakan"]);
        exit();
    }
    
    // Check if email exists
    if ($user->emailExists()) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Email sudah digunakan"]);
        exit();
    }
    
    if ($user->create()) {
        $lastInsertId = $db->lastInsertId();
        
        http_response_code(201);
        echo json_encode([
            "success" => true,
            "message" => "User berhasil dibuat",
            "data" => [
                "id" => (int)$lastInsertId,
                "username" => $user->username,
                "email" => $user->email,
                "role" => $user->role,
                "is_blocked" => (int)$user->is_blocked
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Gagal membuat user"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
}

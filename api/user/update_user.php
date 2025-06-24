<?php
// ===== FILE: api/users/update.php =====
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT, PATCH");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

if (!in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'PATCH'])) {
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
    if (empty($data->id) || empty($data->username) || empty($data->email)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID, username, dan email harus diisi"]);
        exit();
    }
    
    // Validate email format
    if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Format email tidak valid"]);
        exit();
    }
    
    // Check if user exists
    $existing = $user->getById($data->id);
    if (!$existing) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "User tidak ditemukan"]);
        exit();
    }
    
    // Set properties
    $user->id = $data->id;
    $user->username = trim($data->username);
    $user->email = trim($data->email);
    $user->role = isset($data->role) ? $data->role : $existing['role'];
    $user->is_blocked = isset($data->is_blocked) ? $data->is_blocked : $existing['is_blocked'];
    
    // Set password only if provided
    if (!empty($data->password)) {
        $user->password = $data->password;
    }
    
    // Check if username exists (exclude current user)
    if ($user->usernameExists()) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Username sudah digunakan"]);
        exit();
    }
    
    // Check if email exists (exclude current user)
    if ($user->emailExists()) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Email sudah digunakan"]);
        exit();
    }
    
    if ($user->update()) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "User berhasil diupdate",
            "data" => [
                "id" => (int)$user->id,
                "username" => $user->username,
                "email" => $user->email,
                "role" => $user->role,
                "is_blocked" => (int)$user->is_blocked
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Gagal mengupdate user"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
}
?>
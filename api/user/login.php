<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
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

include_once '../config/db_config.php';
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
    
    if (empty($data->username) || empty($data->password)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Username dan password harus diisi"]);
        exit();
    }
    
    if ($user->login($data->username, $data->password)) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Login berhasil",
            "data" => [
                "id" => (int)$user->id,
                "username" => $user->username,
                "email" => $user->email,
                "role" => $user->role
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Username atau password salah"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
}
        
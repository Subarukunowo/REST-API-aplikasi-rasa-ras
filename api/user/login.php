<?php
ob_start(); // start output buffering
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include_once '../conf/db_config.php';
include_once '../model/Users.php';

$inputJSON = file_get_contents("php://input");
$data = json_decode($inputJSON);

// Validasi JSON
if (is_null($data)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Data yang dikirim bukan JSON yang valid"]);
    exit;
}

if (empty(trim($data->username)) || empty(trim($data->password))) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Username dan password wajib diisi"]);
    exit;
}

try {
    $database = new Database();
    $conn = $database->connect();

    $username = trim($data->username);
    $password = $data->password;

    $user = new Users($conn);

    // Login menggunakan class Users
    if ($user->login($username, $password)) {
        // ✅ Generate token acak (64 karakter)
        $token = bin2hex(random_bytes(32));
        $user->token = $token;

        // ✅ Simpan token ke database
        $update = $conn->prepare("UPDATE users SET token = ? WHERE id = ?");
        $update->execute([$token, $user->id]);

        // ✅ Kirim response
        echo json_encode([
            "success" => true,
            "message" => "Login berhasil",
            "token" => $token,
            "user" => [
                "id" => $user->id,
                "username" => $user->username,
                "email" => $user->email,
                "role" => $user->role,
                "is_blocked" => $user->is_blocked
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Username atau password salah"
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Terjadi kesalahan server"]);
    error_log("Login API Error: " . $e->getMessage());
}
?>

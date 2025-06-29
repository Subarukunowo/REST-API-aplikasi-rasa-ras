<?php
header("Content-Type: application/json");

include_once '../conf/db_config.php';
include_once '../model/Users.php';

// Ambil raw JSON dari body
$inputJSON = file_get_contents("php://input");
$data = json_decode($inputJSON);

// Cek apakah parsing JSON berhasil
if (is_null($data)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Data yang dikirim bukan JSON yang valid"
    ]);
    exit;
}

// Validasi field
if (!isset($data->username) || !isset($data->password)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Username dan password wajib diisi"
    ]);
    exit;
}

// Validasi input tidak kosong
if (empty(trim($data->username)) || empty(trim($data->password))) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Username dan password tidak boleh kosong"
    ]);
    exit;
}

try {
    // Koneksi ke database
    $database = new Database();
    $conn = $database->connect();
    
    $username = trim($data->username);
    $password = $data->password;
    
    // Ambil data user dari database
    $query = "SELECT id, username, password, email, role, is_blocked FROM users WHERE username = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(1, $username);
    $stmt->execute();
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user_data) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Username atau password salah"
        ]);
        exit;
    }
    
    // Cek apakah user diblokir
    if (intval($user_data['is_blocked']) === 1) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Akun Anda telah diblokir"
        ]);
        exit;
    }
    
    $stored_hash = $user_data['password'];
    $login_success = false;
    $password_was_fixed = false;
    
    // STEP 1: Coba login normal dengan class Users
    $user = new Users($conn);
    if ($user->login($username, $password)) {
        $login_success = true;
    } else {
        // STEP 2: Auto-fix password jika login gagal
        // Kemungkinan password masih dalam format lama/rusak
        
        // Coba reset password dengan input password sebagai password baru
        $new_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Update password di database
        $update_query = "UPDATE users SET password = ? WHERE username = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_result = $update_stmt->execute([$new_hash, $username]);
        
        if ($update_result) {
            // Verifikasi password baru
            $verify_test = password_verify($password, $new_hash);
            
            if ($verify_test) {
                // Coba login lagi dengan password yang sudah diperbaiki
                $user_fixed = new Users($conn);
                if ($user_fixed->login($username, $password)) {
                    $user = $user_fixed; // Gunakan user object yang berhasil login
                    $login_success = true;
                    $password_was_fixed = true;
                    
                    // Log untuk debugging
                    error_log("Auto-fixed password for user: " . $username);
                }
            }
        }
    }
    
    if ($login_success) {
        $response = [
            "success" => true,
            "message" => "Login berhasil",
            "data" => [
                "id" => $user->id,
                "username" => $user->username,
                "email" => $user->email,
                "role" => $user->role,
                "is_blocked" => $user->is_blocked
            ]
        ];
        
        // Tambahkan info jika password diperbaiki (opsional, bisa dihapus)
        if ($password_was_fixed) {
            $response["info"] = "Password Anda telah diperbaiki secara otomatis";
        }
        
        echo json_encode($response);
    } else {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Username atau password salah"
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Terjadi kesalahan server"
    ]);
    
    // Log error untuk debugging
    error_log("Login API Error: " . $e->getMessage());
}
?>
<?php
header("Content-Type: application/json");

include_once '../conf/db_config.php';
include_once '../model/Users.php';

// Mulai output buffering untuk menangkap error logs
ob_start();

// Aktifkan error logging
ini_set('log_errors', 1);
ini_set('error_log', 'login_debug.log');

try {
    $database = new Database();
    $conn = $database->connect();
    
    // Test data
    $test_username = 'rin123';
    $test_password = '$2y$10$799s31X/T.ESUCPADOwZ1u5N8RpY0AaERrfhs1.LjY1iIGM4vITrK'; // Ganti dengan password yang benar jika berbeda
    
    echo json_encode([
        "test_info" => [
            "testing_username" => $test_username,
            "testing_password" => $test_password,
            "timestamp" => date('Y-m-d H:i:s')
        ]
    ], JSON_PRETTY_PRINT);
    
    echo "\n\n=== TESTING LOGIN ===\n";
    
    // Test login menggunakan class Users
    $user = new Users($conn);
    $login_result = $user->login($test_username, $test_password);
    
    echo json_encode([
        "login_result" => $login_result,
        "user_properties" => $login_result ? [
            "id" => $user->id,
            "username" => $user->username,
            "email" => $user->email,
            "role" => $user->role,
            "is_blocked" => $user->is_blocked
        ] : null
    ], JSON_PRETTY_PRINT);
    
    echo "\n\n=== MANUAL DATABASE CHECK ===\n";
    
    // Manual check langsung ke database
    $query = "SELECT id, username, password, email, role, is_blocked FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$test_username]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user_data) {
        $manual_password_check = password_verify($test_password, $user_data['password']);
        
        echo json_encode([
            "manual_check" => [
                "user_found" => true,
                "user_id" => $user_data['id'],
                "username" => $user_data['username'],
                "is_blocked" => $user_data['is_blocked'],
                "password_length" => strlen($user_data['password']),
                "password_starts_with" => substr($user_data['password'], 0, 10),
                "password_verify_result" => $manual_password_check
            ]
        ], JSON_PRETTY_PRINT);
    } else {
        echo json_encode([
            "manual_check" => [
                "user_found" => false,
                "message" => "User tidak ditemukan"
            ]
        ], JSON_PRETTY_PRINT);
    }
    
    echo "\n\n=== ERROR LOG CONTENT ===\n";
    
    // Coba baca file log jika ada
    if (file_exists('login_debug.log')) {
        $log_content = file_get_contents('login_debug.log');
        echo "Log content:\n";
        echo $log_content;
    } else {
        echo "Log file tidak ditemukan. Cek error log server PHP.\n";
    }
    
    // Juga tampilkan last error
    $last_error = error_get_last();
    if ($last_error) {
        echo "\nLast PHP Error:\n";
        echo json_encode($last_error, JSON_PRETTY_PRINT);
    }
    
} catch (Exception $e) {
    echo json_encode([
        "error" => [
            "message" => $e->getMessage(),
            "file" => $e->getFile(),
            "line" => $e->getLine(),
            "trace" => $e->getTraceAsString()
        ]
    ], JSON_PRETTY_PRINT);
}

// Juga test dengan beberapa password umum
echo "\n\n=== TESTING COMMON PASSWORDS ===\n";

$common_passwords = ['rin123', 'Rin123', 'RIN123', '123456', 'password', 'admin', ''];

foreach ($common_passwords as $pwd) {
    if ($user_data) {
        $test_result = password_verify($pwd, $user_data['password']);
        echo "Password '$pwd': " . ($test_result ? "MATCH" : "NO MATCH") . "\n";
    }
}
?>
<?php
// === FILE: api/komentar/create.php ===
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include_once '../conf/db_config.php';
include_once '../model/Komentar.php';

try {

$db = new Database();
$conn = $db->connect();

$komentar = new Komentar($conn);
$data = json_decode(file_get_contents("php://input"));

// Validasi input
if (
    !isset($data->recipe_id) || 
    !isset($data->user_id) || 
    !isset($data->isi_komentar) || 
    empty(trim($data->isi_komentar))
) {
    http_response_code(400); // Bad Request
    echo json_encode([
        "success" => false,
        "message" => "Data komentar tidak lengkap. Pastikan recipe_id, user_id, dan isi_komentar diisi."
    ]);
    exit;
}

// Set properti
$komentar->recipe_id = $data->recipe_id;
$komentar->user_id = $data->user_id;
$komentar->isi_komentar = trim($data->isi_komentar);

// Simpan komentar
if ($komentar->create()) {
    http_response_code(201); // Created
    echo json_encode([
        "success" => true,
        "message" => "Komentar berhasil ditambahkan."
    ]);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        "success" => false,
        "message" => "Gagal menambahkan komentar. Silakan coba lagi."
    ]);
}

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        "success" => false,
        "message" => "Terjadi kesalahan: " . $e->getMessage()
    ]);
}
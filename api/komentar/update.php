<?php
// === FILE: api/komentar/update.php ===
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include_once '../conf/db_config.php';
include_once '../model/Komentar.php';

$db = new Database();
$conn = $db->connect();

$komentar = new Komentar($conn);
$data = json_decode(file_get_contents("php://input"));

// Validasi input
if (
    !isset($data->id) || 
    !isset($data->user_id) || 
    !isset($data->isi_komentar) || 
    empty(trim($data->isi_komentar))
) {
    http_response_code(400); // Bad Request
    echo json_encode([
        "success" => false,
        "message" => "Data tidak lengkap. Pastikan id, user_id, dan isi_komentar diisi."
    ]);
    exit;
}

// Set properti
$komentar->id = $data->id;
$komentar->user_id = $data->user_id;
$komentar->isi_komentar = trim($data->isi_komentar);

// Eksekusi update
if ($komentar->update()) {
    http_response_code(200); // OK
    echo json_encode([
        "success" => true,
        "message" => "Komentar berhasil diperbarui."
    ]);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        "success" => false,
        "message" => "Gagal memperbarui komentar. Periksa kembali ID dan kepemilikan komentar."
    ]);
}

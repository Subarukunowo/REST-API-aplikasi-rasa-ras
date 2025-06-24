<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include_once "../conf/db_config.php";
include_once "../model/Profil.php";

$database = new Database();
$db = $database->connect();

$data = json_decode(file_get_contents("php://input"));

// Validasi apakah semua data wajib tersedia
if (
    empty($data->user_id) ||
    empty($data->nama_lengkap) ||
    empty($data->foto) ||
    empty($data->bio)
) {
    http_response_code(400); // Bad Request
    echo json_encode([
        "success" => false,
        "message" => "Semua field (user_id, nama_lengkap, foto, bio) wajib diisi."
    ]);
    exit();
}

$stmt = $db->prepare("INSERT INTO profil (user_id, nama_lengkap, foto, bio) VALUES (?, ?, ?, ?)");
$result = $stmt->execute([
    $data->user_id,
    $data->nama_lengkap,
    $data->foto,
    $data->bio
]);

echo json_encode([
    "success" => $result,
    "message" => $result ? "Data profil berhasil ditambahkan." : "Gagal menambahkan data profil."
]);

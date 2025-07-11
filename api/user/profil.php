<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once "../conf/db_config.php";
include_once "../model/Profil.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['user_id'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Parameter user_id wajib diisi."]);
    exit;
}

$database = new Database();
$db = $database->connect();

$profil = new Profil($db);

// Ambil data profil berdasarkan user_id
$existing = $profil->getByUserId($data['user_id']);
if (!$existing) {
    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Profil tidak ditemukan untuk user_id tersebut."]);
    exit;
}

// Set data baru
$profil->id = $existing['id'];
$profil->user_id = $data['user_id'];
$profil->nama_lengkap = $data['nama_lengkap'] ?? $existing['nama_lengkap'];
$profil->bio = $data['bio'] ?? $existing['bio'];
$profil->foto = $data['foto'] ?? $existing['foto'];

// Jika ada base64 foto, simpan ke file
if (!empty($data['foto_base64']) && !empty($profil->foto)) {
    $decoded = base64_decode($data['foto_base64']);
    file_put_contents("../images/" . $profil->foto, $decoded);
}

// Jalankan update
$updated = $profil->update();

if ($updated) {
    echo json_encode([
        "success" => true,
        "message" => "Profil berhasil diperbarui.",
        "nama_lengkap" => $profil->nama_lengkap,
        "bio" => $profil->bio,
        "foto" => $profil->foto
    ]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Gagal memperbarui profil."]);
}

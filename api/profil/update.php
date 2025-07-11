<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include_once "../conf/db_config.php";
include_once "../model/Profil.php";
include_once "../model/Users.php";

$database = new Database();
$db = $database->connect();

// Ambil data JSON
$data = json_decode(file_get_contents("php://input"));

// Validasi field utama profil
if (
    empty($data->id) ||
    empty($data->user_id) ||
    empty($data->nama_lengkap) ||
    !isset($data->foto) ||
    !isset($data->bio)
) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Field id, user_id, nama_lengkap, foto, dan bio wajib diisi."
    ]);
    exit();
}

// ==================== UPDATE PROFIL ====================
$profil = new Profil($db);
$stmt = $db->prepare("UPDATE profil SET user_id=?, nama_lengkap=?, foto=?, bio=? WHERE id=?");
$resultProfil = $stmt->execute([
    $data->user_id,
    $data->nama_lengkap,
    $data->foto,
    $data->bio,
    $data->id
]);

// ==================== UPDATE USER ====================
$user = new Users($db);
$user->id = $data->user_id;
$user->username = isset($data->username) ? $data->username : null;
$user->email = isset($data->email) ? $data->email : null;
$user->role = isset($data->role) ? $data->role : 'pengguna'; // default
$user->is_blocked = isset($data->is_blocked) ? $data->is_blocked : 0;
$user->password = isset($data->password) && trim($data->password) !== '' ? $data->password : null;

$resultUser = $user->update(); // ini akan otomatis skip update password jika null

// ==================== RESPON ====================
echo json_encode([
    "success" => $resultProfil && $resultUser,
    "message" => $resultProfil && $resultUser ? "Berhasil memperbarui profil dan data pengguna." : "Gagal memperbarui data.",
]);

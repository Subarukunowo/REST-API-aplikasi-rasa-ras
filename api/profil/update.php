<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once "../conf/db_config.php";
require_once "../model/Profil.php";
require_once "../model/Users.php";

$database = new Database();
$db = $database->connect();

// Ambil dan decode input JSON
$inputRaw = file_get_contents("php://input");
$data = json_decode($inputRaw, true);

// Validasi wajib
if (empty($data['id']) || empty($data['user_id'])) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Field 'id' (profil) dan 'user_id' wajib diisi."
    ]);
    exit();
}

// ===================== SIMPAN GAMBAR (jika ada) =====================
if (!empty($data['foto_base64']) && !empty($data['foto'])) {
    $fileName = basename($data['foto']);
    $fileData = base64_decode($data['foto_base64']);
    $targetDir = "../images/";

    // Buat folder jika belum ada
    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0755, true)) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Folder tujuan gambar tidak tersedia dan gagal dibuat."
            ]);
            exit();
        }
    }

    $filePath = $targetDir . $fileName;

    if (file_put_contents($filePath, $fileData) === false) {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Gagal menyimpan file gambar ke server."
        ]);
        exit();
    }

    // ✅ Pastikan disiapkan untuk update ke database
    $data['foto'] = $fileName;
}

// ===================== UPDATE PROFIL =====================
$profilFields = [];
$profilParams = [];

if (!empty($data['nama_lengkap'])) {
    $profilFields[] = "nama_lengkap=?";
    $profilParams[] = $data['nama_lengkap'];
}
if (!empty($data['bio'])) {
    $profilFields[] = "bio=?";
    $profilParams[] = $data['bio'];
}
if (!empty($data['foto'])) {
    $profilFields[] = "foto=?";
    $profilParams[] = $data['foto'];
}

// ❌ PERBAIKI bagian WHERE
$profilParams[] = $data['id'];
$profilParams[] = $data['user_id'];

$profilQuery = "UPDATE profil SET " . implode(", ", $profilFields) . " WHERE id=? AND user_id=?";
$profilStmt = $db->prepare($profilQuery);
$resultProfil = $profilStmt->execute($profilParams);

// ===================== UPDATE USERS =====================
$userFields = [];
$userParams = [];

if (!empty($data['username'])) {
    $userFields[] = "username=?";
    $userParams[] = $data['username'];
}
if (!empty($data['email'])) {
    $userFields[] = "email=?";
    $userParams[] = $data['email'];
}
if (isset($data['is_blocked'])) {
    $userFields[] = "is_blocked=?";
    $userParams[] = $data['is_blocked'];
}

if (!empty($userFields)) {
    $userParams[] = $data['user_id'];
    $userQuery = "UPDATE users SET " . implode(", ", $userFields) . " WHERE id=?";
    $userStmt = $db->prepare($userQuery);
    $resultUser = $userStmt->execute($userParams);
} else {
    $resultUser = true;
}

// ===================== RESPON =====================
echo json_encode([
    "success" => $resultProfil && $resultUser,
    "message" => ($resultProfil && $resultUser)
        ? "Berhasil memperbarui data profil dan pengguna."
        : "Gagal memperbarui data."
]);

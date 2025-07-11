<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Content-Type: multipart/form-data");
include_once "../conf/db_config.php";
include_once "../model/Profil.php";
include_once "../model/Users.php";

$database = new Database();
$db = $database->connect();

// Validasi ID yang wajib
if (
    empty($_POST['id']) ||
    empty($_POST['user_id'])
) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Field 'id' (profil) dan 'user_id' wajib diisi."
    ]);
    exit();
}

// ==================== HANDLE FILE UPLOAD FOTO ====================
$uploadedFileName = null;
$targetDir = "../images/";

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['foto']['tmp_name'];
    $fileName = basename($_FILES['foto']['name']);
    $targetFilePath = $targetDir . $fileName;

    if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
        $uploadedFileName = $fileName;
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Gagal mengunggah gambar."
        ]);
        exit();
    }
} else {
    $uploadedFileName = isset($_POST['foto']) ? $_POST['foto'] : null;
}

// ==================== UPDATE PROFIL ====================
$profilFields = [];
$profilParams = [];

if (isset($_POST['nama_lengkap'])) {
    $profilFields[] = "nama_lengkap=?";
    $profilParams[] = $_POST['nama_lengkap'];
}
if (isset($_POST['bio'])) {
    $profilFields[] = "bio=?";
    $profilParams[] = $_POST['bio'];
}
if ($uploadedFileName !== null) {
    $profilFields[] = "foto=?";
    $profilParams[] = $uploadedFileName;
}

// Tambahkan user_id dan id (untuk WHERE clause)
$profilFields[] = "user_id=?";
$profilParams[] = $_POST['user_id'];
$profilParams[] = $_POST['id'];

$query = "UPDATE profil SET " . implode(", ", $profilFields) . " WHERE id=?";
$stmt = $db->prepare($query);
$resultProfil = $stmt->execute($profilParams);

// ==================== UPDATE USER (hanya username, email, is_blocked) ====================
$userFields = [];
$userParams = [];

if (isset($_POST['username'])) {
    $userFields[] = "username=?";
    $userParams[] = $_POST['username'];
}
if (isset($_POST['email'])) {
    $userFields[] = "email=?";
    $userParams[] = $_POST['email'];
}
if (isset($_POST['is_blocked'])) {
    $userFields[] = "is_blocked=?";
    $userParams[] = $_POST['is_blocked'];
}

if (!empty($userFields)) {
    $userParams[] = $_POST['user_id'];
    $queryUser = "UPDATE users SET " . implode(", ", $userFields) . " WHERE id=?";
    $stmtUser = $db->prepare($queryUser);
    $resultUser = $stmtUser->execute($userParams);
} else {
    $resultUser = true;
}

// ==================== RESPON ====================
echo json_encode([
    "success" => $resultProfil && $resultUser,
    "message" => $resultProfil && $resultUser
        ? "Berhasil memperbarui data profil dan pengguna."
        : "Gagal memperbarui data.",
]);

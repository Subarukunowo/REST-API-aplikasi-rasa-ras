<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include_once '../conf/db_config.php';
include_once "../model/Kategori.php";

$database = new Database();
$db = $database->connect();

$data = json_decode(file_get_contents("php://input"));

// Validasi input
if (empty($data->id) || empty($data->nama)) {
    http_response_code(400); // Bad Request
    echo json_encode([
        "success" => false,
        "message" => "Field 'id' dan 'nama' wajib diisi."
    ]);
    exit();
}

try {
    $stmt = $db->prepare("UPDATE kategori SET nama = ? WHERE id = ?");
    $stmt->execute([$data->nama, $data->id]);

    // Cek apakah data benar-benar ter-update
    if ($stmt->rowCount() > 0) {
        http_response_code(200); // OK
        echo json_encode([
            "success" => true,
            "message" => "Kategori berhasil diperbarui."
        ]);
    } else {
        http_response_code(404); // Not Found / Tidak ada perubahan
        echo json_encode([
            "success" => false,
            "message" => "Kategori tidak ditemukan atau tidak ada perubahan."
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        "success" => false,
        "message" => "Terjadi kesalahan: " . $e->getMessage()
    ]);
}

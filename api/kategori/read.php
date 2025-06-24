<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include_once '../conf/db_config.php';
include_once "../model/Kategori.php";

$database = new Database();
$db = $database->connect();

if (isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM kategori WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        echo json_encode([
            "success" => true,
            "message" => "Data kategori ditemukan.",
            "data" => $data
        ]);
    } else {
        http_response_code(404); // Not Found
        echo json_encode([
            "success" => false,
            "message" => "Kategori tidak ditemukan.",
            "data" => null
        ]);
    }
} else {
    $stmt = $db->query("SELECT * FROM kategori");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($data) {
        echo json_encode([
            "success" => true,
            "message" => "Data semua kategori berhasil diambil.",
            "data" => $data
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Tidak ada data kategori ditemukan.",
            "data" => []
        ]);
    }
}

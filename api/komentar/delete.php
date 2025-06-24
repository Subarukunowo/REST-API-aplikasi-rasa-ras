<?php
header("Content-Type: application/json");

include_once '../conf/db_config.php';
include_once '../model/Komentar.php';

$db = new Database();
$conn = $db->connect();

$komentar = new Komentar($conn);
$data = json_decode(file_get_contents("php://input"));

if (isset($data->id)) {
    $komentar->id = $data->id;

    if ($komentar->delete()) {
        http_response_code(200);
        echo json_encode(["success" => true, "message" => "Komentar berhasil dihapus"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Gagal menghapus komentar"]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "ID komentar tidak dikirim"]);
}

<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include_once "../conf/db_config.php";
include_once "../model/Profil.php";

$database = new Database();
$db = $database->connect();

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->id)) {
    $stmt = $db->prepare("DELETE FROM profil WHERE id = ?");
    $stmt->execute([$data->id]);

    if ($stmt->rowCount() > 0) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Data berhasil dihapus."
        ]);
    } else {
        http_response_code(404); // ❗ Data tidak ditemukan
        echo json_encode([
            "success" => false,
            "message" => "Data gagal dihapus atau ID tidak ditemukan."
        ]);
    }
} else {
    http_response_code(400); // ❗ Bad Request
    echo json_encode([
        "success" => false,
        "message" => "ID tidak ditemukan dalam permintaan."
    ]);
}

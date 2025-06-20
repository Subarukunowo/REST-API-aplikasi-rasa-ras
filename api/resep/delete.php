<?php
include_once '../config/db_config.php';
include_once '../model/Resep.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

 $database = new Database();
    $db = $database->connect();

$resep = new Resep($db);

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->id) && !empty($data->user_id)) {
    $resep->id = $data->id;
    $resep->user_id = $data->user_id;

    if ($resep->delete()) {
        echo json_encode(["message" => "Resep berhasil dihapus."]);
    } else {
        http_response_code(503);
        echo json_encode(["message" => "Gagal menghapus resep."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Data tidak lengkap."]);
}

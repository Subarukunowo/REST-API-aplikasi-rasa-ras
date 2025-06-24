<?php
require_once '../conf/db_config.php';
require_once '../model/Notifikasi.php';

$database = new Database();
$db = $database->connect();
$model = new Notifikasi($db);

$data = json_decode(file_get_contents("php://input"));

if (isset($data->id) && $model->delete($data->id)) {
    echo json_encode(["message" => "Notifikasi berhasil dihapus"]);
} else {
    http_response_code(400);
    echo json_encode(["message" => "Gagal menghapus notifikasi"]);
}

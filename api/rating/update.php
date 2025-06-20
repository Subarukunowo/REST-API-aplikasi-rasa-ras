<?php
require_once '../conf/db_config.php';
require_once '../../model/Rating.php';

$data = json_decode(file_get_contents("php://input"));

$database = new Database();
$db = $database->connect();
$model = new Rating($conn);

if ($model->update($data->id, $data->nilai)) {
    echo json_encode(["message" => "Rating berhasil diupdate"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Gagal mengupdate rating"]);
}

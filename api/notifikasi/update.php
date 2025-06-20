<?php
require_once '../conf/db_config.php';
require_once '../../model/Notifikasi.php';

$database = new Database();
$db = $database->connect();
$model = new Notifikasi($db);

$data = json_decode(file_get_contents("php://input"));

if (
    isset($data->id) &&
    $model->update($data->id, $data->is_read)
) {
    echo json_encode(["message" => "Notifikasi berhasil diperbarui"]);
} else {
    http_response_code(400);
    echo json_encode(["message" => "Gagal memperbarui notifikasi"]);
}

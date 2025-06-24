<?php
require_once '../conf/db_config.php';
require_once '../model/Notifikasi.php';

$database = new Database();
$db = $database->connect();
$model = new Notifikasi($db);

$data = json_decode(file_get_contents("php://input"));

if ($model->create($data)) {
    echo json_encode(["message" => "Notifikasi berhasil ditambahkan"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Gagal menambahkan notifikasi"]);
}

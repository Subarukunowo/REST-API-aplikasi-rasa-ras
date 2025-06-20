<?php
// === FILE: api/komentar/create.php ===
include_once '../config/db_config.php';
include_once '../../model/Komentar.php';

$db = new Database();
$conn = $db->connect();

$komentar = new Komentar($conn);
$data = json_decode(file_get_contents("php://input"));

$komentar->recipe_id = $data->recipe_id;
$komentar->user_id = $data->user_id;
$komentar->isi_komentar = $data->isi_komentar;

if ($komentar->create()) {
    echo json_encode(["message" => "Komentar berhasil ditambahkan"]);
} else {
    echo json_encode(["message" => "Gagal menambahkan komentar"]);
}

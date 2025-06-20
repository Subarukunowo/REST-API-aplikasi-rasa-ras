<?php
// === FILE: api/komentar/update.php ===
include_once '../config/db_config.php';
include_once '../../model/Komentar.php';

$db = new Database();
$conn = $db->connect();

$komentar = new Komentar($conn);
$data = json_decode(file_get_contents("php://input"));

$komentar->id = $data->id;
$komentar->user_id = $data->user_id;
$komentar->isi_komentar = $data->isi_komentar;

if ($komentar->update()) {
    echo json_encode(["message" => "Komentar berhasil diperbarui"]);
} else {
    echo json_encode(["message" => "Gagal memperbarui komentar"]);
}

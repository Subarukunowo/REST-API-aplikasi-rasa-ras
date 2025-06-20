<?php
include_once '../config/db_config.php';
include_once '../../model/Komentar.php';

$db = new Database();
$conn = $db->connect();

$komentar = new Komentar($conn);
$data = json_decode(file_get_contents("php://input"));

$komentar->id = $data->id;
$komentar->user_id = $data->user_id;

if ($komentar->delete()) {
    echo json_encode(["message" => "Komentar berhasil dihapus"]);
} else {
    echo json_encode(["message" => "Gagal menghapus komentar"]);
}

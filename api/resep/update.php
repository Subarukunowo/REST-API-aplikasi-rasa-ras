<?php
include_once "../conf/db_config.php";
include_once "../model/Profil.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$database = new Database();
$db = $database->connect();

$resep = new Resep($db);

$data = json_decode(file_get_contents("php://input"));

// Pastikan data yang dibutuhkan tersedia
if (!empty($data->id) && !empty($data->user_id)) {
    $resep->id = $data->id;
    $resep->user_id = $data->user_id;
    $resep->nama_masakan = $data->nama_masakan;
    $resep->kategori_id = $data->kategori_id;
    $resep->waktu_memasak = $data->waktu_memasak;
    $resep->bahan_utama = $data->bahan_utama;
    $resep->deskripsi = $data->deskripsi;
    $resep->level_kesulitan = $data->level_kesulitan;
    $resep->waktu_id = $data->waktu_id;
    $resep->hidangan_id = $data->hidangan_id;
    $resep->video = $data->video;

    if ($resep->update()) {
        echo json_encode(["message" => "Resep berhasil diperbarui."]);
    } else {
        http_response_code(503);
        echo json_encode(["message" => "Gagal memperbarui resep."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Data tidak lengkap."]);
}

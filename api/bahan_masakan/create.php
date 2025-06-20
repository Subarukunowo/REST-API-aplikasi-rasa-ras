<?php
include_once '../config/db_config.php';
include_once '../../model/BahanMasakan.php';

$db = new Database();
$conn = $db->connect();

$bahan = new BahanMasakan($conn);
$data = json_decode(file_get_contents("php://input"));

$bahan->recipe_id = $data->recipe_id;
$bahan->nama_bahan = $data->nama_bahan;
$bahan->jumlah = $data->jumlah;

if ($bahan->create()) {
    echo json_encode(["message" => "Bahan masakan berhasil ditambahkan"]);
} else {
    echo json_encode(["message" => "Gagal menambahkan bahan masakan"]);
}


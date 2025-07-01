<?php
// api/langkah_resep/read_all.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../conf/db_config.php';
include_once '../model/LangkahResep.php';

$database = new Database();
$db = $database->connect();
$langkah_resep = new LangkahResep($db);

// Cek apakah resep_id diberikan untuk filter
if (isset($_GET['resep_id'])) {
    $langkah_resep->resep_id = $_GET['resep_id'];
    $stmt = $langkah_resep->readByResepId();
} else {
    $stmt = $langkah_resep->readAll();
}

$num = $stmt->rowCount();

if ($num > 0) {
    $data_arr = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $data_arr[] = [
            "id" => $id,
            "resep_id" => $resep_id,
            "nama_masakan" => isset($nama_masakan) ? $nama_masakan : null,
            "urutan" => $urutan,
            "judul" => $judul,
            "deskripsi" => $deskripsi,
            "created_at" => $created_at,
            "updated_at" => $updated_at
        ];
    }

    echo json_encode([
        "success" => true,
        "data" => $data_arr
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Tidak ada data langkah resep ditemukan."
    ]);
}
?>

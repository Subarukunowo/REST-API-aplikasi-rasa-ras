<?php
// api/langkah_resep/read.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../conf/db_config.php';
include_once '../model/LangkahResep.php';

$database = new Database();
$db = $database->connect();
$langkah_resep = new LangkahResep($db);

// Ambil ID dari query parameter
$langkah_resep->id = isset($_GET['id']) ? $_GET['id'] : die(json_encode([
    "success" => false,
    "message" => "ID tidak ditemukan."
]));

if ($langkah_resep->readOne()) {
    echo json_encode([
        "success" => true,
        "data" => [
            "id" => $langkah_resep->id,
            "resep_id" => $langkah_resep->resep_id,
            "urutan" => $langkah_resep->urutan,
            "judul" => $langkah_resep->judul,
            "deskripsi" => $langkah_resep->deskripsi,
            "created_at" => $langkah_resep->created_at,
            "updated_at" => $langkah_resep->updated_at
        ]
    ]);
} else {
    http_response_code(404);
    echo json_encode([
        "success" => false,
        "message" => "Langkah resep tidak ditemukan."
    ]);
}
?>

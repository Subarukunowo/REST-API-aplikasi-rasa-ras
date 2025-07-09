<?php
// api/langkah_resep/read.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../conf/db_config.php';
include_once '../model/LangkahResep.php';

$database = new Database();
$db = $database->connect();
$langkah_resep = new LangkahResep($db);

// Ambil berdasarkan ID langkah (tunggal)
if (isset($_GET['id'])) {
    $langkah_resep->id = intval($_GET['id']);

    if ($langkah_resep->readOne()) {
        echo json_encode([
            "success" => true,
            "data" => [
                "id" => $langkah_resep->id,
                "resep_id" => $langkah_resep->resep_id,
                "urutan" => $langkah_resep->urutan,
                "judul" => $langkah_resep->judul,
                "deskripsi" => $langkah_resep->deskripsi,
              
            ]
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Langkah resep tidak ditemukan."
        ]);
    }

// Ambil semua langkah berdasarkan resep_id
} elseif (isset($_GET['resep_id'])) {
    $langkah_resep->resep_id = intval($_GET['resep_id']);
    $stmt = $langkah_resep->readByResepId();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $result = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }

        echo json_encode(["success" => true, "data" => $result]);
    } else {
        echo json_encode(["success" => false, "message" => "Langkah resep tidak ditemukan."]);
    }

// Ambil semua jika tidak ada query parameter
} else {
    $stmt = $langkah_resep->readAll();
    $result = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $result[] = $row;
    }

    echo json_encode(["success" => true, "data" => $result]);
}

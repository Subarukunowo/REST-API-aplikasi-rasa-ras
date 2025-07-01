<?php
// api/langkah_resep/create.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../conf/db_config.php';
include_once '../model/LangkahResep.php';

$database = new Database();
$db = $database->connect();
$langkah_resep = new LangkahResep($db);

// Ambil data dari request body (as associative array)
$data = json_decode(file_get_contents("php://input"), true);

$inserted = [];
$failed = [];

// Fungsi validasi data
function is_valid_item($item) {
    return isset($item['resep_id']) && isset($item['judul']) && isset($item['deskripsi']);
}

// Jika data berupa array (bulk insert)
if (is_array($data)) {
    foreach ($data as $item) {
        if (is_valid_item($item)) {
            $langkah_resep->resep_id = $item['resep_id'];
            $langkah_resep->judul = $item['judul'];
            $langkah_resep->deskripsi = $item['deskripsi'];
            $langkah_resep->urutan = $langkah_resep->getNextUrutan();

            if ($langkah_resep->create()) {
                $inserted[] = [
                    "judul" => $item['judul'],
                    "id" => $langkah_resep->id
                ];
            } else {
                $failed[] = [
                    "judul" => $item['judul'],
                    "reason" => "Gagal menyimpan ke database"
                ];
            }
        } else {
            $failed[] = [
                "data" => $item,
                "reason" => "Data tidak lengkap"
            ];
        }
    }

    http_response_code(count($inserted) > 0 ? 201 : 400);
    echo json_encode([
        "success" => count($inserted) > 0,
        "message" => count($inserted) > 0 ? "Beberapa langkah berhasil ditambahkan." : "Tidak ada langkah yang berhasil ditambahkan.",
        "inserted" => $inserted,
        "failed" => $failed
    ]);
}

// Jika data berupa satu objek (single insert)
elseif (is_valid_item($data)) {
    $langkah_resep->resep_id = $data['resep_id'];
    $langkah_resep->judul = $data['judul'];
    $langkah_resep->deskripsi = $data['deskripsi'];
    $langkah_resep->urutan = $langkah_resep->getNextUrutan();

    if ($langkah_resep->create()) {
        http_response_code(201);
        echo json_encode([
            "success" => true,
            "message" => "Langkah resep berhasil ditambahkan.",
            "id" => $langkah_resep->id
        ]);
    } else {
        http_response_code(503);
        echo json_encode([
            "success" => false,
            "message" => "Gagal menambahkan langkah resep."
        ]);
    }
}

// Format tidak valid
else {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Format data tidak valid atau data tidak lengkap.",
        "required_fields" => ["resep_id", "judul", "deskripsi"]
    ]);
}
?>

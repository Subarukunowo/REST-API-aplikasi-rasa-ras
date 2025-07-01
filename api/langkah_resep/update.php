<?php
// api/langkah_resep/update.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../conf/db_config.php';
include_once '../model/LangkahResep.php';

$database = new Database();
$db = $database->connect();
$langkah_resep = new LangkahResep($db);

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Make sure data is not empty
if (
    !empty($data->id) &&
    !empty($data->urutan) &&
    !empty($data->judul) &&
    !empty($data->deskripsi)
) {
    // Set langkah resep property values
    $langkah_resep->id = $data->id;
    $langkah_resep->urutan = $data->urutan;
    $langkah_resep->judul = $data->judul;
    $langkah_resep->deskripsi = $data->deskripsi;

    // Update the langkah resep
    if ($langkah_resep->update()) {
        // Set response code - 200 ok
        http_response_code(200);

        // Tell the user
        echo json_encode(array(
            "success" => true,
            "message" => "Langkah resep berhasil diupdate."
        ));
    } else {
        // Set response code - 503 service unavailable
        http_response_code(503);

        // Tell the user
        echo json_encode(array(
            "success" => false,
            "message" => "Gagal mengupdate langkah resep."
        ));
    }
} else {
    // Set response code - 400 bad request
    http_response_code(400);

    // Tell the user
    echo json_encode(array(
        "success" => false,
        "message" => "Gagal mengupdate langkah resep. Data tidak lengkap.",
        "required_fields" => array("id", "urutan", "judul", "deskripsi")
    ));
}

// api/langkah_resep/reorder.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../conf/db_config.php';
include_once '../model/LangkahResep.php';

$database = new Database();
$db = $database->connect();
$langkah_resep = new LangkahResep($db);

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Make sure data is not empty
if (!empty($data->langkah_ids) && is_array($data->langkah_ids)) {
    // Reorder langkah resep
    if ($langkah_resep->reorder($data->langkah_ids)) {
        // Set response code - 200 ok
        http_response_code(200);

        // Tell the user
        echo json_encode(array(
            "success" => true,
            "message" => "Urutan langkah resep berhasil diupdate."
        ));
    } else {
        // Set response code - 503 service unavailable
        http_response_code(503);

        // Tell the user
        echo json_encode(array(
            "success" => false,
            "message" => "Gagal mengupdate urutan langkah resep."
        ));
    }
} else {
    // Set response code - 400 bad request
    http_response_code(400);

    // Tell the user
    echo json_encode(array(
        "success" => false,
        "message" => "Gagal mengupdate urutan. Data langkah_ids tidak valid."
    ));
}
?>
?>
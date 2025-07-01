
<?php
// api/langkah_resep/delete.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
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
if (!empty($data->id)) {
    // Set langkah resep id to delete
    $langkah_resep->id = $data->id;

    // Delete the langkah resep
    if ($langkah_resep->delete()) {
        // Set response code - 200 ok
        http_response_code(200);

        // Tell the user
        echo json_encode(array(
            "success" => true,
            "message" => "Langkah resep berhasil dihapus."
        ));
    } else {
        // Set response code - 503 service unavailable
        http_response_code(503);

        // Tell the user
        echo json_encode(array(
            "success" => false,
            "message" => "Gagal menghapus langkah resep."
        ));
    }
} else {
    // Set response code - 400 bad request
    http_response_code(400);

    // Tell the user
    echo json_encode(array(
        "success" => false,
        "message" => "Gagal menghapus langkah resep. ID tidak ditemukan."
    ));
}
?>


<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../conf/db_config.php';
include_once '../model/Resep.php';
include_once '../model/LangkahResep.php';

$database = new Database();
$db = $database->connect();

$resep = new Resep($db);
$langkahResep = new LangkahResep($db);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "ID resep tidak valid"
    ]);
    exit;
}

// Get resep
$dataResep = $resep->getById($id);

if (!$dataResep) {
    echo json_encode([
        "success" => false,
        "message" => "Resep tidak ditemukan"
    ]);
    exit;
}

// Get langkah resep
$langkahResep->resep_id = $id;
$stmtLangkah = $langkahResep->readByResepId();
$langkahArr = [];

while ($row = $stmtLangkah->fetch(PDO::FETCH_ASSOC)) {
    extract($row);
    $langkahItem = [
        "id" => $id,
        "resep_id" => $resep_id,
        "urutan" => $urutan,
        "judul" => $judul,
        "deskripsi" => $deskripsi
    ];
    $langkahArr[] = $langkahItem;
}

echo json_encode([
    "success" => true,
    "data" => $dataResep,
    "langkah" => $langkahArr
]);
?>

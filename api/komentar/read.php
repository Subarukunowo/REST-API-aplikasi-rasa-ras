<?php
// === FILE: api/komentar/get.php ===
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include_once '../conf/db_config.php';
include_once '../model/Komentar.php';

$db = new Database();
$conn = $db->connect();
$komentar = new Komentar($conn);

if (isset($_GET['id'])) {
    // Ambil komentar berdasarkan ID
    $stmt = $komentar->getById($_GET['id']);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        echo json_encode([
            "success" => true,
            "message" => "Komentar ditemukan.",
            "data" => $data
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Komentar tidak ditemukan."
        ]);
    }
} elseif (isset($_GET['recipe_id'])) {
    // Ambil semua komentar berdasarkan resep
    $stmt = $komentar->getByRecipe($_GET['recipe_id']);
    $data = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }

    echo json_encode([
        "success" => true,
        "message" => "Daftar komentar berhasil diambil.",
        "data" => $data
    ]);
} else {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Parameter recipe_id atau id diperlukan."
    ]);
}

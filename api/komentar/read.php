<?php
// === FILE: api/komentar/get.php ===
include_once '../config/db_config.php';
include_once '../../model/Komentar.php';

$db = new Database();
$conn = $db->connect();

$komentar = new Komentar($conn);
$recipe_id = isset($_GET['recipe_id']) ? $_GET['recipe_id'] : die(json_encode(["message" => "Recipe ID diperlukan"]));

$stmt = $komentar->getByRecipe($recipe_id);
$data = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[] = $row;
}

echo json_encode($data);
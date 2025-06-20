<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include_once "../conf/db_config.php";
include_once "../model/Profil.php";

$database = new Database();
$db = $database->connect();

if (isset($_GET['id'])) {
    $stmt = $db->prepare("DELETE FROM profil WHERE id = ?");
    $result = $stmt->execute([$_GET['id']]);
    echo json_encode(["success" => $result]);
} else {
    echo json_encode(["error" => "ID tidak ditemukan."]);
}

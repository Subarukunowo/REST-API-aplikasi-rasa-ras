<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include_once "../conf/db_config.php";
include_once "../model/Profil.php";

$database = new Database();
$db = $database->connect();

if (isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM profil WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $stmt = $db->query("SELECT * FROM profil");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($data);

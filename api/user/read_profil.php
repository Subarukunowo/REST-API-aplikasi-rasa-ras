<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include_once "../conf/db_config.php";
include_once "../model/Profil.php";

if (!isset($_GET['user_id'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Parameter user_id wajib diisi."]);
    exit;
}

$database = new Database();
$db = $database->connect();

$profil = new Profil($db);
$data = $profil->getByUserId($_GET['user_id']);

if ($data) {
    echo json_encode($data);
} else {
    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Profil tidak ditemukan."]);
}

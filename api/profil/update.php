<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include_once "../conf/db_config.php";
include_once "../model/Profil.php";

$database = new Database();
$db = $database->connect();

$data = json_decode(file_get_contents("php://input"));
$stmt = $db->prepare("UPDATE profil SET user_id=?, nama_lengkap=?, foto=?, bio=? WHERE id=?");
$result = $stmt->execute([
    $data->user_id,
    $data->nama_lengkap,
    $data->foto,
    $data->bio,
    $data->id
]);

echo json_encode(["success" => $result]);

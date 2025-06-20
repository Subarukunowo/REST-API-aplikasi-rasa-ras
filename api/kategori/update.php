<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include_once '../conf/db_config.php';
include_once "../model/Kategori.php";
$database = new Database();
$db = $database->connect();

$data = json_decode(file_get_contents("php://input"));
$stmt = $db->prepare("UPDATE kategori SET nama = ? WHERE id = ?");
$result = $stmt->execute([$data->nama, $data->id]);

echo json_encode(["success" => $result]);

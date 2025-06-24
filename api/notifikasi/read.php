<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once '../conf/db_config.php';
require_once '../model/Notifikasi.php';

$database = new Database();
$db = $database->connect();
$model = new Notifikasi($db);

if (isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM notifikasi WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $stmt = $db->query("SELECT * FROM notifikasi");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($data);

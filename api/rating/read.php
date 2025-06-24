<?php
require_once '../conf/db_config.php';
require_once '../model/Rating.php';

$database = new Database();
$db = $database->connect();
$model = new Rating($conn);

if (isset($_GET['id'])) {
    $result = $model->getById($_GET['id']);
} else {
    $result = $model->getAll();
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);

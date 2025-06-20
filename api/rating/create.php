<?php
require_once '../conf/db_config.php';
require_once '../../model/Rating.php';


$database = new Database();
$db = $database->connect();
$model = new Rating($conn);

$ratingData = [
    'recipe_id' => $data->recipe_id,
    'user_id' => $data->user_id,
    'nilai' => $data->nilai
];

if ($model->create($ratingData)) {
    echo json_encode(["message" => "Rating berhasil ditambahkan"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Gagal menambahkan rating"]);
}

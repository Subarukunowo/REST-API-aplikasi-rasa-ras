<?php
header("Content-Type: application/json");
include_once "../conf/db_config.php";
include_once "../model/Users.php";

$db = (new Database())->connect();
$user = new Users($db);

$data = json_decode(file_get_contents("php://input"));
$user->id = $data->id;

if ($user->delete()) {
    echo json_encode(["message" => "User deleted."]);
} else {
    echo json_encode(["message" => "User deletion failed."]);
}

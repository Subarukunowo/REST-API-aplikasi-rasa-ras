<?php
header("Content-Type: application/json");
include_once "../conf/db_config.php";
include_once "../model/Users.php";

$db = (new Database())->connect();
$user = new Users($db);

$data = json_decode(file_get_contents("php://input"));

$user->username = $data->username;
$user->password = $data->password;
$user->email = $data->email;
$user->role = $data->role;
$user->is_blocked = $data->is_blocked;

if ($user->create()) {
    echo json_encode(["message" => "User created."]);
} else {
    echo json_encode(["message" => "User creation failed."]);
}

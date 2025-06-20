<?php
header("Content-Type: application/json");
include_once "../conf/db_config.php";
include_once "../model/Users.php";

$db = (new Database())->connect();
$user = new Users($db);

$result = $user->readAll();
$users = [];

while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode($users);

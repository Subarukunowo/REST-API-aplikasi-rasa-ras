<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once '../conf/db_conn.php';
require_once '../model/Users.php';

$db = new Database();
$conn = $db->connect();
$user = new Users($conn);

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            echo json_encode($user->getById($_GET['id']));
        } else {
            $stmt = $user->getAll();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($result);
        }
        break;

    case 'POST':
        $user->username = $input['username'];
        $user->password = $input['password'];
        $user->email = $input['email'];
        $user->role = $input['role'];
        $user->is_blocked = 0;
        echo json_encode(['success' => $user->create()]);
        break;

    case 'PUT':
        $user->id = $input['id'];
        $user->username = $input['username'];
        $user->password = $input['password'];
        $user->email = $input['email'];
        $user->role = $input['role'];
        $user->is_blocked = $input['is_blocked'] ?? 0;
        echo json_encode(['success' => $user->update()]);
        break;

    case 'DELETE':
        $user->id = $_GET['id'] ?? 0;
        echo json_encode(['success' => $user->delete()]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method not allowed']);
}

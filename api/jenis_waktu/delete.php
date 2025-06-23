<?php


header("Content-Type: application/json");
include_once '../conf/db_config.php';
include_once '../model/JenisWaktu.php';

try {
    $database = new Database();
    $db = $database->connect();
    $waktu = new JenisWaktu($db);

    $id = null;
    $method = $_SERVER['REQUEST_METHOD'];

    // Ambil id dari berbagai sumber (GET, POST, JSON body, atau multipart pada DELETE)
    if ($method === 'GET' && isset($_GET['id'])) {
        $id = $_GET['id'];
    } elseif ($method === 'POST' && isset($_POST['id'])) {
        $id = $_POST['id'];
    } else {
        $input = file_get_contents("php://input");
        $data = json_decode($input);
        if ($data && isset($data->id)) {
            $id = $data->id;
        } elseif (
            $method === 'DELETE' &&
            isset($_SERVER['CONTENT_TYPE']) &&
            stripos($_SERVER['CONTENT_TYPE'], 'multipart/form-data') !== false
        ) {
            // Ambil id dari multipart form-data pada DELETE
            if (preg_match('/name="id"\r\n\r\n(.*?)\r\n/s', $input, $matches)) {
                $id = trim($matches[1]);
            }
        }
    }

    if ($id) {
        $result = $waktu->delete($id);
        echo json_encode([
            "success" => $result,
            "message" => $result ? "Data berhasil dihapus." : "Data gagal dihapus."
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "ID tidak diberikan.",
            "debug" => [
                "method" => $method,
                "GET" => $_GET,
                "POST" => $_POST,
                "raw_input" => isset($input) ? $input : null
            ]
        ]);
    }
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
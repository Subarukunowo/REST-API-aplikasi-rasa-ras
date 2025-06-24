// Fixed create.php API
<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once '../conf/db_config.php';
require_once '../model/Notifikasi.php';

try {
    $database = new Database();
    $db = $database->connect();
    $model = new Notifikasi($db);

    $data = json_decode(file_get_contents("php://input"));
    
    // Validate input
    if (!$data) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Data JSON tidak valid"
        ]);
        exit;
    }

    if ($model->create($data)) {
        http_response_code(201);
        echo json_encode([
            "success" => true,
            "message" => "Notifikasi berhasil ditambahkan"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Gagal menambahkan notifikasi"
        ]);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}
?>


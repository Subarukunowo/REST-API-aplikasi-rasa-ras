<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once '../conf/db_config.php';
require_once '../model/Notifikasi.php';

$database = new Database();
$db = $database->connect();
$model = new Notifikasi($db);

try {
    if (isset($_GET['id'])) {
        $stmt = $db->prepare("SELECT * FROM notifikasi WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            echo json_encode([
                "success" => true,
                "message" => "Notifikasi berhasil ditemukan.",
                "data" => $data
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "message" => "Notifikasi dengan ID tersebut tidak ditemukan."
            ]);
        }
    } else {
        $stmt = $db->query("SELECT * FROM notifikasi");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            "success" => true,
            "message" => "Daftar notifikasi berhasil diambil.",
            "data" => $data
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        "success" => false,
        "message" => "Terjadi kesalahan saat mengambil data: " . $e->getMessage()
    ]);
}

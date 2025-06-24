<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Include database dan model
include_once '../conf/db_config.php';
include_once "../model/Kategori.php";

// Inisialisasi koneksi database
$database = new Database();
$db = $database->connect();

// Get ID from JSON body or GET parameter
$id = null;

// First try to get from JSON body
$input = json_decode(file_get_contents('php://input'), true);
if (isset($input['id'])) {
    $id = trim($input['id']);
} else {
    // Fallback to GET parameter
    $id = isset($_GET['id']) ? trim($_GET['id']) : null;
}

if (empty($id)) {
    http_response_code(400); // Bad Request
    echo json_encode([
        "success" => false,
        "message" => "ID kategori tidak diberikan."
    ]);
    exit;
}

try {
    // Create kategori object and use the model's delete method
    $kategori = new Kategori($db);
    $kategori->id = $id;
    
    // Check if kategori exists first
    $existing = $kategori->getById($id);
    if (!$existing) {
        http_response_code(404); // Not Found
        echo json_encode([
            "success" => false,
            "message" => "Kategori tidak ditemukan."
        ]);
        exit;
    }
    
    // Delete the kategori
    if ($kategori->delete()) {
        http_response_code(200); // OK
        echo json_encode([
            "success" => true,
            "message" => "Kategori berhasil dihapus."
        ]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode([
            "success" => false,
            "message" => "Gagal menghapus kategori."
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        "success" => false,
        "message" => "Terjadi kesalahan saat menghapus kategori.",
        "error" => $e->getMessage()
    ]);
}
?>
<?php
require_once '../conf/db_config.php';
require_once '../model/Resep.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Validasi metode
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use GET.'
    ]);
    exit;
}

// Validasi parameter
if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Parameter user_id tidak ditemukan atau tidak valid.'
    ]);
    exit;
}

$user_id = intval($_GET['user_id']);

try {
    $database = new Database();
    $conn = $database->connect();

    // Pastikan path gambar benar (gunakan domain dan port saat ini)
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $baseImageUrl = $protocol . '://' . $host . '/rasa-rasa/images/';

    // SQL query
    $sql = "SELECT r.*, u.username AS user_name
            FROM resep r
            JOIN users u ON r.user_id = u.id
            WHERE r.user_id = :user_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $recipes = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $recipes[] = [
            'id' => (int)$row['id'],
            'user_id' => (int)$row['user_id'],
            'nama_masakan' => $row['nama_masakan'],
            'kategori_id' => (int)$row['kategori_id'],
            'waktu_memasak' => (int)$row['waktu_memasak'],
            'bahan_utama' => $row['bahan_utama'],
            'deskripsi' => $row['deskripsi'],
            'created_at' => $row['created_at'],
            'level_kesulitan' => $row['level_kesulitan'],
            'jenis_waktu' => $row['jenis_waktu'],
            'video' => $row['video'],
            'gambar' => $row['gambar'],
            'gambar_url' => !empty($row['gambar']) ? $baseImageUrl . $row['gambar'] : null,
            'user_name' => $row['user_name'],
        ];
    }

    echo json_encode([
        'success' => true,
        'total' => count($recipes),
        'data' => $recipes
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

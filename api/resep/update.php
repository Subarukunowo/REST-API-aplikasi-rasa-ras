// ========== UPDATE RESEP (update_resep.php) ==========
<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
    exit();
}

include_once '../conf/db_config.php';
include_once '../model/Resep.php';

try {
    $database = new Database();
    $db = $database->connect();
    $resep = new Resep($db);
    
    $input = file_get_contents("php://input");
    $data = json_decode($input);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid JSON format"]);
        exit();
    }
    
    // Validation
    $required_fields = ['id', 'user_id', 'nama_masakan', 'kategori_id', 'waktu_memasak', 'bahan_utama', 'deskripsi', 'level_kesulitan'];
    foreach ($required_fields as $field) {
        if (empty($data->$field)) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Field $field harus diisi"]);
            exit();
        }
    }
    
    // Check if recipe exists
    $existing = $resep->getById($data->id);
    if (!$existing) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Resep tidak ditemukan"]);
        exit();
    }
    
    // Check ownership
    if ($existing['user_id'] != $data->user_id) {
        http_response_code(403);
        echo json_encode(["success" => false, "message" => "Anda tidak memiliki akses untuk mengubah resep ini"]);
        exit();
    }
    
    // Validasi level kesulitan
    if (!$resep->validateLevelKesulitan($data->level_kesulitan)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Level kesulitan harus Mudah, Sedang, atau Sulit"]);
        exit();
    }
    
    // Validasi jenis waktu jika ada
    if (isset($data->jenis_waktu) && !empty($data->jenis_waktu)) {
        if (!$resep->validateJenisWaktu($data->jenis_waktu)) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Jenis waktu harus Sarapan, Makan Siang, atau Makan Malam"]);
            exit();
        }
    }
    
    // Set properties
    $resep->id = $data->id;
    $resep->user_id = $data->user_id;
    $resep->nama_masakan = trim($data->nama_masakan);
    $resep->kategori_id = $data->kategori_id;
    $resep->waktu_memasak = $data->waktu_memasak;
    $resep->bahan_utama = trim($data->bahan_utama);
    $resep->deskripsi = trim($data->deskripsi);
    $resep->level_kesulitan = $data->level_kesulitan;
    $resep->jenis_waktu = isset($data->jenis_waktu) ? $data->jenis_waktu : null;
    $resep->video = isset($data->video) ? trim($data->video) : null;
    
    // Validate foreign keys
    $validation_errors = $resep->validateForeignKeys();
    if (!empty($validation_errors)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => implode(", ", $validation_errors)]);
        exit();
    }
    
    if ($resep->update()) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Resep berhasil diupdate",
            "data" => [
                "id" => (int)$resep->id,
                "nama_masakan" => $resep->nama_masakan,
                "level_kesulitan" => $resep->level_kesulitan
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Gagal mengupdate resep"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
}
?>
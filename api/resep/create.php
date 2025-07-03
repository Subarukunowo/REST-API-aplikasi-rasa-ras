<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
    exit();
}

include_once '../conf/db_config.php';
include_once '../model/Resep.php';
include_once '../model/LangkahResep.php';

try {
    $database = new Database();
    $db = $database->connect();
    $resep = new Resep($db);

    $input = file_get_contents("php://input");
    $data = json_decode($input);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Format JSON tidak valid"]);
        exit();
    }

    $required_fields = ['user_id', 'nama_masakan', 'kategori_id', 'waktu_memasak', 'bahan_utama', 'deskripsi', 'level_kesulitan', 'gambar'];
    foreach ($required_fields as $field) {
        if (empty($data->$field)) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Field $field harus diisi"]);
            exit();
        }
    }

    if (!$resep->validateLevelKesulitan($data->level_kesulitan)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Level kesulitan harus Mudah, Sedang, atau Sulit"]);
        exit();
    }

    if (isset($data->jenis_waktu) && !empty($data->jenis_waktu)) {
        if (!$resep->validateJenisWaktu($data->jenis_waktu)) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Jenis waktu harus Sarapan, Makan Siang, atau Makan Malam"]);
            exit();
        }
    }

    $gambar_base64 = $data->gambar;
    $gambar_parts = explode(',', $gambar_base64);

    if (count($gambar_parts) !== 2) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Format base64 gambar tidak valid"]);
        exit();
    }

    $decoded_image = base64_decode($gambar_parts[1]);
    if ($decoded_image === false) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Gagal mendekode gambar"]);
        exit();
    }

    $nama_file = uniqid('resep_') . '.jpg';
    $folder_upload = '../images/';
    if (!is_dir($folder_upload)) {
        mkdir($folder_upload, 0755, true);
    }

    $path_file = $folder_upload . $nama_file;
    if (!file_put_contents($path_file, $decoded_image)) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Gagal menyimpan gambar"]);
        exit();
    }

    $resep->user_id = $data->user_id;
    $resep->nama_masakan = trim($data->nama_masakan);
    $resep->kategori_id = $data->kategori_id;
    $resep->waktu_memasak = $data->waktu_memasak;
    $resep->bahan_utama = trim($data->bahan_utama);
    $resep->deskripsi = trim($data->deskripsi);
    $resep->level_kesulitan = $data->level_kesulitan;
    $resep->jenis_waktu = isset($data->jenis_waktu) ? $data->jenis_waktu : null;
    $resep->video = isset($data->video) ? trim($data->video) : null;
    $resep->gambar = $nama_file;

    $validation_errors = $resep->validateForeignKeys();
    if (!empty($validation_errors)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => implode(", ", $validation_errors)]);
        exit();
    }

    $steps = isset($data->langkah) && is_array($data->langkah) ? $data->langkah : [];

    if (count($steps) > 0) {
        if ($resep->createWithSteps($steps)) {
            $lastInsertId = $db->lastInsertId();
            http_response_code(201);
            echo json_encode([
                "success" => true,
                "message" => "Resep dan langkah berhasil dibuat",
                "data" => [
                    "id" => (int)$lastInsertId,
                    "nama_masakan" => $resep->nama_masakan,
                    "gambar" => $nama_file,
                    "level_kesulitan" => $resep->level_kesulitan
                ]
            ]);
            exit();
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Gagal menyimpan resep dan langkah"]);
            exit();
        }
    } else {
        if ($resep->create()) {
            $lastInsertId = $db->lastInsertId();
            http_response_code(201);
            echo json_encode([
                "success" => true,
                "message" => "Resep berhasil dibuat",
                "data" => [
                    "id" => (int)$lastInsertId,
                    "nama_masakan" => $resep->nama_masakan,
                    "gambar" => $nama_file,
                    "level_kesulitan" => $resep->level_kesulitan
                ]
            ]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Gagal menyimpan resep"]);
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
}

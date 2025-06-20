<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/db_config.php';
include_once '../model/JenisHidangan.php';

try {
    $database = new Database();
    $db = $database->connect();
    
    if (!$db) {
        throw new Exception("Koneksi database gagal");
    }
    
    $jenisHidangan = new JenisHidangan($db);
    
    // Check jika ada parameter ID untuk get single record
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id = intval($_GET['id']);
        
        if ($jenisHidangan->getById($id)) {
            $result = array(
                "id" => $jenisHidangan->id,
                "nama" => $jenisHidangan->nama
            );
            
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(array(
                "success" => false,
                "message" => "Data tidak ditemukan"
            ));
        }
    } else {
        // Get all records
        $stmt = $jenisHidangan->getAll();
        $num = $stmt->rowCount();
        
        if ($num > 0) {
            $jenisHidangan_arr = array();
            $jenisHidangan_arr["records"] = array();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                
                $jenisHidangan_item = array(
                    "id" => $id,
                    "nama" => $nama
                );
                
                array_push($jenisHidangan_arr["records"], $jenisHidangan_item);
            }
            
            http_response_code(200);
            echo json_encode($jenisHidangan_arr);
        } else {
            http_response_code(200);
            echo json_encode(array(
                "records" => array(),
                "message" => "Tidak ada data ditemukan"
            ));
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "Server error: " . $e->getMessage()
    ));
}
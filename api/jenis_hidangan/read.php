<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Only allow GET method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(array(
        "success" => false,
        "message" => "Method tidak diizinkan"
    ));
    exit();
}

include_once '../conf/db_config.php';
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
        
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(array(
                "success" => false,
                "message" => "ID harus berupa angka yang valid"
            ));
            exit();
        }
        
        $result = $jenisHidangan->getById($id);
        
        if ($result) {
            http_response_code(200);
            echo json_encode(array(
                "success" => true,
                "data" => array(
                    "id" => (int)$result['id'],
                    "nama" => $result['nama']
                )
            ));
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
            $records = array();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $jenisHidangan_item = array(
                    "id" => (int)$row['id'],
                    "nama" => $row['nama']
                );
                
                array_push($records, $jenisHidangan_item);
            }
            
            http_response_code(200);
            echo json_encode(array(
                "success" => true,
                "total" => $num,
                "data" => $records
            ));
        } else {
            http_response_code(200);
            echo json_encode(array(
                "success" => true,
                "total" => 0,
                "data" => array(),
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
?>
<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/db_config.php';
include_once '../model/Resep.php';

try {
    $database = new Database();
    $db = $database->connect();
    
    if (!$db) {
        throw new Exception("Koneksi database gagal");
    }
    
    $resep = new Resep($db);
    
    // Check parameter
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $kategori_id = isset($_GET['kategori_id']) ? intval($_GET['kategori_id']) : null;
    $level_kesulitan = isset($_GET['level_kesulitan']) ? $_GET['level_kesulitan'] : null;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 0;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    
    // Hitung offset untuk pagination
    $offset = ($page - 1) * $limit;
    
    if ($id > 0) {
        // Get single record
        if ($resep->getById($id)) {
            $result = array(
                "id" => $resep->id,
                "user_id" => $resep->user_id,
                "nama_masakan" => $resep->nama_masakan,
                "kategori_id" => $resep->kategori_id,
                "waktu_memasak" => $resep->waktu_memasak,
                "bahan_utama" => $resep->bahan_utama,
                "deskripsi" => $resep->deskripsi,
                "created_at" => $resep->created_at,
                "level_kesulitan" => $resep->level_kesulitan,
                "waktu_id" => $resep->waktu_id,
                "hidangan_id" => $resep->hidangan_id,
                "video" => $resep->video
            );
            
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(array(
                "success" => false,
                "message" => "Resep tidak ditemukan"
            ));
        }
    } elseif ($user_id > 0) {
        // Get resep by user
        $stmt = $resep->getByUserId($user_id);
        $num = $stmt->rowCount();
        
        if ($num > 0) {
            $resep_arr = array();
            $resep_arr["records"] = array();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $resep_item = array(
                    "id" => $row['id'],
                    "user_id" => $row['user_id'],
                    "nama_masakan" => $row['nama_masakan'],
                    "kategori_id" => $row['kategori_id'],
                    "kategori_nama" => $row['kategori_nama'],
                    "waktu_memasak" => $row['waktu_memasak'],
                    "bahan_utama" => $row['bahan_utama'],
                    "deskripsi" => $row['deskripsi'],
                    "created_at" => $row['created_at'],
                    "level_kesulitan" => $row['level_kesulitan'],
                    "waktu_id" => $row['waktu_id'],
                    "jenis_waktu" => $row['jenis_waktu'],
                    "hidangan_id" => $row['hidangan_id'],
                    "jenis_hidangan" => $row['jenis_hidangan'],
                    "video" => $row['video']
                );
                
                array_push($resep_arr["records"], $resep_item);
            }
            
            $resep_arr["total"] = $num;
            
            http_response_code(200);
            echo json_encode($resep_arr);
        } else {
            http_response_code(200);
            echo json_encode(array(
                "records" => array(),
                "total" => 0,
                "message" => "Tidak ada resep ditemukan untuk user ini"
            ));
        }
    } elseif (!empty($search)) {
        // Search resep
        $stmt = $resep->search($search, $kategori_id, $level_kesulitan);
        $num = $stmt->rowCount();
        
        if ($num > 0) {
            $resep_arr = array();
            $resep_arr["records"] = array();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $resep_item = array(
                    "id" => $row['id'],
                    "user_id" => $row['user_id'],
                    "user_name" => $row['user_name'],
                    "nama_masakan" => $row['nama_masakan'],
                    "kategori_id" => $row['kategori_id'],
                    "kategori_nama" => $row['kategori_nama'],
                    "waktu_memasak" => $row['waktu_memasak'],
                    "bahan_utama" => $row['bahan_utama'],
                    "deskripsi" => $row['deskripsi'],
                    "created_at" => $row['created_at'],
                    "level_kesulitan" => $row['level_kesulitan'],
                    "waktu_id" => $row['waktu_id'],
                    "jenis_waktu" => $row['jenis_waktu'],
                    "hidangan_id" => $row['hidangan_id'],
                    "jenis_hidangan" => $row['jenis_hidangan'],
                    "video" => $row['video']
                );
                
                array_push($resep_arr["records"], $resep_item);
            }
            
            $resep_arr["total"] = $num;
            $resep_arr["search_keyword"] = $search;
            
            http_response_code(200);
            echo json_encode($resep_arr);
        } else {
            http_response_code(200);
            echo json_encode(array(
                "records" => array(),
                "total" => 0,
                "search_keyword" => $search,
                "message" => "Tidak ada resep ditemukan dengan keyword: " . $search
            ));
        }
    } else {
        // Get all records with pagination
        $stmt = $resep->getAll($limit, $offset);
        $num = $stmt->rowCount();
        
        if ($num > 0) {
            $resep_arr = array();
            $resep_arr["records"] = array();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $resep_item = array(
                    "id" => $row['id'],
                    "user_id" => $row['user_id'],
                    "user_name" => $row['user_name'],
                    "nama_masakan" => $row['nama_masakan'],
                    "kategori_id" => $row['kategori_id'],
                    "kategori_nama" => $row['kategori_nama'],
                    "waktu_memasak" => $row['waktu_memasak'],
                    "bahan_utama" => $row['bahan_utama'],
                    "deskripsi" => $row['deskripsi'],
                    "created_at" => $row['created_at'],
                    "level_kesulitan" => $row['level_kesulitan'],
                    "waktu_id" => $row['waktu_id'],
                    "jenis_waktu" => $row['jenis_waktu'],
                    "hidangan_id" => $row['hidangan_id'],
                    "jenis_hidangan" => $row['jenis_hidangan'],
                    "video" => $row['video']
                );
                
                array_push($resep_arr["records"], $resep_item);
            }
            
            // Add pagination info
            $total_records = $resep->countAll();
            $resep_arr["pagination"] = array(
                "total_records" => $total_records,
                "current_page" => $page,
                "records_per_page" => $limit > 0 ? $limit : $num,
                "total_pages" => $limit > 0 ? ceil($total_records / $limit) : 1
            );
            
            http_response_code(200);
            echo json_encode($resep_arr);
        } else {
            http_response_code(200);
            echo json_encode(array(
                "records" => array(),
                "pagination" => array(
                    "total_records" => 0,
                    "current_page" => $page,
                    "records_per_page" => $limit,
                    "total_pages" => 0
                ),
                "message" => "Tidak ada resep ditemukan"
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
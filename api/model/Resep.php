<?php
class Resep {
    private $conn;
    private $table = 'resep';
    
    // Properties sesuai dengan struktur database
    public $id;
    public $user_id;
    public $nama_masakan;
    public $kategori_id;
    public $waktu_memasak;
    public $bahan_utama;
    public $deskripsi;
    public $created_at;
    public $level_kesulitan;
    public $jenis_waktu;
    public $video;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Create - Menambah resep baru
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                 (user_id, nama_masakan, kategori_id, waktu_memasak, bahan_utama, deskripsi, level_kesulitan, jenis_waktu, video, created_at) 
                 VALUES 
                 (:user_id, :nama_masakan, :kategori_id, :waktu_memasak, :bahan_utama, :deskripsi, :level_kesulitan, :jenis_waktu, :video, NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->bindParam(':nama_masakan', $this->nama_masakan, PDO::PARAM_STR);
        $stmt->bindParam(':kategori_id', $this->kategori_id, PDO::PARAM_INT);
        $stmt->bindParam(':waktu_memasak', $this->waktu_memasak, PDO::PARAM_INT);
        $stmt->bindParam(':bahan_utama', $this->bahan_utama, PDO::PARAM_STR);
        $stmt->bindParam(':deskripsi', $this->deskripsi, PDO::PARAM_STR);
        $stmt->bindParam(':level_kesulitan', $this->level_kesulitan, PDO::PARAM_STR);
        $stmt->bindParam(':jenis_waktu', $this->jenis_waktu, PDO::PARAM_STR);
        $stmt->bindParam(':video', $this->video, PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Read - Mendapatkan semua resep
    public function getAll($limit = 0, $offset = 0) {
        $query = "SELECT r.*, u.username as user_name 
                 FROM " . $this->table . " r 
                 LEFT JOIN users u ON r.user_id = u.id 
                 ORDER BY r.created_at DESC";
        
        if ($limit > 0) {
            $query .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if ($limit > 0) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt;
    }
    
    // Read - Mendapatkan resep berdasarkan ID
    public function getById($id) {
        $query = "SELECT r.*, u.username as user_name 
                 FROM " . $this->table . " r 
                 LEFT JOIN users u ON r.user_id = u.id 
                 WHERE r.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Read - Mendapatkan resep berdasarkan user ID
    public function getByUserId($user_id) {
        $query = "SELECT r.*, u.username as user_name 
                 FROM " . $this->table . " r 
                 LEFT JOIN users u ON r.user_id = u.id 
                 WHERE r.user_id = :user_id 
                 ORDER BY r.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Search - Mencari resep berdasarkan kriteria
    public function search($keyword, $kategori_id = null, $level_kesulitan = null) {
        $query = "SELECT r.*, u.username as user_name 
                 FROM " . $this->table . " r 
                 LEFT JOIN users u ON r.user_id = u.id 
                 WHERE (r.nama_masakan LIKE :keyword OR r.bahan_utama LIKE :keyword OR r.deskripsi LIKE :keyword)";
        
        if ($kategori_id) {
            $query .= " AND r.kategori_id = :kategori_id";
        }
        
        if ($level_kesulitan) {
            $query .= " AND r.level_kesulitan = :level_kesulitan";
        }
        
        $query .= " ORDER BY r.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        
        $keyword = "%{$keyword}%";
        $stmt->bindParam(':keyword', $keyword, PDO::PARAM_STR);
        
        if ($kategori_id) {
            $stmt->bindParam(':kategori_id', $kategori_id, PDO::PARAM_INT);
        }
        
        if ($level_kesulitan) {
            $stmt->bindParam(':level_kesulitan', $level_kesulitan, PDO::PARAM_STR);
        }
        
        $stmt->execute();
        return $stmt;
    }
    
    // Update - Mengupdate resep
    public function update() {
        $query = "UPDATE " . $this->table . " 
                 SET user_id = :user_id,
                     nama_masakan = :nama_masakan,
                     kategori_id = :kategori_id,
                     waktu_memasak = :waktu_memasak,
                     bahan_utama = :bahan_utama,
                     deskripsi = :deskripsi,
                     level_kesulitan = :level_kesulitan,
                     jenis_waktu = :jenis_waktu,
                     video = :video
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->bindParam(':nama_masakan', $this->nama_masakan, PDO::PARAM_STR);
        $stmt->bindParam(':kategori_id', $this->kategori_id, PDO::PARAM_INT);
        $stmt->bindParam(':waktu_memasak', $this->waktu_memasak, PDO::PARAM_INT);
        $stmt->bindParam(':bahan_utama', $this->bahan_utama, PDO::PARAM_STR);
        $stmt->bindParam(':deskripsi', $this->deskripsi, PDO::PARAM_STR);
        $stmt->bindParam(':level_kesulitan', $this->level_kesulitan, PDO::PARAM_STR);
        $stmt->bindParam(':jenis_waktu', $this->jenis_waktu, PDO::PARAM_STR);
        $stmt->bindParam(':video', $this->video, PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Delete - Menghapus resep
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Validasi foreign keys
    public function validateForeignKeys() {
        $errors = [];
        
        // Validasi user_id
        $query = "SELECT id FROM users WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            $errors[] = "User ID tidak valid";
        }
        
        // Validasi kategori_id (jika ada tabel kategori)
        $query = "SELECT id FROM kategori WHERE id = :kategori_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':kategori_id', $this->kategori_id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            $errors[] = "Kategori ID tidak valid";
        }
        
        return $errors;
    }
    
    // Validasi level kesulitan
    public function validateLevelKesulitan($level) {
        $valid_levels = ['Mudah', 'Sedang', 'Sulit'];
        return in_array($level, $valid_levels);
    }
    
    // Validasi jenis waktu
    public function validateJenisWaktu($jenis) {
        $valid_jenis = ['Sarapan', 'Makan Siang', 'Makan Malam'];
        return in_array($jenis, $valid_jenis);
    }
}
?>
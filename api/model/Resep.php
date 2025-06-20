<?php
class Resep {
    public $conn;
    public $table = 'resep';
    
    public $id;
    public $user_id;
    public $nama_masakan;
    public $kategori_id;
    public $waktu_memasak;
    public $bahan_utama;
    public $deskripsi;
    public $created_at;
    public $level_kesulitan;
    public $waktu_id;
    public $hidangan_id;
    public $video;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Method untuk mendapatkan semua data dengan join
    public function getAll($limit = 0, $offset = 0) {
        $query = "SELECT 
                    r.id,
                    r.user_id,
                    r.nama_masakan,
                    r.kategori_id,
                    r.waktu_memasak,
                    r.bahan_utama,
                    r.deskripsi,
                    r.created_at,
                    r.level_kesulitan,
                    r.waktu_id,
                    r.hidangan_id,
                    r.video,
                    u.username as user_name,
                    k.nama as kategori_nama,
                    jw.nama as jenis_waktu,
                    jh.nama as jenis_hidangan
                  FROM {$this->table} r
                  LEFT JOIN users u ON r.user_id = u.id
                  LEFT JOIN kategori k ON r.kategori_id = k.id
                  LEFT JOIN jenis_waktu jw ON r.waktu_id = jw.id
                  LEFT JOIN jenis_hidangan jh ON r.hidangan_id = jh.id
                  ORDER BY r.created_at DESC";
        
        if ($limit > 0) {
            $query .= " LIMIT " . $limit;
            if ($offset > 0) {
                $query .= " OFFSET " . $offset;
            }
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    // Method untuk mendapatkan data berdasarkan ID
    public function getById($id) {
        $query = "SELECT 
                    r.id,
                    r.user_id,
                    r.nama_masakan,
                    r.kategori_id,
                    r.waktu_memasak,
                    r.bahan_utama,
                    r.deskripsi,
                    r.created_at,
                    r.level_kesulitan,
                    r.waktu_id,
                    r.hidangan_id,
                    r.video,
                    u.username as user_name,
                    k.nama as kategori_nama,
                    jw.nama as jenis_waktu,
                    jh.nama as jenis_hidangan
                  FROM {$this->table} r
                  LEFT JOIN users u ON r.user_id = u.id
                  LEFT JOIN kategori k ON r.kategori_id = k.id
                  LEFT JOIN jenis_waktu jw ON r.waktu_id = jw.id
                  LEFT JOIN jenis_hidangan jh ON r.hidangan_id = jh.id
                  WHERE r.id = ? LIMIT 0,1";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->id = $row['id'];
            $this->user_id = $row['user_id'];
            $this->nama_masakan = $row['nama_masakan'];
            $this->kategori_id = $row['kategori_id'];
            $this->waktu_memasak = $row['waktu_memasak'];
            $this->bahan_utama = $row['bahan_utama'];
            $this->deskripsi = $row['deskripsi'];
            $this->created_at = $row['created_at'];
            $this->level_kesulitan = $row['level_kesulitan'];
            $this->waktu_id = $row['waktu_id'];
            $this->hidangan_id = $row['hidangan_id'];
            $this->video = $row['video'];
            return true;
        }
        return false;
    }
    
    // Method untuk search resep
    public function search($keyword, $kategori_id = null, $level_kesulitan = null) {
        $query = "SELECT 
                    r.id,
                    r.user_id,
                    r.nama_masakan,
                    r.kategori_id,
                    r.waktu_memasak,
                    r.bahan_utama,
                    r.deskripsi,
                    r.created_at,
                    r.level_kesulitan,
                    r.waktu_id,
                    r.hidangan_id,
                    r.video,
                    u.username as user_name,
                    k.nama as kategori_nama,
                    jw.nama as jenis_waktu,
                    jh.nama as jenis_hidangan
                  FROM {$this->table} r
                  LEFT JOIN users u ON r.user_id = u.id
                  LEFT JOIN kategori k ON r.kategori_id = k.id
                  LEFT JOIN jenis_waktu jw ON r.waktu_id = jw.id
                  LEFT JOIN jenis_hidangan jh ON r.hidangan_id = jh.id
                  WHERE (r.nama_masakan LIKE ? OR r.bahan_utama LIKE ? OR r.deskripsi LIKE ?)";
        
        $params = ["%$keyword%", "%$keyword%", "%$keyword%"];
        
        if ($kategori_id) {
            $query .= " AND r.kategori_id = ?";
            $params[] = $kategori_id;
        }
        
        if ($level_kesulitan) {
            $query .= " AND r.level_kesulitan = ?";
            $params[] = $level_kesulitan;
        }
        
        $query .= " ORDER BY r.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }
    
    // Method untuk mendapatkan resep berdasarkan user
    public function getByUserId($user_id) {
        $query = "SELECT 
                    r.id,
                    r.user_id,
                    r.nama_masakan,
                    r.kategori_id,
                    r.waktu_memasak,
                    r.bahan_utama,
                    r.deskripsi,
                    r.created_at,
                    r.level_kesulitan,
                    r.waktu_id,
                    r.hidangan_id,
                    r.video,
                    k.nama as kategori_nama,
                    jw.nama as jenis_waktu,
                    jh.nama as jenis_hidangan
                  FROM {$this->table} r
                  LEFT JOIN kategori k ON r.kategori_id = k.id
                  LEFT JOIN jenis_waktu jw ON r.waktu_id = jw.id
                  LEFT JOIN jenis_hidangan jh ON r.hidangan_id = jh.id
                  WHERE r.user_id = ?
                  ORDER BY r.created_at DESC";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt;
    }
    
    // Method untuk membuat resep baru
    public function create() {
        $query = "INSERT INTO {$this->table} 
                    (user_id, nama_masakan, kategori_id, waktu_memasak, bahan_utama, 
                     deskripsi, level_kesulitan, waktu_id, hidangan_id, video, created_at) 
                  VALUES 
                    (:user_id, :nama_masakan, :kategori_id, :waktu_memasak, :bahan_utama, 
                     :deskripsi, :level_kesulitan, :waktu_id, :hidangan_id, :video, NOW())";
                     
        $stmt = $this->conn->prepare($query);
        
        // Sanitize input
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->nama_masakan = htmlspecialchars(strip_tags($this->nama_masakan));
        $this->kategori_id = htmlspecialchars(strip_tags($this->kategori_id));
        $this->waktu_memasak = htmlspecialchars(strip_tags($this->waktu_memasak));
        $this->bahan_utama = htmlspecialchars(strip_tags($this->bahan_utama));
        $this->deskripsi = htmlspecialchars(strip_tags($this->deskripsi));
        $this->level_kesulitan = htmlspecialchars(strip_tags($this->level_kesulitan));
        $this->waktu_id = htmlspecialchars(strip_tags($this->waktu_id));
        $this->hidangan_id = htmlspecialchars(strip_tags($this->hidangan_id));
        $this->video = htmlspecialchars(strip_tags($this->video));
        
        // Bind data
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':nama_masakan', $this->nama_masakan);
        $stmt->bindParam(':kategori_id', $this->kategori_id);
        $stmt->bindParam(':waktu_memasak', $this->waktu_memasak);
        $stmt->bindParam(':bahan_utama', $this->bahan_utama);
        $stmt->bindParam(':deskripsi', $this->deskripsi);
        $stmt->bindParam(':level_kesulitan', $this->level_kesulitan);
        $stmt->bindParam(':waktu_id', $this->waktu_id);
        $stmt->bindParam(':hidangan_id', $this->hidangan_id);
        $stmt->bindParam(':video', $this->video);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Method untuk update resep
    public function update() {
        $query = "UPDATE {$this->table} SET 
                    nama_masakan = :nama_masakan,
                    kategori_id = :kategori_id,
                    waktu_memasak = :waktu_memasak,
                    bahan_utama = :bahan_utama,
                    deskripsi = :deskripsi,
                    level_kesulitan = :level_kesulitan,
                    waktu_id = :waktu_id,
                    hidangan_id = :hidangan_id,
                    video = :video
                  WHERE id = :id AND user_id = :user_id";
                  
        $stmt = $this->conn->prepare($query);
        
        // Sanitize input
        $this->nama_masakan = htmlspecialchars(strip_tags($this->nama_masakan));
        $this->kategori_id = htmlspecialchars(strip_tags($this->kategori_id));
        $this->waktu_memasak = htmlspecialchars(strip_tags($this->waktu_memasak));
        $this->bahan_utama = htmlspecialchars(strip_tags($this->bahan_utama));
        $this->deskripsi = htmlspecialchars(strip_tags($this->deskripsi));
        $this->level_kesulitan = htmlspecialchars(strip_tags($this->level_kesulitan));
        $this->waktu_id = htmlspecialchars(strip_tags($this->waktu_id));
        $this->hidangan_id = htmlspecialchars(strip_tags($this->hidangan_id));
        $this->video = htmlspecialchars(strip_tags($this->video));
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        
        // Bind data
        $stmt->bindParam(':nama_masakan', $this->nama_masakan);
        $stmt->bindParam(':kategori_id', $this->kategori_id);
        $stmt->bindParam(':waktu_memasak', $this->waktu_memasak);
        $stmt->bindParam(':bahan_utama', $this->bahan_utama);
        $stmt->bindParam(':deskripsi', $this->deskripsi);
        $stmt->bindParam(':level_kesulitan', $this->level_kesulitan);
        $stmt->bindParam(':waktu_id', $this->waktu_id);
        $stmt->bindParam(':hidangan_id', $this->hidangan_id);
        $stmt->bindParam(':video', $this->video);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Method untuk delete resep
    public function delete() {
        $query = "DELETE FROM {$this->table} WHERE id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        
        $stmt->bindParam(1, $this->id);
        $stmt->bindParam(2, $this->user_id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Method untuk validasi foreign key
    public function validateForeignKeys() {
        $errors = array();
        
        // Check user_id
        if ($this->user_id) {
            $query = "SELECT id FROM users WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$this->user_id]);
            if ($stmt->rowCount() == 0) {
                $errors[] = "User ID tidak valid";
            }
        }
        
        // Check kategori_id
        if ($this->kategori_id) {
            $query = "SELECT id FROM kategori WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$this->kategori_id]);
            if ($stmt->rowCount() == 0) {
                $errors[] = "Kategori ID tidak valid";
            }
        }
        
        // Check waktu_id
        if ($this->waktu_id) {
            $query = "SELECT id FROM jenis_waktu WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$this->waktu_id]);
            if ($stmt->rowCount() == 0) {
                $errors[] = "Waktu ID tidak valid";
            }
        }
        
        // Check hidangan_id
        if ($this->hidangan_id) {
            $query = "SELECT id FROM jenis_hidangan WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$this->hidangan_id]);
            if ($stmt->rowCount() == 0) {
                $errors[] = "Hidangan ID tidak valid";
            }
        }
        
        return $errors;
    }
    
    // Method untuk count total resep
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
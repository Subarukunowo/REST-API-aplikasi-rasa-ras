<?php
// api/model/LangkahResep.php

class LangkahResep {
    private $conn;
    private $table_name = "langkah_resep";

    // Properties
    public $id;
    public $resep_id;
    public $urutan;
    public $judul;
    public $deskripsi;


    public function __construct($db) {
        $this->conn = $db;
    }

    // Create langkah resep
    function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET resep_id=:resep_id, urutan=:urutan, judul=:judul, deskripsi=:deskripsi";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->resep_id = htmlspecialchars(strip_tags($this->resep_id));
        $this->urutan = htmlspecialchars(strip_tags($this->urutan));
        $this->judul = htmlspecialchars(strip_tags($this->judul));
        $this->deskripsi = htmlspecialchars(strip_tags($this->deskripsi));
        
        // Bind values
        $stmt->bindParam(":resep_id", $this->resep_id);
        $stmt->bindParam(":urutan", $this->urutan);
        $stmt->bindParam(":judul", $this->judul);
        $stmt->bindParam(":deskripsi", $this->deskripsi);
        
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }
public function readByResepId() {
    $query = "SELECT id, resep_id, urutan, judul, deskripsi
              FROM " . $this->table_name . " 
              WHERE resep_id = :resep_id 
              ORDER BY urutan ASC";
    
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":resep_id", $this->resep_id);
    $stmt->execute();

    return $stmt;
}
    // Read all langkah resep
    function readAll() {
        $query = "SELECT lr.id, lr.resep_id, lr.urutan, lr.judul, lr.deskripsi, 
                        lr.created_at, lr.updated_at, r.nama_masakan
                 FROM " . $this->table_name . " lr
                 LEFT JOIN resep r ON lr.resep_id = r.id
                 ORDER BY lr.resep_id ASC, lr.urutan ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    // Read single langkah resep
    function readOne() {
        $query = "SELECT lr.id, lr.resep_id, lr.urutan, lr.judul, lr.deskripsi, 
                        lr.created_at, lr.updated_at, r.nama_masakan
                 FROM " . $this->table_name . " lr
                 LEFT JOIN resep r ON lr.resep_id = r.id
                 WHERE lr.id = :id
                 LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->id = $row['id'];
            $this->resep_id = $row['resep_id'];
            $this->urutan = $row['urutan'];
            $this->judul = $row['judul'];
            $this->deskripsi = $row['deskripsi'];
        
            return true;
        }
        return false;
    }

    // Update langkah resep
    function update() {
        $query = "UPDATE " . $this->table_name . " 
                 SET urutan=:urutan, judul=:judul, deskripsi=:deskripsi, updated_at=NOW()
                 WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->urutan = htmlspecialchars(strip_tags($this->urutan));
        $this->judul = htmlspecialchars(strip_tags($this->judul));
        $this->deskripsi = htmlspecialchars(strip_tags($this->deskripsi));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Bind values
        $stmt->bindParam(":urutan", $this->urutan);
        $stmt->bindParam(":judul", $this->judul);
        $stmt->bindParam(":deskripsi", $this->deskripsi);
        $stmt->bindParam(":id", $this->id);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete langkah resep
    function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(":id", $this->id);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete all langkah resep by resep_id
    function deleteByResepId() {
        $query = "DELETE FROM " . $this->table_name . " WHERE resep_id = :resep_id";
        
        $stmt = $this->conn->prepare($query);
        $this->resep_id = htmlspecialchars(strip_tags($this->resep_id));
        $stmt->bindParam(":resep_id", $this->resep_id);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Count langkah resep by resep_id
    function countByResepId() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE resep_id = :resep_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":resep_id", $this->resep_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Get next urutan number
    function getNextUrutan() {
        $query = "SELECT COALESCE(MAX(urutan), 0) + 1 as next_urutan 
                 FROM " . $this->table_name . " 
                 WHERE resep_id = :resep_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":resep_id", $this->resep_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['next_urutan'];
    }

    // Reorder langkah resep
    function reorder($langkah_ids) {
        $this->conn->beginTransaction();
        
        try {
            $query = "UPDATE " . $this->table_name . " SET urutan = :urutan WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            
            foreach ($langkah_ids as $index => $langkah_id) {
                $urutan = $index + 1;
                $stmt->bindParam(":urutan", $urutan);
                $stmt->bindParam(":id", $langkah_id);
                $stmt->execute();
            }
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
    
}
?>
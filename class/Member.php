<?php
require_once 'config/db.php';

// Class untuk mengelola data Member (CRUD pada tabel members)
class Member {
    private $db;

    // Menginisialisasi koneksi database
    public function __construct() {
        $this->db = (new Database())->conn;
    }

    // Mengambil seluruh data member
    public function getAllMembers() {
        $stmt = $this->db->prepare("SELECT * FROM members");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Menambahkan member baru dengan tanggal daftar hari ini
    public function createMember($name, $phone, $email) {
        try {
            $stmt = $this->db->prepare("INSERT INTO members (name, phone, email, join_date) VALUES (?, ?, ?, CURDATE())");
            return $stmt->execute([$name, $phone, $email]);
        } catch (PDOException $e) {
            // Error 1062: Duplicate entry (email sudah terdaftar)
            if($e->errorInfo[1] == 1062) {
                return 'duplicate';
            }
            throw $e;
        }
    }

    // Memperbarui informasi member berdasarkan ID
    public function updateMember($id, $name, $phone, $email) {
        try{
            $stmt = $this->db->prepare("UPDATE members SET name = ?, phone = ?, email = ? WHERE id = ?");
            return $stmt->execute([$name, $phone, $email, $id]);
        } catch (PDOException $e) {
            // Error 1062: Duplicate entry (email sudah digunakan member lain)
            if($e->errorInfo[1] == 1062) {
                return 'duplicate';
            }
            throw $e;
        }
    }

    // Menghapus data member berdasarkan ID
    public function deleteMember($id) {
        try{
            $stmt = $this->db->prepare("DELETE FROM members WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            // Error 1451: Foreign key constraint (masih memiliki data keanggotaan)
            if($e->errorInfo[1] == 1451) {
                return 'in use';
            }
            throw $e;
        }
    }
}
?>
<?php
require_once 'config/db.php';

// Class untuk mengelola data Paket Gym (CRUD pada tabel packages)
class Package {
    private $db;

    // Menginisialisasi koneksi database
    public function __construct() {
        $this->db = (new Database())->conn;
    }

    // Mengambil seluruh data paket
    public function getAllPackages() {
        $stmt = $this->db->prepare("SELECT * FROM packages");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Menambahkan paket baru
    public function createPackage($package_name, $price, $duration_days) {
        $stmt = $this->db->prepare("INSERT INTO packages (package_name, price, duration_days) VALUES (?, ?, ?)");
        return $stmt->execute([$package_name, $price, $duration_days]);
    }

    // Memperbarui informasi paket berdasarkan ID
    public function updatePackage($id, $package_name, $price, $duration_days) {
        $stmt = $this->db->prepare("UPDATE packages SET package_name = ?, price = ?, duration_days = ? WHERE id = ?");
        return $stmt->execute([$package_name, $price, $duration_days, $id]);
    }

    // Menghapus data paket berdasarkan ID
    public function deletePackage($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM packages WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            // Error 1451: Foreign key constraint (masih dipakai di data memberships)
            if($e->errorInfo[1] == 1451) {
                return 'in use';
            }
            throw $e;
        }
    }
}

?>
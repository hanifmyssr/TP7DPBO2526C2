<?php
require_once 'config/db.php';

class Package {
    private $db;

    public function __construct() {
        $this->db = (new Database())->conn;
    }

    public function getAllPackages() {
        $stmt = $this->db->prepare("SELECT * FROM packages");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createPackage($package_name, $price, $duration_days) {
        $stmt = $this->db->prepare("INSERT INTO packages (package_name, price, duration_days) VALUES (?, ?, ?)");
        return $stmt->execute([$package_name, $price, $duration_days]);
    }

    public function updatePackage($id, $package_name, $price, $duration_days) {
        $stmt = $this->db->prepare("UPDATE packages SET package_name = ?, price = ?, duration_days = ? WHERE id = ?");
        return $stmt->execute([$package_name, $price, $duration_days, $id]);
    }

    public function deletePackage($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM packages WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            if($e->errorInfo[1] == 1451) {
                return 'in use';
            }
            throw $e;
        }
    }
}

?>
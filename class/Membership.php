<?php
require_once 'config/db.php';

// Class untuk mengelola data Keanggotaan/Transaksi (CRUD pada tabel memberships)
class Membership {
    private $db;

    // Menginisialisasi koneksi database
    public function __construct() {
        $this->db = (new Database())->conn;
    }

    // Mengambil seluruh data membership dengan JOIN ke tabel members dan packages
    public function getAllMemberships() {
        $stmt = $this->db->prepare("SELECT ms.id, m.name AS member_name, p.package_name, ms.start_date, ms.end_date, ms.total_paid, ms.status
                                    FROM memberships ms
                                    JOIN members m ON ms.member_id = m.id
                                    JOIN packages p ON ms.package_id = p.id
                                    ORDER BY ms.id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mengecek apakah member masih memiliki keanggotaan yang aktif dan belum expired
    public function hasActiveMembership($memberId) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM memberships
             WHERE member_id = ? AND status = 'active' AND end_date >= CURDATE()"
        );
        $stmt->execute([$memberId]);
        return $stmt->fetchColumn() > 0;
    }

    // Menambahkan transaksi membership baru dan menghitung end_date secara otomatis
    public function createMembership($memberId, $packageId, $startDate, $price, $durationDays) {
        $endDate = date('Y-m-d', strtotime($startDate . ' + ' . (int) $durationDays . ' days'));

        try {
            $stmt = $this->db->prepare(
                "INSERT INTO memberships (member_id, package_id, start_date, end_date, total_paid)
                 VALUES (?, ?, ?, ?, ?)"
            );
            return $stmt->execute([$memberId, $packageId, $startDate, $endDate, $price]);
        } catch (PDOException $e) {
            // Error 1452: Foreign key tidak ditemukan (member_id atau package_id tidak valid)
            if ($e->errorInfo[1] == 1452) {
                return 'invalid_reference';
            }
            throw $e;
        }
    }

    // Memperbarui status keanggotaan (active, expired, cancelled)
    public function updateMembership($id, $status) {
        $allowed = ['active', 'expired', 'cancelled'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }

        $stmt = $this->db->prepare("UPDATE memberships SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    // Menghapus data keanggotaan berdasarkan ID
    public function deleteMembership($id) {
        $stmt = $this->db->prepare("DELETE FROM memberships WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
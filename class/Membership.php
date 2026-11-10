<?php
require_once 'config/db.php';

class Membership {
    private $db;

    public function __construct() {
        $this->db = (new Database())->conn;
    }

    public function getAllMemberships() {
        $stmt = $this->db->prepare("SELECT ms.id, m.name AS member_name, p.package_name, ms.start_date, ms.end_date, ms.total_paid, ms.status
                                    FROM memberships ms
                                    JOIN members m ON ms.member_id = m.id
                                    JOIN packages p ON ms.package_id = p.id
                                    ORDER BY ms.id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function hasActiveMembership($memberId) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM memberships
             WHERE member_id = ? AND status = 'active' AND end_date >= CURDATE()"
        );
        $stmt->execute([$memberId]);
        return $stmt->fetchColumn() > 0;
    }

    public function createMembership($memberId, $packageId, $startDate, $price, $durationDays) {
        $endDate = date('Y-m-d', strtotime($startDate . ' + ' . (int) $durationDays . ' days'));

        try {
            $stmt = $this->db->prepare(
                "INSERT INTO memberships (member_id, package_id, start_date, end_date, total_paid)
                 VALUES (?, ?, ?, ?, ?)"
            );
            return $stmt->execute([$memberId, $packageId, $startDate, $endDate, $price]);
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1452) {
                return 'invalid_reference';
            }
            throw $e;
        }
    }

    public function updateMembership($id, $status) {
        $allowed = ['active', 'expired', 'cancelled'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }

        $stmt = $this->db->prepare("UPDATE memberships SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function deleteMembership($id) {
        $stmt = $this->db->prepare("DELETE FROM memberships WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
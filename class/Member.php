<?php
require_once 'config/db.php';

class Member {
    private $db;

    public function __construct() {
        $this->db = (new Database())->conn;
    }

    public function getAllMembers() {
        $stmt = $this->db->prepare("SELECT * FROM members");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createMember($name, $phone, $email) {
        try {
            $stmt = $this->db->prepare("INSERT INTO members (name, phone, email, join_date) VALUES (?, ?, ?, CURDATE())");
            return $stmt->execute([$name, $phone, $email]);
        } catch (PDOException $e) {
            if($e->errorInfo[1] == 1062) {
                return 'duplicate';
            }
            throw $e;
        }
    }

    public function updateMember($id, $name, $phone, $email) {
        try{
            $stmt = $this->db->prepare("UPDATE members SET name = ?, phone = ?, email = ? WHERE id = ?");
            return $stmt->execute([$name, $phone, $email, $id]);
        } catch (PDOException $e) {
            if($e->errorInfo[1] == 1062) {
                return 'duplicate';
            }
            throw $e;
        }
    }

    public function deleteMember($id) {
        try{
            $stmt = $this->db->prepare("DELETE FROM members WHERE id = ?");
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
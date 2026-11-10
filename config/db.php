<?php
// Class untuk mengelola koneksi database MySQL menggunakan PDO
class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "db_gymhub";
    public $conn;

    // Membuka koneksi ke database dan mengatur mode error exception
    public function __construct() {
        try {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
}
?>
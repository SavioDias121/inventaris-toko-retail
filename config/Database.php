<?php
/**
 * Class Database
 * 
 * Mengelola koneksi ke database MySQL menggunakan PDO.
 * Menerapkan konsep Encapsulation dengan menyembunyikan
 * detail koneksi database melalui property private.
 */
class Database
{
    // Property private - Encapsulation
    private $host = "localhost";
    private $db_name = "inventaris_toko";
    private $username = "root";
    private $password = "";
    private $conn;

    /**
     * Membuat dan mengembalikan koneksi PDO ke database MySQL.
     * 
     * @return PDO|null Objek koneksi PDO atau null jika gagal
     */
    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            // Set mode error ke exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Set charset UTF-8
            $this->conn->exec("set names utf8mb4");
        } catch (PDOException $exception) {
            echo "Koneksi database gagal: " . $exception->getMessage();
        }

        return $this->conn;
    }
}

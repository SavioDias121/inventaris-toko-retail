<?php
require_once __DIR__ . '/Produk.php';

/**
 * Class Laptop
 * 
 * Mewarisi (extends) abstract class Produk.
 * Menerapkan konsep:
 * - Inheritance: mewarisi semua property dan method dari Produk
 * - Method Override: mengimplementasikan abstract method dari parent
 * - Encapsulation: property private khusus Laptop
 */
class Laptop extends Produk
{
    // Property private khusus Laptop - Encapsulation
    private $tableSpesifikasi = 'laptop';
    private $processor;
    private $ram;
    private $storage;
    private $ukuran_layar;

    /**
     * Constructor - memanggil constructor parent
     * 
     * @param PDO $db Objek koneksi database
     */
    public function __construct($db)
    {
        // Memanggil constructor parent class (Produk)
        parent::__construct($db);
    }

    /**
     * Mengambil spesifikasi laptop berdasarkan produk_id
     * Override dari abstract method parent
     * 
     * @param int $produk_id ID produk
     * @return array|false Data spesifikasi laptop
     */
    public function getSpesifikasi($produk_id)
    {
        $query = "SELECT * FROM " . $this->tableSpesifikasi . " WHERE produk_id = :produk_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':produk_id', $produk_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Menyimpan spesifikasi laptop
     * Override dari abstract method parent
     * 
     * @param int $produk_id ID produk
     * @param array $data Data spesifikasi (processor, ram, storage, ukuran_layar)
     * @return bool Berhasil atau tidak
     */
    public function simpanSpesifikasi($produk_id, $data)
    {
        $query = "INSERT INTO " . $this->tableSpesifikasi . " 
                  (produk_id, processor, ram, storage, ukuran_layar) 
                  VALUES (:produk_id, :processor, :ram, :storage, :ukuran_layar)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':produk_id', $produk_id, PDO::PARAM_INT);
        $stmt->bindParam(':processor', $data['processor']);
        $stmt->bindParam(':ram', $data['ram']);
        $stmt->bindParam(':storage', $data['storage']);
        $stmt->bindParam(':ukuran_layar', $data['ukuran_layar']);

        return $stmt->execute();
    }

    /**
     * Mengedit spesifikasi laptop
     * Override dari abstract method parent
     * 
     * @param int $produk_id ID produk
     * @param array $data Data spesifikasi baru
     * @return bool Berhasil atau tidak
     */
    public function editSpesifikasi($produk_id, $data)
    {
        $query = "UPDATE " . $this->tableSpesifikasi . " 
                  SET processor = :processor, ram = :ram, 
                      storage = :storage, ukuran_layar = :ukuran_layar 
                  WHERE produk_id = :produk_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':produk_id', $produk_id, PDO::PARAM_INT);
        $stmt->bindParam(':processor', $data['processor']);
        $stmt->bindParam(':ram', $data['ram']);
        $stmt->bindParam(':storage', $data['storage']);
        $stmt->bindParam(':ukuran_layar', $data['ukuran_layar']);

        return $stmt->execute();
    }

    /**
     * Mengambil data produk laptop lengkap dengan spesifikasi (JOIN)
     * 
     * @return array Daftar laptop dengan spesifikasi
     */
    public function getAllDenganSpesifikasi()
    {
        $query = "SELECT p.*, l.processor, l.ram, l.storage, l.ukuran_layar 
                  FROM produk p 
                  INNER JOIN laptop l ON p.id = l.produk_id 
                  WHERE p.jenis = 'laptop' 
                  ORDER BY p.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

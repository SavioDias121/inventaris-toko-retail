<?php
require_once __DIR__ . '/Produk.php';

/**
 * Class Smartphone
 * 
 * Mewarisi (extends) abstract class Produk.
 * Menerapkan konsep:
 * - Inheritance: mewarisi semua property dan method dari Produk
 * - Method Override: mengimplementasikan abstract method dari parent
 * - Encapsulation: property private khusus Smartphone
 * 
 * Perbedaan dengan Laptop: memiliki tambahan field 'kamera'
 */
class Smartphone extends Produk
{
    // Property private khusus Smartphone - Encapsulation
    private $tableSpesifikasi = 'smartphone';
    private $processor;
    private $ram;
    private $storage;
    private $ukuran_layar;
    private $kamera;

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
     * Mengambil spesifikasi smartphone berdasarkan produk_id
     * Override dari abstract method parent
     * 
     * @param int $produk_id ID produk
     * @return array|false Data spesifikasi smartphone
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
     * Menyimpan spesifikasi smartphone
     * Override dari abstract method parent
     * 
     * @param int $produk_id ID produk
     * @param array $data Data spesifikasi (processor, ram, storage, ukuran_layar, kamera)
     * @return bool Berhasil atau tidak
     */
    public function simpanSpesifikasi($produk_id, $data)
    {
        $query = "INSERT INTO " . $this->tableSpesifikasi . " 
                  (produk_id, processor, ram, storage, ukuran_layar, kamera) 
                  VALUES (:produk_id, :processor, :ram, :storage, :ukuran_layar, :kamera)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':produk_id', $produk_id, PDO::PARAM_INT);
        $stmt->bindParam(':processor', $data['processor']);
        $stmt->bindParam(':ram', $data['ram']);
        $stmt->bindParam(':storage', $data['storage']);
        $stmt->bindParam(':ukuran_layar', $data['ukuran_layar']);
        $stmt->bindParam(':kamera', $data['kamera']);

        return $stmt->execute();
    }

    /**
     * Mengedit spesifikasi smartphone
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
                      storage = :storage, ukuran_layar = :ukuran_layar,
                      kamera = :kamera 
                  WHERE produk_id = :produk_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':produk_id', $produk_id, PDO::PARAM_INT);
        $stmt->bindParam(':processor', $data['processor']);
        $stmt->bindParam(':ram', $data['ram']);
        $stmt->bindParam(':storage', $data['storage']);
        $stmt->bindParam(':ukuran_layar', $data['ukuran_layar']);
        $stmt->bindParam(':kamera', $data['kamera']);

        return $stmt->execute();
    }

    /**
     * Mengambil data produk smartphone lengkap dengan spesifikasi (JOIN)
     * 
     * @return array Daftar smartphone dengan spesifikasi
     */
    public function getAllDenganSpesifikasi()
    {
        $query = "SELECT p.*, s.processor, s.ram, s.storage, s.ukuran_layar, s.kamera 
                  FROM produk p 
                  INNER JOIN smartphone s ON p.id = s.produk_id 
                  WHERE p.jenis = 'smartphone' 
                  ORDER BY p.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

<?php
/**
 * Abstract Class Produk
 * 
 * Class abstrak yang menjadi blueprint untuk semua jenis produk.
 * Menerapkan konsep:
 * - Abstract Class: tidak bisa diinstansiasi langsung
 * - Encapsulation: property protected hanya bisa diakses oleh child class
 * - Abstract Method: method yang wajib diimplementasikan oleh child class
 */
abstract class Produk
{
    // Property protected - Encapsulation (hanya bisa diakses oleh child class)
    protected $conn;
    protected $table = 'produk';
    protected $id;
    protected $nama;
    protected $jenis;
    protected $harga;
    protected $stok;
    protected $deskripsi;

    /**
     * Constructor - menerima koneksi database
     * 
     * @param PDO $db Objek koneksi database
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // ============================================
    // Abstract Methods - WAJIB diimplementasikan oleh child class
    // ============================================

    /**
     * Mengambil spesifikasi khusus produk
     * @param int $produk_id ID produk
     * @return array Data spesifikasi
     */
    abstract public function getSpesifikasi($produk_id);

    /**
     * Menyimpan spesifikasi khusus produk
     * @param int $produk_id ID produk
     * @param array $data Data spesifikasi
     * @return bool Berhasil atau tidak
     */
    abstract public function simpanSpesifikasi($produk_id, $data);

    /**
     * Mengedit spesifikasi khusus produk
     * @param int $produk_id ID produk
     * @param array $data Data spesifikasi baru
     * @return bool Berhasil atau tidak
     */
    abstract public function editSpesifikasi($produk_id, $data);

    // ============================================
    // CRUD Methods
    // ============================================

    /**
     * Mengambil semua data produk
     * 
     * @param string|null $jenis Filter berdasarkan jenis (laptop/smartphone)
     * @return array Daftar produk
     */
    public function getAll($jenis = null)
    {
        $query = "SELECT * FROM " . $this->table;
        if ($jenis) {
            $query .= " WHERE jenis = :jenis";
        }
        $query .= " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        if ($jenis) {
            $stmt->bindParam(':jenis', $jenis);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mengambil data produk berdasarkan ID
     * 
     * @param int $id ID produk
     * @return array|false Data produk atau false jika tidak ditemukan
     */
    public function getById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Menambah produk baru
     * 
     * @param array $data Data produk (nama, jenis, harga, stok, deskripsi)
     * @return int|false ID produk baru atau false jika gagal
     */
    public function tambah($data)
    {
        $query = "INSERT INTO " . $this->table . " 
                  (nama, jenis, harga, stok, deskripsi) 
                  VALUES (:nama, :jenis, :harga, :stok, :deskripsi)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama', $data['nama']);
        $stmt->bindParam(':jenis', $data['jenis']);
        $stmt->bindParam(':harga', $data['harga']);
        $stmt->bindParam(':stok', $data['stok'], PDO::PARAM_INT);
        $stmt->bindParam(':deskripsi', $data['deskripsi']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    /**
     * Mengedit data produk
     * 
     * @param int $id ID produk
     * @param array $data Data produk baru
     * @return bool Berhasil atau tidak
     */
    public function edit($id, $data)
    {
        $query = "UPDATE " . $this->table . " 
                  SET nama = :nama, harga = :harga, stok = :stok, deskripsi = :deskripsi 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama', $data['nama']);
        $stmt->bindParam(':harga', $data['harga']);
        $stmt->bindParam(':stok', $data['stok'], PDO::PARAM_INT);
        $stmt->bindParam(':deskripsi', $data['deskripsi']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Menghapus produk
     * 
     * @param int $id ID produk
     * @return bool Berhasil atau tidak
     */
    public function hapus($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Mengurangi stok produk (untuk transaksi keluar)
     * 
     * @param int $id ID produk
     * @param int $jumlah Jumlah pengurangan
     * @return bool Berhasil atau tidak
     */
    public function kurangiStok($id, $jumlah)
    {
        // Cek stok saat ini
        $produk = $this->getById($id);
        if (!$produk) {
            return false;
        }

        // Validasi: stok tidak boleh menjadi negatif
        if ($produk['stok'] - $jumlah < 0) {
            return false;
        }

        $stokBaru = $produk['stok'] - $jumlah;
        $query = "UPDATE " . $this->table . " SET stok = :stok WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':stok', $stokBaru, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Mengambil produk yang stoknya menipis (< 5)
     * Fitur peringatan otomatis "Stok Menipis"
     * 
     * @return array Daftar produk dengan stok < 5
     */
    public function cekStokMenipis()
    {
        $query = "SELECT * FROM " . $this->table . " WHERE stok < 5 ORDER BY stok ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Menghitung total produk
     * 
     * @return int Total produk
     */
    public function hitungTotal()
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total'];
    }

    /**
     * Menghitung total produk berdasarkan jenis
     * 
     * @param string $jenis Jenis produk (laptop/smartphone)
     * @return int Total produk
     */
    public function hitungByJenis($jenis)
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE jenis = :jenis";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':jenis', $jenis);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total'];
    }

    /**
     * Menghitung total nilai inventaris
     * 
     * @return float Total nilai inventaris
     */
    public function totalNilaiInventaris()
    {
        $query = "SELECT SUM(harga * stok) as total_nilai FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total_nilai'] ?? 0;
    }
}

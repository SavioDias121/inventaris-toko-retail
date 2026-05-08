<?php
/**
 * Class Transaksi
 * 
 * Mengelola data transaksi pengurangan stok.
 * Menerapkan prinsip-prinsip OOP dan berhubungan dengan class Produk.
 */
class Transaksi
{
    private $conn;
    private $table = 'transaksi';

    /**
     * Constructor - menerima koneksi database
     * 
     * @param PDO $db Objek koneksi database
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Mengambil semua data transaksi beserta detail produk (JOIN)
     * 
     * @param int $limit Batasan jumlah data yang diambil (opsional)
     * @return array Daftar transaksi
     */
    public function getAll($limit = null)
    {
        $query = "SELECT t.*, p.nama as nama_produk, p.jenis as jenis_produk 
                  FROM " . $this->table . " t 
                  INNER JOIN produk p ON t.produk_id = p.id 
                  ORDER BY t.tanggal_transaksi DESC";
        
        if ($limit) {
            $query .= " LIMIT " . (int)$limit;
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Menambah transaksi baru (pengurangan stok)
     * 
     * @param array $data Data transaksi (produk_id, jumlah, keterangan)
     * @return bool|string True jika berhasil, string error jika gagal
     */
    public function tambah($data)
    {
        try {
            // Mulai transaction database
            $this->conn->beginTransaction();

            // 1. Cek ketersediaan stok
            $queryCek = "SELECT stok, nama FROM produk WHERE id = :produk_id";
            $stmtCek = $this->conn->prepare($queryCek);
            $stmtCek->bindParam(':produk_id', $data['produk_id'], PDO::PARAM_INT);
            $stmtCek->execute();
            $produk = $stmtCek->fetch(PDO::FETCH_ASSOC);

            if (!$produk) {
                throw new Exception("Produk tidak ditemukan.");
            }

            if ($produk['stok'] < $data['jumlah']) {
                throw new Exception("Stok tidak mencukupi. Stok " . $produk['nama'] . " saat ini: " . $produk['stok']);
            }

            // 2. Simpan catatan transaksi
            $queryInsert = "INSERT INTO " . $this->table . " 
                            (produk_id, jumlah, jenis_transaksi, keterangan) 
                            VALUES (:produk_id, :jumlah, 'keluar', :keterangan)";
            
            $stmtInsert = $this->conn->prepare($queryInsert);
            $stmtInsert->bindParam(':produk_id', $data['produk_id'], PDO::PARAM_INT);
            $stmtInsert->bindParam(':jumlah', $data['jumlah'], PDO::PARAM_INT);
            $stmtInsert->bindParam(':keterangan', $data['keterangan']);
            $stmtInsert->execute();

            // 3. Kurangi stok produk
            $stokBaru = $produk['stok'] - $data['jumlah'];
            $queryUpdate = "UPDATE produk SET stok = :stok WHERE id = :produk_id";
            $stmtUpdate = $this->conn->prepare($queryUpdate);
            $stmtUpdate->bindParam(':stok', $stokBaru, PDO::PARAM_INT);
            $stmtUpdate->bindParam(':produk_id', $data['produk_id'], PDO::PARAM_INT);
            $stmtUpdate->execute();

            // Commit transaction
            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            // Rollback jika terjadi error
            $this->conn->rollBack();
            return $e->getMessage();
        }
    }

    /**
     * Menghitung total transaksi (jumlah item yang keluar)
     * 
     * @return int Total item terjual
     */
    public function getTotalTerjual()
    {
        $query = "SELECT SUM(jumlah) as total FROM " . $this->table . " WHERE jenis_transaksi = 'keluar'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total'] ?? 0;
    }

    /**
     * Mengambil rekap transaksi bulan ini
     * 
     * @return array Daftar transaksi bulan ini
     */
    public function getRekapBulanIni()
    {
        $query = "SELECT t.*, p.nama as nama_produk 
                  FROM " . $this->table . " t 
                  INNER JOIN produk p ON t.produk_id = p.id 
                  WHERE MONTH(t.tanggal_transaksi) = MONTH(CURRENT_DATE()) 
                  AND YEAR(t.tanggal_transaksi) = YEAR(CURRENT_DATE())
                  ORDER BY t.tanggal_transaksi DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

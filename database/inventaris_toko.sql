-- ============================================
-- DATABASE: inventaris_toko
-- Sistem Inventaris Toko Retail
-- PHP OOP & MySQL
-- ============================================

-- Buat database
CREATE DATABASE IF NOT EXISTS inventaris_toko;
USE inventaris_toko;

-- ============================================
-- Tabel: produk
-- Menyimpan data umum semua produk
-- ============================================
CREATE TABLE IF NOT EXISTS produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    jenis ENUM('laptop', 'smartphone') NOT NULL,
    harga DECIMAL(15, 2) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    deskripsi TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Tabel: laptop
-- Menyimpan spesifikasi khusus laptop
-- ============================================
CREATE TABLE IF NOT EXISTS laptop (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produk_id INT NOT NULL,
    processor VARCHAR(100) NOT NULL,
    ram VARCHAR(50) NOT NULL,
    storage VARCHAR(100) NOT NULL,
    ukuran_layar VARCHAR(50) NOT NULL,
    FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Tabel: smartphone
-- Menyimpan spesifikasi khusus smartphone
-- ============================================
CREATE TABLE IF NOT EXISTS smartphone (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produk_id INT NOT NULL,
    processor VARCHAR(100) NOT NULL,
    ram VARCHAR(50) NOT NULL,
    storage VARCHAR(100) NOT NULL,
    ukuran_layar VARCHAR(50) NOT NULL,
    kamera VARCHAR(100) NOT NULL,
    FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Tabel: transaksi
-- Menyimpan catatan transaksi pengurangan stok
-- ============================================
CREATE TABLE IF NOT EXISTS transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produk_id INT NOT NULL,
    jumlah INT NOT NULL,
    jenis_transaksi ENUM('keluar') NOT NULL DEFAULT 'keluar',
    tanggal_transaksi DATETIME DEFAULT CURRENT_TIMESTAMP,
    keterangan TEXT,
    FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Data Sample: Produk Laptop
-- ============================================
INSERT INTO produk (nama, jenis, harga, stok, deskripsi) VALUES
('ASUS ROG Strix G16', 'laptop', 18500000.00, 10, 'Laptop gaming performa tinggi dengan layar 16 inci'),
('Lenovo ThinkPad X1 Carbon', 'laptop', 22000000.00, 7, 'Laptop bisnis ultrabook ringan dan tangguh'),
('Acer Aspire 5', 'laptop', 8500000.00, 15, 'Laptop multimedia dengan harga terjangkau'),
('HP Pavilion 15', 'laptop', 12000000.00, 3, 'Laptop serba guna untuk kebutuhan sehari-hari'),
('Dell XPS 13', 'laptop', 19500000.00, 4, 'Laptop premium dengan desain ringkas');

-- Spesifikasi Laptop
INSERT INTO laptop (produk_id, processor, ram, storage, ukuran_layar) VALUES
(1, 'Intel Core i7-13650HX', '16 GB DDR5', '512 GB SSD NVMe', '16 inci FHD+'),
(2, 'Intel Core i7-1365U', '16 GB LPDDR5', '512 GB SSD NVMe', '14 inci 2.8K OLED'),
(3, 'AMD Ryzen 5 7530U', '8 GB DDR4', '512 GB SSD', '15.6 inci FHD IPS'),
(4, 'Intel Core i5-1335U', '8 GB DDR4', '256 GB SSD', '15.6 inci FHD'),
(5, 'Intel Core i7-1360P', '16 GB LPDDR5', '512 GB SSD NVMe', '13.4 inci FHD+');

-- ============================================
-- Data Sample: Produk Smartphone
-- ============================================
INSERT INTO produk (nama, jenis, harga, stok, deskripsi) VALUES
('Samsung Galaxy S24 Ultra', 'smartphone', 19999000.00, 8, 'Flagship Samsung dengan S Pen dan kamera AI'),
('iPhone 15 Pro Max', 'smartphone', 24999000.00, 2, 'Smartphone premium Apple dengan chip A17 Pro'),
('Xiaomi 14', 'smartphone', 7999000.00, 20, 'Smartphone flagship killer dengan kamera Leica'),
('OPPO Reno 11', 'smartphone', 5499000.00, 12, 'Smartphone mid-range dengan desain stylish'),
('Vivo V30 Pro', 'smartphone', 6999000.00, 1, 'Smartphone dengan kamera portrait terbaik di kelasnya');

-- Spesifikasi Smartphone
INSERT INTO smartphone (produk_id, processor, ram, storage, ukuran_layar, kamera) VALUES
(6, 'Snapdragon 8 Gen 3', '12 GB', '256 GB', '6.8 inci Dynamic AMOLED 2X', '200 MP + 50 MP + 12 MP + 10 MP'),
(7, 'Apple A17 Pro', '8 GB', '256 GB', '6.7 inci Super Retina XDR OLED', '48 MP + 12 MP + 12 MP'),
(8, 'Snapdragon 8 Gen 3', '12 GB', '256 GB', '6.36 inci LTPO AMOLED', '50 MP + 50 MP + 50 MP (Leica)'),
(9, 'MediaTek Dimensity 7050', '8 GB', '256 GB', '6.7 inci AMOLED', '50 MP + 32 MP + 8 MP'),
(10, 'MediaTek Dimensity 8200', '12 GB', '256 GB', '6.78 inci AMOLED', '50 MP + 50 MP + 8 MP');

-- ============================================
-- Data Sample: Transaksi
-- ============================================
INSERT INTO transaksi (produk_id, jumlah, jenis_transaksi, keterangan) VALUES
(1, 2, 'keluar', 'Penjualan ke pelanggan walk-in'),
(6, 3, 'keluar', 'Penjualan online marketplace'),
(3, 1, 'keluar', 'Penjualan ke pelanggan korporat'),
(8, 5, 'keluar', 'Penjualan promo akhir bulan'),
(10, 2, 'keluar', 'Penjualan ke reseller');

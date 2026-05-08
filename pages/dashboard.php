<?php
require_once '../config/Database.php';
require_once '../classes/Laptop.php';
require_once '../classes/Smartphone.php';
require_once '../classes/Transaksi.php';

// Inisialisasi koneksi DB
$database = new Database();
$db = $database->getConnection();

// Inisialisasi Object
$laptop = new Laptop($db);
$smartphone = new Smartphone($db);
$transaksi = new Transaksi($db);

// Mengambil Data Statistik
$totalLaptop = $laptop->hitungByJenis('laptop');
$totalSmartphone = $smartphone->hitungByJenis('smartphone');
$totalProduk = $laptop->hitungTotal(); // Method ini ada di parent Produk, bisa dipanggil dari object mana saja
$totalTerjual = $transaksi->getTotalTerjual();

// Mengambil Data Peringatan Stok Menipis (< 5) - CPMK102
$stokMenipis = $laptop->cekStokMenipis(); // Mengambil dari abstract class Produk

// Mengambil 5 Transaksi Terakhir
$recentTransaksi = $transaksi->getAll(5);

include_once '../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Dashboard Inventaris</h2>
    <span style="color: #6c757d;">
        <i class="fas fa-calendar-alt"></i> <?php echo date('l, d F Y'); ?>
    </span>
</div>

<!-- FITUR PENDETEKSI STOK MENIPIS (CPMK102) -->
<?php if (count($stokMenipis) > 0): ?>
    <div class="alert alert-danger">
        <h4><i class="fas fa-exclamation-triangle"></i> PERINGATAN: STOK MENIPIS!</h4>
        <p>Terdapat <strong><?php echo count($stokMenipis); ?></strong> barang dengan jumlah stok di bawah 5. Segera lakukan restock.</p>
        <ul style="margin-top: 10px; margin-bottom: 0;">
            <?php foreach ($stokMenipis as $item): ?>
                <li>
                    <strong><?php echo htmlspecialchars($item['nama']); ?></strong> 
                    (Stok tersisa: <span class="badge badge-danger"><?php echo $item['stok']; ?></span>)
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Statistik Card -->
<div class="grid-cards" style="margin-bottom: 30px;">
    <div class="card stat-card">
        <div class="stat-icon bg-primary">
            <i class="fas fa-boxes"></i>
        </div>
        <div class="stat-details">
            <p>Total Produk (Jenis)</p>
            <h3><?php echo $totalProduk; ?></h3>
        </div>
    </div>
    
    <div class="card stat-card">
        <div class="stat-icon bg-success">
            <i class="fas fa-laptop"></i>
        </div>
        <div class="stat-details">
            <p>Total Laptop</p>
            <h3><?php echo $totalLaptop; ?></h3>
        </div>
    </div>
    
    <div class="card stat-card">
        <div class="stat-icon bg-warning">
            <i class="fas fa-mobile-alt"></i>
        </div>
        <div class="stat-details">
            <p>Total Smartphone</p>
            <h3><?php echo $totalSmartphone; ?></h3>
        </div>
    </div>
    
    <div class="card stat-card">
        <div class="stat-icon bg-danger">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="stat-details">
            <p>Barang Terjual (Unit)</p>
            <h3><?php echo $totalTerjual; ?></h3>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
    <!-- Tabel Transaksi Terakhir -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 15px;">
            <h3 style="margin: 0;">Transaksi Terakhir</h3>
            <a href="transaksi/index.php" class="btn btn-sm btn-primary">Lihat Semua</a>
        </div>
        
        <?php if (count($recentTransaksi) > 0): ?>
            <div style="overflow-x: auto;">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentTransaksi as $trx): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($trx['tanggal_transaksi'])); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($trx['nama_produk']); ?></strong><br>
                                    <span style="font-size: 12px; color: #6c757d;"><?php echo ucfirst($trx['jenis_produk']); ?></span>
                                </td>
                                <td><span class="badge badge-warning">- <?php echo $trx['jumlah']; ?> unit</span></td>
                                <td><?php echo htmlspecialchars($trx['keterangan']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: #6c757d; padding: 20px 0;">Belum ada transaksi keluar.</p>
        <?php endif; ?>
    </div>

    <!-- Ringkasan Info Tambahan -->
    <div class="card">
        <h3 style="border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 15px; margin-top: 0;">Status Sistem</h3>
        <ul style="list-style-type: none; padding: 0; margin: 0;">
            <li style="padding: 10px 0; border-bottom: 1px solid #f8f9fa; display: flex; justify-content: space-between;">
                <span><i class="fas fa-check-circle" style="color: var(--success-color);"></i> Database Koneksi</span>
                <strong>OK</strong>
            </li>
            <li style="padding: 10px 0; border-bottom: 1px solid #f8f9fa; display: flex; justify-content: space-between;">
                <span><i class="fas fa-shield-alt" style="color: var(--primary-color);"></i> Sistem OOP</span>
                <strong>Aktif</strong>
            </li>
            <li style="padding: 10px 0; display: flex; justify-content: space-between;">
                <span><i class="fas fa-bell" style="color: var(--warning-color);"></i> Detektor Stok</span>
                <strong>Aktif (< 5)</strong>
            </li>
        </ul>
        <div style="margin-top: 20px; text-align: center;">
            <a href="produk/tambah.php" class="btn btn-primary" style="width: 100%;"><i class="fas fa-plus"></i> Tambah Produk Baru</a>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>

<?php
require_once '../../config/Database.php';
require_once '../../classes/Transaksi.php';

// Inisialisasi DB
$database = new Database();
$db = $database->getConnection();

$transaksiObj = new Transaksi($db);
$dataTransaksi = $transaksiObj->getAll();

include_once '../../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Data Transaksi Keluar</h2>
    <a href="tambah.php" class="btn btn-danger"><i class="fas fa-minus-circle"></i> Catat Transaksi Baru</a>
</div>

<?php if (isset($_GET['status'])): ?>
    <?php if ($_GET['status'] == 'success'): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Transaksi berhasil dicatat dan stok telah dikurangi!
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="card">
    <div style="overflow-x: auto;">
        <table class="table-custom">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">Tanggal</th>
                    <th width="25%">Produk</th>
                    <th width="10%">Jenis</th>
                    <th width="10%">Jumlah</th>
                    <th width="35%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($dataTransaksi) > 0): ?>
                    <?php $no = 1; foreach ($dataTransaksi as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo date('d M Y, H:i', strtotime($row['tanggal_transaksi'])); ?></td>
                            <td><strong><?php echo htmlspecialchars($row['nama_produk']); ?></strong></td>
                            <td><?php echo ucfirst($row['jenis_produk']); ?></td>
                            <td>
                                <span class="badge badge-warning" style="font-size: 14px;">
                                    <i class="fas fa-arrow-down"></i> <?php echo $row['jumlah']; ?> unit
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px;">Belum ada catatan transaksi.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once '../../includes/footer.php'; ?>

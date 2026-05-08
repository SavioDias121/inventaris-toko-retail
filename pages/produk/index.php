<?php
require_once '../../config/Database.php';
require_once '../../classes/Laptop.php';
require_once '../../classes/Smartphone.php';

// Inisialisasi koneksi DB
$database = new Database();
$db = $database->getConnection();

// Inisialisasi Object
$laptop = new Laptop($db);
$smartphone = new Smartphone($db);

// Filter Jenis
$filterJenis = isset($_GET['jenis']) ? $_GET['jenis'] : '';

// Mengambil Data Produk
if ($filterJenis == 'laptop') {
    $dataProduk = $laptop->getAllDenganSpesifikasi();
} elseif ($filterJenis == 'smartphone') {
    $dataProduk = $smartphone->getAllDenganSpesifikasi();
} else {
    // Gabungkan data
    $dataLaptop = $laptop->getAllDenganSpesifikasi();
    $dataSmartphone = $smartphone->getAllDenganSpesifikasi();
    $dataProduk = array_merge($dataLaptop, $dataSmartphone);
    
    // Sort descending by created_at (karena array_merge menghilangkan order dari query)
    usort($dataProduk, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
}

include_once '../../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Data Produk Inventaris</h2>
    <a href="tambah.php" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Produk Baru</a>
</div>

<!-- Alert Notifikasi -->
<?php if (isset($_GET['status'])): ?>
    <?php if ($_GET['status'] == 'success'): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Data produk berhasil disimpan!
        </div>
    <?php elseif ($_GET['status'] == 'deleted'): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Data produk berhasil dihapus!
        </div>
    <?php elseif ($_GET['status'] == 'error'): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan saat memproses data.
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="card">
    <!-- Filter -->
    <div style="margin-bottom: 20px; display: flex; gap: 10px;">
        <a href="index.php" class="btn <?php echo empty($filterJenis) ? 'btn-primary' : 'btn-sm'; ?>" style="<?php echo !empty($filterJenis) ? 'background: #e9ecef; color: #495057; border: 1px solid #ced4da; padding: 0.375rem 0.75rem;' : ''; ?>">Semua</a>
        <a href="index.php?jenis=laptop" class="btn <?php echo $filterJenis == 'laptop' ? 'btn-primary' : 'btn-sm'; ?>" style="<?php echo $filterJenis != 'laptop' ? 'background: #e9ecef; color: #495057; border: 1px solid #ced4da; padding: 0.375rem 0.75rem;' : ''; ?>">Laptop</a>
        <a href="index.php?jenis=smartphone" class="btn <?php echo $filterJenis == 'smartphone' ? 'btn-primary' : 'btn-sm'; ?>" style="<?php echo $filterJenis != 'smartphone' ? 'background: #e9ecef; color: #495057; border: 1px solid #ced4da; padding: 0.375rem 0.75rem;' : ''; ?>">Smartphone</a>
    </div>

    <div style="overflow-x: auto;">
        <table class="table-custom">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="20%">Nama Produk</th>
                    <th width="10%">Jenis</th>
                    <th width="15%">Harga</th>
                    <th width="10%">Stok</th>
                    <th width="25%">Spesifikasi Utama</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($dataProduk) > 0): ?>
                    <?php $no = 1; foreach ($dataProduk as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($row['nama']); ?></strong>
                            </td>
                            <td>
                                <?php if ($row['jenis'] == 'laptop'): ?>
                                    <span class="badge" style="background-color: #e2e3e5; color: #383d41;"><i class="fas fa-laptop"></i> Laptop</span>
                                <?php else: ?>
                                    <span class="badge" style="background-color: #d1ecf1; color: #0c5460;"><i class="fas fa-mobile-alt"></i> Smartphone</span>
                                <?php endif; ?>
                            </td>
                            <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                            <td>
                                <?php 
                                // Peringatan Stok Menipis < 5
                                if ($row['stok'] < 5): 
                                ?>
                                    <span class="badge badge-danger" title="Stok Menipis!"><?php echo $row['stok']; ?> <i class="fas fa-exclamation-circle"></i></span>
                                <?php else: ?>
                                    <span class="badge badge-success"><?php echo $row['stok']; ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size: 13px;">
                                <?php if ($row['jenis'] == 'laptop'): ?>
                                    <div><strong>CPU:</strong> <?php echo htmlspecialchars($row['processor']); ?></div>
                                    <div><strong>RAM:</strong> <?php echo htmlspecialchars($row['ram']); ?> | <strong>Storage:</strong> <?php echo htmlspecialchars($row['storage']); ?></div>
                                <?php else: ?>
                                    <div><strong>RAM/ROM:</strong> <?php echo htmlspecialchars($row['ram']); ?> / <?php echo htmlspecialchars($row['storage']); ?></div>
                                    <div><strong>Kamera:</strong> <?php echo htmlspecialchars($row['kamera']); ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit.php?id=<?php echo $row['id']; ?>&jenis=<?php echo $row['jenis']; ?>" class="btn btn-sm btn-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="hapus.php" method="POST" style="display: inline-block;" onsubmit="return confirmDelete();">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="jenis" value="<?php echo $row['jenis']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px;">Belum ada data produk.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once '../../includes/footer.php'; ?>

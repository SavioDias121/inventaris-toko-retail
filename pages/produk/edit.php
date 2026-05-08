<?php
require_once '../../config/Database.php';
require_once '../../classes/Laptop.php';
require_once '../../classes/Smartphone.php';
require_once '../../classes/Validator.php';

// Inisialisasi DB
$database = new Database();
$db = $database->getConnection();

// Cek ID
if (!isset($_GET['id']) || !isset($_GET['jenis'])) {
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];
$jenis_param = $_GET['jenis'];

// Ambil data produk
$produkObj = ($jenis_param == 'laptop') ? new Laptop($db) : new Smartphone($db);
$dataUtama = $produkObj->getById($id);

if (!$dataUtama) {
    header("Location: index.php");
    exit();
}

// Ambil spesifikasi
$dataSpesifikasi = $produkObj->getSpesifikasi($id);

$error_messages = [];

// Proses Submit Form Edit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitasi
    $nama = Validator::sanitize($_POST['nama']);
    $harga = Validator::sanitize($_POST['harga']);
    $stok = Validator::sanitize($_POST['stok']);
    $deskripsi = Validator::sanitize($_POST['deskripsi']);
    
    $processor = Validator::sanitize($_POST['processor']);
    $ram = Validator::sanitize($_POST['ram']);
    $storage = Validator::sanitize($_POST['storage']);
    $ukuran_layar = Validator::sanitize($_POST['ukuran_layar']);

    // Validasi Field Wajib
    $requiredFields = [
        'nama' => $nama, 'harga' => $harga, 'stok' => $stok,
        'processor' => $processor, 'ram' => $ram, 'storage' => $storage, 'ukuran_layar' => $ukuran_layar
    ];
    $error_messages = Validator::validateRequired($requiredFields);

    // Validasi khusus Smartphone
    $kamera = '';
    if ($jenis_param == 'smartphone') {
        $kamera = Validator::sanitize($_POST['kamera']);
        if (empty(trim($kamera))) {
            $error_messages[] = "Field Kamera wajib diisi untuk Smartphone.";
        }
    }

    // Validasi Stok (tidak boleh negatif)
    if (!Validator::validateStok($stok)) {
        $error_messages[] = "Error: Nilai stok tidak boleh negatif atau tidak valid!";
    }

    // Validasi Harga
    if (!Validator::validateHarga($harga)) {
        $error_messages[] = "Error: Harga harus berupa angka positif!";
    }

    if (empty($error_messages)) {
        $dataUpdateUtama = [
            'nama' => $nama,
            'harga' => $harga,
            'stok' => $stok,
            'deskripsi' => $deskripsi
        ];

        try {
            // Update tabel produk
            if ($produkObj->edit($id, $dataUpdateUtama)) {
                $dataUpdateSpesifikasi = [
                    'processor' => $processor,
                    'ram' => $ram,
                    'storage' => $storage,
                    'ukuran_layar' => $ukuran_layar
                ];

                if ($jenis_param == 'smartphone') {
                    $dataUpdateSpesifikasi['kamera'] = $kamera;
                }

                // Update spesifikasi
                if ($produkObj->editSpesifikasi($id, $dataUpdateSpesifikasi)) {
                    header("Location: index.php?status=success");
                    exit();
                } else {
                    $error_messages[] = "Berhasil update data utama, tapi gagal update spesifikasi.";
                }
            } else {
                $error_messages[] = "Gagal mengupdate data produk.";
            }
        } catch (Exception $e) {
            $error_messages[] = "Terjadi kesalahan sistem: " . $e->getMessage();
        }
    }
}

include_once '../../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Edit Produk: <?php echo htmlspecialchars($dataUtama['nama']); ?></h2>
    <a href="index.php" class="btn btn-sm" style="background: #e9ecef; color: #495057; border: 1px solid #ced4da;"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

<?php if (!empty($error_messages)): ?>
    <div class="alert alert-danger">
        <strong><i class="fas fa-exclamation-circle"></i> Validasi Error:</strong>
        <ul>
            <?php foreach ($error_messages as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card">
    <form action="" method="POST">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            
            <!-- Kolom Kiri -->
            <div>
                <h4 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px;">Informasi Umum</h4>
                
                <div class="form-group">
                    <label>Jenis Produk</label>
                    <input type="text" class="form-control" value="<?php echo ucfirst($jenis_param); ?>" readonly style="background-color: #e9ecef; cursor: not-allowed;">
                </div>

                <div class="form-group">
                    <label for="nama">Nama Produk *</label>
                    <input type="text" name="nama" id="nama" class="form-control" value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : htmlspecialchars($dataUtama['nama']); ?>" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label for="harga">Harga (Rp) *</label>
                        <input type="number" name="harga" id="harga" class="form-control" min="1" value="<?php echo isset($_POST['harga']) ? htmlspecialchars($_POST['harga']) : $dataUtama['harga']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="stok">Stok Awal *</label>
                        <input type="number" name="stok" id="stok" class="form-control" min="0" value="<?php echo isset($_POST['stok']) ? htmlspecialchars($_POST['stok']) : $dataUtama['stok']; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4"><?php echo isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : htmlspecialchars($dataUtama['deskripsi']); ?></textarea>
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div>
                <h4 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px;">Spesifikasi Teknis</h4>
                
                <div class="form-group">
                    <label for="processor">Processor / Chipset *</label>
                    <input type="text" name="processor" id="processor" class="form-control" value="<?php echo isset($_POST['processor']) ? htmlspecialchars($_POST['processor']) : htmlspecialchars($dataSpesifikasi['processor']); ?>" required>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label for="ram">RAM *</label>
                        <input type="text" name="ram" id="ram" class="form-control" value="<?php echo isset($_POST['ram']) ? htmlspecialchars($_POST['ram']) : htmlspecialchars($dataSpesifikasi['ram']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="storage">Storage / ROM *</label>
                        <input type="text" name="storage" id="storage" class="form-control" value="<?php echo isset($_POST['storage']) ? htmlspecialchars($_POST['storage']) : htmlspecialchars($dataSpesifikasi['storage']); ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="ukuran_layar">Ukuran Layar *</label>
                    <input type="text" name="ukuran_layar" id="ukuran_layar" class="form-control" value="<?php echo isset($_POST['ukuran_layar']) ? htmlspecialchars($_POST['ukuran_layar']) : htmlspecialchars($dataSpesifikasi['ukuran_layar']); ?>" required>
                </div>
                
                <?php if ($jenis_param == 'smartphone'): ?>
                <div class="form-group">
                    <label for="kamera">Spesifikasi Kamera *</label>
                    <input type="text" name="kamera" id="kamera" class="form-control" value="<?php echo isset($_POST['kamera']) ? htmlspecialchars($_POST['kamera']) : htmlspecialchars($dataSpesifikasi['kamera']); ?>" required>
                </div>
                <?php endif; ?>
                
                <div style="margin-top: 30px; text-align: right;">
                    <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Update Produk</button>
                </div>
            </div>
            
        </div>
    </form>
</div>

<?php include_once '../../includes/footer.php'; ?>

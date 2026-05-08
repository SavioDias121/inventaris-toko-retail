<?php
require_once '../../config/Database.php';
require_once '../../classes/Laptop.php';
require_once '../../classes/Smartphone.php';
require_once '../../classes/Validator.php';

// Inisialisasi DB
$database = new Database();
$db = $database->getConnection();

$error_messages = [];

// Proses Submit Form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitasi Input (CPMK093)
    $jenis = Validator::sanitize($_POST['jenis']);
    $nama = Validator::sanitize($_POST['nama']);
    $harga = Validator::sanitize($_POST['harga']);
    $stok = Validator::sanitize($_POST['stok']);
    $deskripsi = Validator::sanitize($_POST['deskripsi']);
    
    // Spesifikasi Umum
    $processor = Validator::sanitize($_POST['processor']);
    $ram = Validator::sanitize($_POST['ram']);
    $storage = Validator::sanitize($_POST['storage']);
    $ukuran_layar = Validator::sanitize($_POST['ukuran_layar']);

    // Validasi Field Wajib (CPMK093)
    $requiredFields = [
        'nama' => $nama, 'jenis' => $jenis, 'harga' => $harga, 'stok' => $stok,
        'processor' => $processor, 'ram' => $ram, 'storage' => $storage, 'ukuran_layar' => $ukuran_layar
    ];
    $error_messages = Validator::validateRequired($requiredFields);

    // Validasi khusus Smartphone
    $kamera = '';
    if ($jenis == 'smartphone') {
        $kamera = Validator::sanitize($_POST['kamera']);
        if (empty(trim($kamera))) {
            $error_messages[] = "Field Kamera wajib diisi untuk Smartphone.";
        }
    }

    // Validasi Stok - MENOLAK STOK NEGATIF (CPMK093)
    if (!Validator::validateStok($stok)) {
        $error_messages[] = "Error: Nilai stok tidak boleh negatif atau tidak valid!";
    }

    // Validasi Harga
    if (!Validator::validateHarga($harga)) {
        $error_messages[] = "Error: Harga harus berupa angka positif!";
    }

    // Jika tidak ada error, proses simpan
    if (empty($error_messages)) {
        $dataUmum = [
            'nama' => $nama,
            'jenis' => $jenis,
            'harga' => $harga,
            'stok' => $stok,
            'deskripsi' => $deskripsi
        ];

        // Instansiasi Object sesuai jenis menggunakan OOP Polymorphism semu
        $produk = null;
        if ($jenis == 'laptop') {
            $produk = new Laptop($db);
        } else {
            $produk = new Smartphone($db);
        }

        try {
            // 1. Simpan ke tabel produk (parent)
            $produk_id = $produk->tambah($dataUmum);

            if ($produk_id) {
                // 2. Simpan spesifikasi ke tabel child (laptop/smartphone)
                $dataSpesifikasi = [
                    'processor' => $processor,
                    'ram' => $ram,
                    'storage' => $storage,
                    'ukuran_layar' => $ukuran_layar
                ];

                if ($jenis == 'smartphone') {
                    $dataSpesifikasi['kamera'] = $kamera;
                }

                // Memanggil method override dari child class
                if ($produk->simpanSpesifikasi($produk_id, $dataSpesifikasi)) {
                    header("Location: index.php?status=success");
                    exit();
                } else {
                    // Rollback manual jika gagal simpan spesifikasi
                    $produk->hapus($produk_id);
                    $error_messages[] = "Gagal menyimpan detail spesifikasi.";
                }
            } else {
                $error_messages[] = "Gagal menyimpan data utama produk.";
            }
        } catch (Exception $e) {
            $error_messages[] = "Terjadi kesalahan sistem: " . $e->getMessage();
        }
    }
}

include_once '../../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Tambah Produk Baru</h2>
    <a href="index.php" class="btn btn-sm" style="background: #e9ecef; color: #495057; border: 1px solid #ced4da;"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

<?php if (!empty($error_messages)): ?>
    <div class="alert alert-danger">
        <strong><i class="fas fa-exclamation-circle"></i> Validasi Error:</strong>
        <ul style="margin-bottom: 0; margin-top: 5px;">
            <?php foreach ($error_messages as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card">
    <form action="" method="POST" id="formProduk">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            
            <!-- Kolom Kiri: Informasi Umum -->
            <div>
                <h4 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px;">Informasi Umum</h4>
                
                <div class="form-group">
                    <label for="jenis">Jenis Produk *</label>
                    <select name="jenis" id="jenis" class="form-control" onchange="toggleSpesifikasi()" required>
                        <option value="">-- Pilih Jenis --</option>
                        <option value="laptop" <?php echo (isset($_POST['jenis']) && $_POST['jenis'] == 'laptop') ? 'selected' : ''; ?>>Laptop</option>
                        <option value="smartphone" <?php echo (isset($_POST['jenis']) && $_POST['jenis'] == 'smartphone') ? 'selected' : ''; ?>>Smartphone</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="nama">Nama Produk *</label>
                    <input type="text" name="nama" id="nama" class="form-control" value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label for="harga">Harga (Rp) *</label>
                        <input type="number" name="harga" id="harga" class="form-control" min="1" value="<?php echo isset($_POST['harga']) ? htmlspecialchars($_POST['harga']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="stok">Stok Awal * <small style="color:red">(Tidak boleh minus)</small></label>
                        <input type="number" name="stok" id="stok" class="form-control" min="0" value="<?php echo isset($_POST['stok']) ? htmlspecialchars($_POST['stok']) : '0'; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4"><?php echo isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : ''; ?></textarea>
                </div>
            </div>

            <!-- Kolom Kanan: Spesifikasi -->
            <div id="spesifikasiSection" style="<?php echo isset($_POST['jenis']) && $_POST['jenis'] != '' ? 'display:block;' : 'display:none; opacity: 0.5; pointer-events: none;'; ?>">
                <h4 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px;">Spesifikasi Teknis</h4>
                
                <div class="form-group">
                    <label for="processor">Processor / Chipset *</label>
                    <input type="text" name="processor" id="processor" class="form-control" value="<?php echo isset($_POST['processor']) ? htmlspecialchars($_POST['processor']) : ''; ?>" required>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label for="ram">RAM *</label>
                        <input type="text" name="ram" id="ram" class="form-control" placeholder="Contoh: 8GB" value="<?php echo isset($_POST['ram']) ? htmlspecialchars($_POST['ram']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="storage">Storage / ROM *</label>
                        <input type="text" name="storage" id="storage" class="form-control" placeholder="Contoh: 256GB SSD" value="<?php echo isset($_POST['storage']) ? htmlspecialchars($_POST['storage']) : ''; ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="ukuran_layar">Ukuran Layar *</label>
                    <input type="text" name="ukuran_layar" id="ukuran_layar" class="form-control" placeholder="Contoh: 15.6 inch" value="<?php echo isset($_POST['ukuran_layar']) ? htmlspecialchars($_POST['ukuran_layar']) : ''; ?>" required>
                </div>
                
                <!-- Khusus Smartphone -->
                <div class="form-group" id="kameraGroup" style="<?php echo (isset($_POST['jenis']) && $_POST['jenis'] == 'smartphone') ? 'display:block;' : 'display:none;'; ?>">
                    <label for="kamera">Spesifikasi Kamera *</label>
                    <input type="text" name="kamera" id="kamera" class="form-control" placeholder="Contoh: 50MP Utama + 12MP Ultra-wide" value="<?php echo isset($_POST['kamera']) ? htmlspecialchars($_POST['kamera']) : ''; ?>">
                </div>
                
                <div style="margin-top: 30px; text-align: right;">
                    <button type="submit" class="btn btn-primary btn-lg" style="padding: 10px 30px;"><i class="fas fa-save"></i> Simpan Produk</button>
                </div>
            </div>
            
        </div>
    </form>
</div>

<script>
function toggleSpesifikasi() {
    const jenis = document.getElementById('jenis').value;
    const specSection = document.getElementById('spesifikasiSection');
    const kameraGroup = document.getElementById('kameraGroup');
    const kameraInput = document.getElementById('kamera');
    
    if (jenis === '') {
        specSection.style.opacity = '0.5';
        specSection.style.pointerEvents = 'none';
        specSection.style.display = 'none';
    } else {
        specSection.style.opacity = '1';
        specSection.style.pointerEvents = 'auto';
        specSection.style.display = 'block';
        
        if (jenis === 'smartphone') {
            kameraGroup.style.display = 'block';
            kameraInput.setAttribute('required', 'required');
        } else {
            kameraGroup.style.display = 'none';
            kameraInput.removeAttribute('required');
        }
    }
}
</script>

<?php include_once '../../includes/footer.php'; ?>

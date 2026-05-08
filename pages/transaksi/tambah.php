<?php
require_once '../../config/Database.php';
require_once '../../classes/Transaksi.php';
require_once '../../classes/Laptop.php'; // Mengambil method getAll dari abstract class Produk
require_once '../../classes/Validator.php';

$database = new Database();
$db = $database->getConnection();

$transaksiObj = new Transaksi($db);
// Menggunakan object Laptop sekadar untuk memanggil method parent getAll() 
$produkObj = new Laptop($db); 
$daftarProduk = $produkObj->getAll();

$error_messages = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $produk_id = (int)$_POST['produk_id'];
    $jumlah = Validator::sanitize($_POST['jumlah']);
    $keterangan = Validator::sanitize($_POST['keterangan']);

    if (empty($produk_id)) {
        $error_messages[] = "Pilih produk yang akan ditransaksikan.";
    }

    if (!Validator::validateStok($jumlah) || (int)$jumlah <= 0) {
        $error_messages[] = "Jumlah transaksi harus angka positif lebih besar dari 0.";
    }

    if (empty($keterangan)) {
        $error_messages[] = "Keterangan transaksi wajib diisi.";
    }

    if (empty($error_messages)) {
        $dataTransaksi = [
            'produk_id' => $produk_id,
            'jumlah' => (int)$jumlah,
            'keterangan' => $keterangan
        ];

        // Lakukan transaksi (akan mengupdate stok)
        $result = $transaksiObj->tambah($dataTransaksi);

        if ($result === true) {
            header("Location: index.php?status=success");
            exit();
        } else {
            $error_messages[] = $result; // Result berisi string Exception message (misal: stok tidak cukup)
        }
    }
}

include_once '../../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Catat Transaksi Keluar (Pengurangan Stok)</h2>
    <a href="index.php" class="btn btn-sm" style="background: #e9ecef; color: #495057; border: 1px solid #ced4da;"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

<?php if (!empty($error_messages)): ?>
    <div class="alert alert-danger">
        <strong><i class="fas fa-exclamation-circle"></i> Error:</strong>
        <ul style="margin-bottom: 0; margin-top: 5px;">
            <?php foreach ($error_messages as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <form action="" method="POST">
        <div class="form-group">
            <label for="produk_id">Pilih Produk *</label>
            <select name="produk_id" id="produk_id" class="form-control" required onchange="tampilkanStok()">
                <option value="">-- Pilih Produk --</option>
                <?php foreach ($daftarProduk as $prod): ?>
                    <option value="<?php echo $prod['id']; ?>" data-stok="<?php echo $prod['stok']; ?>" <?php echo (isset($_POST['produk_id']) && $_POST['produk_id'] == $prod['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($prod['nama']); ?> (<?php echo ucfirst($prod['jenis']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <small style="color: #6c757d; margin-top: 5px; display: block;" id="infoStok">Pilih produk untuk melihat sisa stok.</small>
        </div>

        <div class="form-group">
            <label for="jumlah">Jumlah Keluar *</label>
            <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" value="<?php echo isset($_POST['jumlah']) ? htmlspecialchars($_POST['jumlah']) : '1'; ?>" required>
        </div>

        <div class="form-group">
            <label for="keterangan">Keterangan / Alasan *</label>
            <textarea name="keterangan" id="keterangan" class="form-control" rows="3" placeholder="Contoh: Penjualan tunai, rusak, dll." required><?php echo isset($_POST['keterangan']) ? htmlspecialchars($_POST['keterangan']) : ''; ?></textarea>
        </div>

        <div style="margin-top: 20px; text-align: right;">
            <button type="submit" class="btn btn-danger btn-lg"><i class="fas fa-check"></i> Proses Transaksi</button>
        </div>
    </form>
</div>

<script>
function tampilkanStok() {
    const select = document.getElementById('produk_id');
    const option = select.options[select.selectedIndex];
    const infoStok = document.getElementById('infoStok');
    const inputJumlah = document.getElementById('jumlah');
    
    if (select.value !== '') {
        const stokTersedia = parseInt(option.getAttribute('data-stok'));
        
        infoStok.innerHTML = `Sisa Stok: <strong>${stokTersedia} unit</strong>`;
        inputJumlah.setAttribute('max', stokTersedia);
        
        if (stokTersedia === 0) {
            infoStok.innerHTML += ` <span style="color:red;">(Stok habis, tidak bisa transaksi)</span>`;
            inputJumlah.setAttribute('disabled', 'disabled');
        } else {
            inputJumlah.removeAttribute('disabled');
        }
    } else {
        infoStok.innerHTML = 'Pilih produk untuk melihat sisa stok.';
        inputJumlah.removeAttribute('max');
        inputJumlah.removeAttribute('disabled');
    }
}

// Jalankan saat load jika ada option terpilih (karena validasi error submit)
window.onload = function() {
    if (document.getElementById('produk_id').value !== '') {
        tampilkanStok();
    }
};
</script>

<?php include_once '../../includes/footer.php'; ?>

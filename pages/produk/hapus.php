<?php
require_once '../../config/Database.php';
require_once '../../classes/Laptop.php';
require_once '../../classes/Smartphone.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && isset($_POST['jenis'])) {
    $id = $_POST['id'];
    $jenis = $_POST['jenis'];

    $produk = null;
    if ($jenis == 'laptop') {
        $produk = new Laptop($db);
    } else {
        $produk = new Smartphone($db);
    }

    // Method hapus() ada di abstract class Produk, otomatis akan menghapus di child table (CASCADE)
    if ($produk->hapus($id)) {
        header("Location: index.php?status=deleted");
    } else {
        header("Location: index.php?status=error");
    }
} else {
    header("Location: index.php");
}
exit();

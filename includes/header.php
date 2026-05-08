<?php
session_start();
// Helper untuk menentukan path base url
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
if (substr($base_url, -1) == '/') {
    $base_url = substr($base_url, 0, -1);
}

// Helper untuk active menu
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Inventaris Toko Retail</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo str_replace('/pages', '', str_replace('/produk', '', str_replace('/transaksi', '', $base_url))); ?>/assets/css/style.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-store"></i> RetailSys
        </div>
        
        <?php
        // Fix path resolution for menu active state depending on where the script is run from
        $base_path = str_replace('/pages', '', str_replace('/produk', '', str_replace('/transaksi', '', $base_url)));
        ?>
        
        <a href="<?php echo $base_path; ?>/pages/dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <i class="fas fa-home"></i> Dashboard
        </a>
        
        <a href="<?php echo $base_path; ?>/pages/produk/index.php" class="<?php echo ($current_dir == 'produk') ? 'active' : ''; ?>">
            <i class="fas fa-box"></i> Kelola Produk
        </a>
        
        <a href="<?php echo $base_path; ?>/pages/transaksi/index.php" class="<?php echo ($current_dir == 'transaksi') ? 'active' : ''; ?>">
            <i class="fas fa-shopping-cart"></i> Transaksi Keluar
        </a>
    </div>

    <!-- Main Content -->
    <div class="main-content">

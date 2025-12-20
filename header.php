<?php
// header.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Bangunan Grosir</title>
    <link rel="stylesheet" href="style.css">
    <!-- Font Awesome untuk Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<header class="navbar">
    <div class="navbar-container">
        <!-- Logo -->
        <a href="index.php" class="logo-text">
            <i class="fas fa-hammer"></i> 
            <span>TokoBangunan</span>
        </a>

        <!-- Search Bar (Visual Only) -->
        <div class="search-container">
            <input type="text" class="search-box" placeholder="Cari bahan bangunan...">
        </div>
        
        <!-- Menu Navigasi -->
        <nav class="nav-links">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="keranjang.php" class="cart-icon" title="Keranjang">
                    <i class="fas fa-shopping-cart"></i>
                </a>
                <a href="pesanan_saya.php" title="Pesanan Saya">
                    <i class="fas fa-file-invoice"></i>
                </a>
                <a href="profil.php" title="Profil">
                    <div style="display:flex; align-items:center; gap:5px;">
                        <i class="fas fa-user-circle"></i>
                        <span><?php echo htmlspecialchars(explode(' ', $_SESSION['user_nama'])[0]); ?></span>
                    </div>
                </a>
                <a href="logout.php" style="font-weight:bold;">Keluar</a>
            <?php else: ?>
                <a href="login.php">Masuk</a>
                <span>|</span>
                <a href="register.php">Daftar</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main>
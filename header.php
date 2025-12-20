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
    <title>MATRIA.MART - Material Bangunan Modern</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<header class="navbar">
    <div class="navbar-container">
        <a href="index.php" class="logo-text">
            <i class="fas fa-layer-group"></i> 
            <span>MATRIA.MART</span>
        </a>

        <form action="index.php" method="GET" class="search-container">
            <input type="text" name="q" class="search-box" placeholder="Cari pasir, semen, paku..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
            <button type="submit" style="background:none; border:none; position:absolute; right:10px; top:10px; color:#666; cursor:pointer;">
                <i class="fas fa-search"></i>
            </button>
        </form>
        
        <nav class="nav-links">
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Fitur Pesan Baru -->
                <a href="pesan.php" title="Hubungi Admin">
                    <i class="fas fa-comment-dots"></i>
                </a>
                
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
<?php
// header.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// LOGIKA NOTIFIKASI PESAN USER
$unread_msg_user = 0;
if (isset($_SESSION['user_id']) && isset($conn)) {
    $uid = $_SESSION['user_id'];
    
    // Hitung pesan yang belum dibaca dari admin
    $q_notif = $conn->query("SELECT COUNT(*) as total FROM pesan WHERE id_user = $uid AND pengirim='admin' AND is_read=0");
    if ($q_notif) {
        $unread_msg_user = $q_notif->fetch_assoc()['total'];
    }

    // Helper untuk foto profil
    if (!isset($_SESSION['user_foto'])) {
        $q_foto = $conn->query("SELECT foto FROM user WHERE id = $uid");
        if ($q_foto && $q_foto->num_rows > 0) {
            $f = $q_foto->fetch_assoc();
            $_SESSION['user_foto'] = $f['foto'];
        }
    }
}
$header_foto = !empty($_SESSION['user_foto']) ? 'images/users/' . $_SESSION['user_foto'] : '';

// Cek halaman saat ini untuk menyembunyikan search bar
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MATRIA.MART - Material Bangunan Modern</title>
    <link rel="stylesheet" href="style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        /* CSS Tambahan untuk Badge Notifikasi */
        .nav-links a { position: relative; }
        .badge-user {
            position: absolute;
            top: -5px;
            right: -8px;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
            font-weight: bold;
        }
        
        /* STYLE KHUSUS TOMBOL AUTH (MASUK & DAFTAR) */
        .btn-nav-auth {
            padding: 8px 25px;
            font-size: 14px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            letter-spacing: 0.5px;
            
            /* Default State: Transparan dengan Border */
            background: transparent;
            color: white; /* Teks Putih agar terlihat di navbar gelap */
            border: 1px solid var(--primary-color);
        }

        /* Hover State: Background Muncul dengan Animasi */
        .btn-nav-auth:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(255, 87, 34, 0.4);
            border-color: var(--primary-color);
        }

        /* Jarak antar tombol */
        .btn-register {
            margin-left: 10px;
        }
        
        /* Penyesuaian Mobile */
        @media (max-width: 768px) {
            .badge-user { top: 0; right: 0; }
            .btn-nav-auth { width: 100%; margin: 5px 0; }
            .btn-register { margin-left: 0; }
        }
    </style>
</head>
<body>

<header class="navbar">
    <div class="navbar-container">
        <!-- LOGO -->
        <a href="index.php" class="logo-text">
            <i class="fas fa-cubes" style="color:var(--primary-color);"></i>
            MATRIA<span>.MART</span>
        </a>

        <!-- SEARCH BAR (Disembunyikan di halaman login & register) -->
        <?php if ($current_page != 'login.php' && $current_page != 'register.php'): ?>
        <form action="index.php" method="GET" class="search-container">
            <input type="text" name="q" class="search-box" placeholder="Cari semen, cat, paku..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
            <button type="submit" class="search-btn">
                <i class="fas fa-search"></i>
            </button>
        </form>
        <?php else: ?>
        <!-- Spacer kosong agar logo dan nav tetap di ujung (Flexbox) -->
        <div style="flex: 1;"></div>
        <?php endif; ?>
        
        <!-- NAVIGATION -->
        <nav class="nav-links">
            <?php if (isset($_SESSION['user_id'])): ?>
                
                <a href="pesan.php" title="Chat Admin">
                    <i class="fas fa-comment-dots"></i> 
                    <span>Chat</span>
                    <!-- BADGE NOTIFIKASI -->
                    <?php if ($unread_msg_user > 0): ?>
                        <span class="badge-user"><?php echo $unread_msg_user; ?></span>
                    <?php endif; ?>
                </a>
                
                <a href="keranjang.php" class="cart-icon" title="Keranjang">
                    <i class="fas fa-shopping-cart"></i>
                </a>

                <a href="pesanan_saya.php" title="Pesanan Saya">
                    <i class="fas fa-receipt"></i>
                </a>

                <a href="profil.php" title="Profil">
                    <div style="width: 35px; height: 35px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 1px solid rgba(255,255,255,0.3);">
                        <?php if (!empty($header_foto) && file_exists($header_foto)): ?>
                            <img src="<?php echo $header_foto; ?>" alt="Foto" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <i class="fas fa-user" style="font-size: 16px; color: #fff;"></i>
                        <?php endif; ?>
                    </div>
                    <span style="font-size: 14px; font-weight: 500;"><?php echo htmlspecialchars(explode(' ', $_SESSION['user_nama'])[0]); ?></span>
                </a>
                
                <a href="logout.php" style="color: #e74c3c; margin-left: 5px;" title="Keluar">
                    <i class="fas fa-sign-out-alt"></i>
                </a>

            <?php else: ?>
                <!-- TOMBOL MASUK & DAFTAR (Style Seragam) -->
                <a href="login.php" class="btn-nav-auth btn-login">Masuk</a>
                <a href="register.php" class="btn-nav-auth btn-register">Daftar</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main>
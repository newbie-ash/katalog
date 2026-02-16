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
        /* === LOGIKA TEMA GELAP / TERANG (DARK MODE) === */
        :root {
            /* Default Light Mode Colors (Sesuai style.css asli) */
            --primary-color: #ff5722;
            --bg-body: #f4f6f8;         /* Background asli */
            --pattern-color: #e0e0e0;   /* Warna tekstur asli (abu muda) */
            --bg-navbar: #15283c;       /* Biru gelap industrial */
            --bg-card: #ffffff;
            --text-main: #333333;
            --text-muted: #666666;
            --border-color: #eeeeee;
            --input-bg: rgba(255,255,255,0.1); /* Transparan di navbar */
            --input-text-nav: #ffffff;
            --shadow-soft: 0 4px 6px rgba(0,0,0,0.05);
            --icon-color: #cfd8dc;      /* Warna ikon di navbar gelap */
        }

        /* Dark Mode Overrides */
        [data-theme="dark"] {
            --bg-body: #121212;         /* Hitam pekat */
            --pattern-color: #2c2c2c;   /* Tekstur abu gelap (agar tidak kontras tajam) */
            --bg-navbar: #000000;       /* Navbar hitam */
            --bg-card: #1e1e1e;         /* Kartu gelap */
            --text-main: #e0e0e0;       /* Teks terang */
            --text-muted: #aaaaaa;
            --border-color: #333333;
            --input-bg: #2c2c2c;
            --input-text-nav: #e0e0e0;
            --shadow-soft: 0 4px 6px rgba(0,0,0,0.5);
            --icon-color: #e0e0e0;
        }

        /* Terapkan Variabel ke Elemen Dasar */
        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            transition: background-color 0.3s ease, color 0.3s ease;
            
            /* MEMPERTAHANKAN TEKSTUR (Pattern) */
            /* Menggunakan variabel agar warna tekstur ikut berubah saat mode gelap */
            background-image: radial-gradient(var(--pattern-color) 1px, transparent 1px);
            background-size: 20px 20px;
        }

        .navbar {
            background: var(--bg-navbar) !important;
            box-shadow: var(--shadow-soft);
            transition: background 0.3s ease;
        }
        
        .logo-text {
            color: white !important; /* Logo selalu putih di navbar gelap */
        }

        /* Kartu Produk & Container */
        .card, .product-card, .box-white, .card-container, .auth-box {
            background-color: var(--bg-card) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-main);
        }

        h1, h2, h3, h4, h5, h6 {
            color: var(--text-main);
        }

        /* Input Search di Navbar */
        .search-box {
            background: var(--input-bg) !important;
            color: var(--input-text-nav) !important;
            border: 1px solid transparent;
        }
        
        /* Input Form Biasa (Login/Register) */
        .form-group input {
            background-color: var(--bg-card);
            color: var(--text-main);
            border-color: var(--border-color);
        }

        /* Nav Icons & Links */
        .nav-links a {
            color: var(--icon-color);
        }
        .nav-links a:hover {
            color: var(--primary-color);
        }

        /* Tombol Toggle Tema */
        .theme-toggle-btn {
            background: none;
            border: none;
            color: var(--icon-color);
            font-size: 18px;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            margin-right: 15px;
            transition: background 0.3s;
        }
        .theme-toggle-btn:hover {
            background-color: rgba(255,255,255, 0.1);
            color: white;
        }

        /* === EXISTING STYLES === */
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
            background: transparent;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }

        .btn-nav-auth:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(255, 87, 34, 0.4);
        }

        .btn-register { margin-left: 10px; }
        
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
        <div style="flex: 1;"></div>
        <?php endif; ?>
        
        <!-- NAVIGATION -->
        <nav class="nav-links">
            
            <!-- TOMBOL GANTI TEMA (BARU) -->
            <button id="theme-toggle" class="theme-toggle-btn" title="Ganti Tema">
                <i class="fas fa-moon"></i>
            </button>

            <?php if (isset($_SESSION['user_id'])): ?>
                
                <a href="pesan.php" title="Chat Admin">
                    <i class="fas fa-comment-dots"></i> 
                    <span>Chat</span>
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
                    <div style="width: 35px; height: 35px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 1px solid rgba(255,255,255,0.2);">
                        <?php if (!empty($header_foto) && file_exists($header_foto)): ?>
                            <img src="<?php echo $header_foto; ?>" alt="Foto" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <i class="fas fa-user" style="font-size: 16px; color: var(--icon-color);"></i>
                        <?php endif; ?>
                    </div>
                    <span style="font-size: 14px; font-weight: 500; color: #fff; margin-left:5px;"><?php echo htmlspecialchars(explode(' ', $_SESSION['user_nama'])[0]); ?></span>
                </a>
                
                <a href="logout.php" style="color: #ff6b6b; margin-left: 5px;" title="Keluar">
                    <i class="fas fa-sign-out-alt"></i>
                </a>

            <?php else: ?>
                <a href="login.php" class="btn-nav-auth btn-login">Masuk</a>
                <a href="register.php" class="btn-nav-auth btn-register">Daftar</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<!-- SCRIPT LOGIKA DARK MODE -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('theme-toggle');
        const icon = toggleBtn.querySelector('i');
        const body = document.body;
        
        // 1. Cek LocalStorage
        const savedTheme = localStorage.getItem('user_theme');
        
        // 2. Terapkan Tema
        if (savedTheme === 'dark') {
            body.setAttribute('data-theme', 'dark');
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun'); // Ikon Matahari saat mode gelap
        } else {
            body.removeAttribute('data-theme');
            icon.classList.remove('fa-sun');
            icon.classList.add('fa-moon'); // Ikon Bulan saat mode terang
        }

        // 3. Event Listener Klik
        toggleBtn.addEventListener('click', function() {
            if (body.getAttribute('data-theme') === 'dark') {
                // Switch ke Light
                body.removeAttribute('data-theme');
                localStorage.setItem('user_theme', 'light');
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            } else {
                // Switch ke Dark
                body.setAttribute('data-theme', 'dark');
                localStorage.setItem('user_theme', 'dark');
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            }
        });
    });
</script>

<main>
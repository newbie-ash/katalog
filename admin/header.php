<?php
// admin/header.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Toko Bangunan</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        /* DEFINISI VARIABEL WARNA (THEME VARIABLES) */
        :root {
            --primary-color: #ff5722;
            --sidebar-width: 250px;
            
            /* Default Light Mode Colors */
            --bg-body: #f4f6f9;
            --bg-card: #ffffff;
            --bg-sidebar: #343a40;
            --text-primary: #333333;
            --text-secondary: #777777;
            --border-color: #eeeeee;
            --shadow-color: rgba(0,0,0,0.05);
            --hover-bg: #f8f9fa;
        }

        /* Dark Mode Overrides */
        [data-theme="dark"] {
            --bg-body: #18191a;       /* Dark Grey Background */
            --bg-card: #242526;       /* Slightly Lighter Grey for Cards */
            --bg-sidebar: #000000;    /* Pure Black Sidebar */
            --text-primary: #e4e6eb;  /* Light Text */
            --text-secondary: #b0b3b8;/* Muted Text */
            --border-color: #3e4042;
            --shadow-color: rgba(0,0,0,0.3);
            --hover-bg: #3a3b3c;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-primary);
            overflow-x: hidden;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Sidebar Styling */
        .sidebar {
            height: 100vh;
            width: var(--sidebar-width);
            position: fixed;
            top: 0;
            left: 0;
            background-color: var(--bg-sidebar);
            padding-top: 20px;
            transition: all 0.3s;
            z-index: 1000;
        }

        .sidebar a {
            padding: 15px 25px;
            text-decoration: none;
            font-size: 16px;
            color: #d1d1d1; /* Keep sidebar text light always */
            display: block;
            transition: 0.3s;
            border-left: 4px solid transparent;
        }

        .sidebar a:hover, .sidebar a.active {
            background-color: #495057;
            color: #fff;
            border-left: 4px solid var(--primary-color);
        }

        .sidebar .brand {
            font-size: 20px;
            color: #fff;
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .sidebar .brand i {
            color: var(--primary-color);
        }

        /* Content Styling */
        .content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: all 0.3s;
        }

        /* Top Navbar */
        .top-navbar {
            background-color: var(--bg-card);
            padding: 15px 30px;
            box-shadow: 0 2px 5px var(--shadow-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .top-navbar h4 {
            margin: 0;
            color: var(--text-primary);
            font-weight: 500;
        }

        .user-profile {
            display: flex;
            align-items: center;
        }

        .user-profile span {
            margin-right: 10px;
            font-weight: 500;
            color: var(--text-primary);
        }
        
        .user-profile img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Theme Toggle Button */
        #theme-toggle {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-primary);
            font-size: 1.2rem;
            margin-right: 20px;
            padding: 5px;
            border-radius: 50%;
            transition: background-color 0.2s;
        }
        
        #theme-toggle:hover {
            background-color: var(--hover-bg);
        }

        /* Cards */
        .stat-card {
            background: var(--bg-card);
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px var(--shadow-color);
            margin-bottom: 20px;
            border-left: 4px solid var(--primary-color);
            transition: transform 0.2s, background-color 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card h3 {
            font-size: 28px;
            font-weight: bold;
            color: var(--text-primary);
            margin-bottom: 5px;
        }

        .stat-card p {
            color: var(--text-secondary);
            margin: 0;
            font-size: 14px;
        }
        
        .stat-card .icon {
            font-size: 40px;
            color: var(--border-color); /* Updated for visibility in dark mode */
            position: absolute;
            right: 20px;
            top: 20px;
        }

        /* Responsive */
        @media screen and (max-width: 768px) {
            .sidebar {
                width: 0;
                padding-top: 60px; /* Space for close button if needed */
                overflow-x: hidden;
            }
            .content {
                margin-left: 0;
            }
            .sidebar.show {
                width: var(--sidebar-width);
            }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="brand">
        <i class="fas fa-warehouse"></i> Admin Panel
    </div>
    <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
    </a>
    <a href="manage_produk.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_produk.php' ? 'active' : ''; ?>">
        <i class="fas fa-box mr-2"></i> Produk
    </a>
    <a href="manage_pesanan.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_pesanan.php' ? 'active' : ''; ?>">
        <i class="fas fa-shopping-cart mr-2"></i> Pesanan
    </a>
    <a href="manage_ongkir.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_ongkir.php' ? 'active' : ''; ?>">
        <i class="fas fa-truck mr-2"></i> Ongkir
    </a>
    <a href="manage_banner.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_banner.php' ? 'active' : ''; ?>">
        <i class="fas fa-image mr-2"></i> Banner
    </a>
     <a href="manage_pesan.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_pesan.php' ? 'active' : ''; ?>">
        <i class="fas fa-envelope mr-2"></i> Pesan Masuk
    </a>
    
    <!-- GROUP NAVIGASI TAMBAHAN DIHAPUS SESUAI PERMINTAAN -->
    <!-- (Tombol Lihat Toko dan Kembali sudah dihapus dari sini) -->

    <a href="../logout.php" style="color: #ff6b6b; margin-top: 20px; border-top: 1px solid #495057;">
        <i class="fas fa-sign-out-alt mr-2"></i> Keluar
    </a>
</div>

<!-- Content Wrapper -->
<div class="content">
    <!-- Top Navbar -->
    <div class="top-navbar">
        <div class="d-flex align-items-center">
            <button class="btn btn-light d-md-none mr-3" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>
                <?php 
                $page = basename($_SERVER['PHP_SELF'], ".php");
                if($page == 'dashboard') echo "Dashboard Overview";
                elseif($page == 'manage_produk') echo "Manajemen Produk";
                elseif($page == 'manage_pesanan') echo "Manajemen Pesanan";
                elseif($page == 'manage_ongkir') echo "Manajemen Ongkos Kirim";
                elseif($page == 'manage_banner') echo "Manajemen Banner";
                elseif($page == 'manage_pesan') echo "Pesan Masuk";
                else echo "Admin Panel";
                ?>
            </h4>
        </div>
        
        <div class="user-profile">
            <!-- TEMA TOGGLE BUTTON -->
            <button id="theme-toggle" title="Ganti Tema">
                <i class="fas fa-moon"></i>
            </button>

            <span>Halo, Admin <?php echo htmlspecialchars($_SESSION['user_nama'] ?? 'User'); ?></span>
            <div style="width: 35px; height: 35px; background-color: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                <?php echo strtoupper(substr($_SESSION['user_nama'] ?? 'A', 0, 1)); ?>
            </div>
        </div>
    </div>

    <!-- Script Logika Dark Mode -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('theme-toggle');
            const icon = toggleBtn.querySelector('i');
            const body = document.body;
            
            // 1. Cek apakah ada simpanan preferensi di LocalStorage
            const savedTheme = localStorage.getItem('admin_theme');
            
            // 2. Terapkan tema yang tersimpan
            if (savedTheme === 'dark') {
                body.setAttribute('data-theme', 'dark');
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun'); // Ubah ikon jadi matahari saat gelap
            } else {
                body.removeAttribute('data-theme');
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }

            // 3. Event Listener saat tombol diklik
            toggleBtn.addEventListener('click', function() {
                if (body.getAttribute('data-theme') === 'dark') {
                    // Switch ke Light Mode
                    body.removeAttribute('data-theme');
                    localStorage.setItem('admin_theme', 'light');
                    icon.classList.remove('fa-sun');
                    icon.classList.add('fa-moon');
                } else {
                    // Switch ke Dark Mode
                    body.setAttribute('data-theme', 'dark');
                    localStorage.setItem('admin_theme', 'dark');
                    icon.classList.remove('fa-moon');
                    icon.classList.add('fa-sun');
                }
            });
        });
    </script>
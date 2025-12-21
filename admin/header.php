<?php
// admin/header.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// LOGIKA NOTIFIKASI PESAN BARU
$unread_msg = 0;
if (isset($conn)) {
    $q_notif = $conn->query("SELECT COUNT(*) as total FROM pesan WHERE pengirim='user' AND is_read=0");
    if ($q_notif) {
        $unread_msg = $q_notif->fetch_assoc()['total'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - MATRIA.MART</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts (Roboto) -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        /* --- RESET & GLOBAL --- */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Roboto', sans-serif; }
        body { background-color: #f8f8f8; display: flex; min-height: 100vh; overflow-x: hidden; }
        a { text-decoration: none; color: inherit; }
        ul { list-style: none; }

        /* --- SIDEBAR (Dark Blue) --- */
        .sidebar {
            width: 250px;
            background-color: #15283c;
            color: #fff;
            position: fixed;
            height: 100vh;
            transition: 0.3s;
            z-index: 100;
        }

        .sidebar-header {
            background-color: #214162;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .user-pic {
            width: 50px;
            height: 50px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #15283c;
            font-size: 24px;
        }

        .user-info h4 { font-size: 16px; font-weight: 500; margin-bottom: 5px; }
        .user-info p { font-size: 12px; color: #1ed085; display: flex; align-items: center; gap: 5px; }
        .user-info p::before { content: ''; width: 8px; height: 8px; background: #1ed085; border-radius: 50%; display: block; }

        .sidebar-menu { padding: 20px 0; }
        .menu-header { padding: 10px 25px; font-size: 12px; color: #aab7c5; text-transform: uppercase; letter-spacing: 1px; }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 25px;
            color: #b2c0d0;
            font-size: 14px;
            transition: 0.3s;
            border-left: 3px solid transparent;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: #0f1e2e;
            color: #fff;
            border-left-color: #ff5722;
        }
        
        .sidebar-menu a i { margin-right: 15px; width: 20px; text-align: center; font-size: 16px; }
        
        /* Badge Notifikasi */
        .badge-nav { 
            background: #ff5722; 
            color: white; 
            padding: 3px 8px; 
            border-radius: 12px; 
            font-size: 11px; 
            font-weight: bold;
            margin-left: auto; 
            box-shadow: 0 0 5px rgba(255, 87, 34, 0.5);
        }

        /* --- MAIN CONTENT --- */
        .main-content {
            flex: 1;
            margin-left: 250px;
            width: calc(100% - 250px);
            display: flex;
            flex-direction: column;
        }

        /* --- TOP HEADER --- */
        .topbar {
            background: #fff;
            height: 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 99;
        }

        .logo-area {
            font-size: 20px;
            font-weight: bold;
            color: #15283c;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logo-area span { color: #ff5722; }

        .top-icons a { margin-left: 20px; color: #555; font-size: 18px; position: relative; }
        .top-icons .btn-logout { 
            background: #ff5722; color: white; padding: 5px 15px; 
            border-radius: 4px; font-size: 14px; margin-left: 20px; 
        }

        /* --- CONTENT WRAPPER --- */
        .page-content { padding: 30px; min-height: 85vh; }
        .page-title { font-size: 24px; color: #333; margin-bottom: 25px; font-weight: 500; }

        /* --- RESPONSIVE --- */
        @media (max-width: 768px) {
            .sidebar { left: -250px; }
            .sidebar.active { left: 0; }
            .main-content { margin-left: 0; width: 100%; }
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<nav class="sidebar">
    <div class="sidebar-header">
        <div class="user-pic">
            <i class="fas fa-user"></i>
        </div>
        <div class="user-info">
            <h4><?php echo htmlspecialchars(explode(' ', $_SESSION['user_nama'])[0]); ?></h4>
            <p>Online</p>
        </div>
    </div>
    
    <div class="sidebar-menu">
        <div class="menu-header">General</div>
        <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt" style="color: #ff9800;"></i> Dashboard
        </a>
        <a href="manage_produk.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_produk.php' ? 'active' : ''; ?>">
            <i class="fas fa-box" style="color: #2196f3;"></i> Data Produk
        </a>
        <a href="manage_banner.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_banner.php' ? 'active' : ''; ?>">
            <i class="fas fa-images" style="color: #9b59b6;"></i> Kelola Banner
        </a>
        <!-- MENU BARU -->
        <a href="manage_ongkir.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_ongkir.php' ? 'active' : ''; ?>">
            <i class="fas fa-truck" style="color: #3498db;"></i> Kelola Ongkir
        </a>
        <a href="manage_pesanan.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_pesanan.php' ? 'active' : ''; ?>">
            <i class="fas fa-file-invoice" style="color: #4caf50;"></i> Data Pesanan
        </a>
        <a href="manage_pesan.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_pesan.php' ? 'active' : ''; ?>">
            <i class="fas fa-comments" style="color: #e91e63;"></i> Chat Pelanggan
            <?php if ($unread_msg > 0): ?>
                <span class="badge-nav"><?php echo $unread_msg; ?></span>
            <?php endif; ?>
        </a>
    </div>
</nav>

<!-- MAIN CONTENT WRAPPER -->
<div class="main-content">
    
    <!-- TOP HEADER -->
    <header class="topbar">
        <div class="logo-area">
            <i class="fas fa-layer-group"></i>
            MATRIA<span>ADMIN</span>
        </div>
        <div class="top-icons">
            <a href="manage_pesanan.php" title="Pesanan Baru"><i class="fas fa-bell"></i></a>
            <a href="manage_pesan.php" title="Pesan Masuk">
                <i class="fas fa-envelope"></i>
                <?php if ($unread_msg > 0): ?>
                    <span style="position: absolute; top: -5px; right: -8px; background: #e74c3c; color: white; border-radius: 50%; width: 15px; height: 15px; font-size: 9px; display: flex; align-items: center; justify-content: center; border: 2px solid white;"><?php echo $unread_msg; ?></span>
                <?php endif; ?>
            </a>
            <a href="../logout.php" class="btn-logout">Keluar <i class="fas fa-sign-out-alt"></i></a>
        </div>
    </header>

    <!-- CONTENT AREA STARTS -->
    <div class="page-content">
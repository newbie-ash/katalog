<?php
// admin/header.php (KHUSUS ADMIN)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cek Keamanan: Tendang jika bukan admin
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
    <title>Admin Panel - Toko Bangunan</title>
    <!-- Mundur satu folder (../) untuk akses style.css utama -->
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Override warna navbar khusus Admin agar beda dengan User */
        .navbar { 
            background: linear-gradient(-180deg, #2c3e50, #34495e); 
        }
        /* Penyesuaian container admin */
        .admin-menu a { 
            margin-left: 15px; 
            color: #ecf0f1; 
            font-size: 0.9rem;
        }
        .admin-menu a:hover { 
            color: #3498db; 
        }
    </style>
</head>
<body>

<header class="navbar">
    <div class="navbar-container">
        <!-- Logo Admin -->
        <a href="dashboard.php" class="logo-text">
            <i class="fas fa-user-shield"></i>
            <span>ADMIN PANEL</span>
        </a>
        
        <!-- Menu Navigasi Admin -->
        <nav class="nav-links admin-menu">
            <a href="dashboard.php" title="Dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="manage_produk.php" title="Kelola Produk"><i class="fas fa-box"></i> Produk</a>
            <a href="manage_pesanan.php" title="Kelola Pesanan"><i class="fas fa-file-invoice"></i> Pesanan</a>
        </nav>

        <!-- User Actions -->
        <div class="user-actions" style="display:flex; align-items:center; gap:15px; font-size:0.9rem;">
            <span style="color: #bdc3c7;">Halo, <?php echo htmlspecialchars($_SESSION['user_nama']); ?></span>
            <a href="../logout.php" class="btn btn-danger" style="padding: 5px 15px; font-size: 12px;">Keluar</a>
        </div>
    </div>
</header>
<main>
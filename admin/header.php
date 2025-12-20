<?php
// admin/header.php (KHUSUS ADMIN)
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
    <title>Admin - MATRIA.MART</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .navbar { background: linear-gradient(-180deg, #2c3e50, #34495e); }
        .admin-menu a { margin-left: 15px; color: #ecf0f1; font-size: 0.9rem; }
        .admin-menu a:hover { color: #3498db; }
    </style>
</head>
<body>

<header class="navbar">
    <div class="navbar-container">
        <a href="dashboard.php" class="logo-text">
            <i class="fas fa-layer-group"></i>
            <span>MATRIA ADMIN</span>
        </a>
        
        <nav class="nav-links admin-menu">
            <a href="dashboard.php" title="Dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="manage_produk.php" title="Kelola Produk"><i class="fas fa-box"></i> Produk</a>
            <a href="manage_pesanan.php" title="Kelola Pesanan"><i class="fas fa-file-invoice"></i> Pesanan</a>
            
            <!-- Menu Pesan Baru -->
            <a href="manage_pesan.php" title="Chat User"><i class="fas fa-comments"></i> Chat</a>
        </nav>

        <div class="user-actions" style="display:flex; align-items:center; gap:15px; font-size:0.9rem;">
            <span style="color: #bdc3c7;">Halo, <?php echo htmlspecialchars($_SESSION['user_nama']); ?></span>
            <a href="../logout.php" class="btn btn-danger" style="padding: 5px 15px; font-size: 12px;">Keluar</a>
        </div>
    </div>
</header>
<main>
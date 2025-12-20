<?php
// admin/dashboard.php
include '../db_koneksi.php';
include 'header.php';

// Statistik
$total_produk = $conn->query("SELECT COUNT(*) as total FROM produk")->fetch_assoc()['total'];
$total_user = $conn->query("SELECT COUNT(*) as total FROM user WHERE role='pembeli'")->fetch_assoc()['total'];
$pesanan_pending = $conn->query("SELECT COUNT(*) as total FROM pesanan WHERE status='Pending'")->fetch_assoc()['total'];
?>

<div class="container">
    <div style="background: white; padding: 20px; border-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
        <h2>Dashboard Admin</h2>
        <div class="product-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); margin-top: 20px;">
            
            <div style="background: #3498db; color: white; padding: 20px; border-radius: 4px; text-align: center;">
                <i class="fas fa-box fa-3x" style="opacity: 0.5;"></i>
                <h1><?php echo $total_produk; ?></h1>
                <p>Total Produk</p>
                <a href="manage_produk.php" style="color: white; text-decoration: underline;">Kelola</a>
            </div>

            <div style="background: #2ecc71; color: white; padding: 20px; border-radius: 4px; text-align: center;">
                <i class="fas fa-users fa-3x" style="opacity: 0.5;"></i>
                <h1><?php echo $total_user; ?></h1>
                <p>Pelanggan Terdaftar</p>
            </div>

            <div style="background: #e74c3c; color: white; padding: 20px; border-radius: 4px; text-align: center;">
                <i class="fas fa-file-invoice-dollar fa-3x" style="opacity: 0.5;"></i>
                <h1><?php echo $pesanan_pending; ?></h1>
                <p>Pesanan Perlu Diproses</p>
                <a href="manage_pesanan.php" style="color: white; text-decoration: underline;">Lihat Pesanan</a>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
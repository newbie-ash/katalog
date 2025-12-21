<?php
// pesanan_saya.php
include_once 'db_koneksi.php';
include_once 'header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location='login.php';</script>";
    exit;
}

$id_user = $_SESSION['user_id'];
$query = "SELECT * FROM pesanan WHERE id_user = $id_user ORDER BY id DESC";
$result = $conn->query($query);
?>

<div class="container">
    <h2 style="color:white; text-shadow: 1px 1px 2px black;">Pesanan Saya</h2>
    
    <div style="display: flex; flex-direction: column; gap: 20px;">
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="card-container" style="padding: 0; overflow: hidden;">
                <!-- Header Card -->
                <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <span style="font-weight: bold; color: #555;">No. Pesanan: #<?php echo $row['id']; ?></span>
                        <span style="margin: 0 10px; color: #ddd;">|</span>
                        <span style="font-size: 13px; color: #777;"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></span>
                    </div>
                    <?php 
                        $st = $row['status'];
                        $bg = '#95a5a6';
                        if($st=='Pending') $bg='#f39c12';
                        elseif($st=='Menunggu Konfirmasi') $bg='#3498db';
                        elseif($st=='Dikemas') $bg='#9b59b6';
                        elseif($st=='Dikirim') $bg='#1abc9c';
                        elseif($st=='Selesai') $bg='#27ae60';
                        elseif($st=='Dibatalkan') $bg='#e74c3c';
                    ?>
                    <span style="background: <?php echo $bg; ?>; color: white; padding: 4px 10px; border-radius: 15px; font-size: 12px; font-weight: bold;">
                        <?php echo $st; ?>
                    </span>
                </div>

                <!-- Body Card (Detail Produk) -->
                <div style="padding: 20px;">
                    <?php
                    $id_pesanan = $row['id'];
                    // Query Detail Items
                    $q_detail = $conn->query("SELECT dp.*, p.nama_barang, p.gambar FROM detail_pesanan dp JOIN produk p ON dp.id_produk = p.id WHERE dp.id_pesanan = $id_pesanan");
                    
                    while($item = $q_detail->fetch_assoc()):
                        // LOGIKA CEK ULASAN
                        // Cek di database apakah user sudah memberi ulasan untuk produk ini di pesanan ini
                        $id_produk = $item['id_produk'];
                        $cek_review = $conn->query("SELECT id FROM ulasan WHERE id_pesanan = $id_pesanan AND id_produk = $id_produk");
                        $sudah_ulas = ($cek_review->num_rows > 0);
                    ?>
                        <div style="display: flex; gap: 15px; margin-bottom: 15px; align-items: center; flex-wrap: wrap;">
                            <img src="images/<?php echo $item['gambar']; ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #eee;">
                            <div style="flex: 1; min-width: 200px;">
                                <div style="font-weight: 500; color: #333;"><?php echo htmlspecialchars($item['nama_barang']); ?></div>
                                <div style="font-size: 13px; color: #777;"><?php echo $item['qty']; ?> x Rp <?php echo number_format($item['harga_deal'], 0, ',', '.'); ?></div>
                            </div>
                            
                            <!-- TOMBOL REVIEW -->
                            <!-- Hanya muncul jika Status Selesai -->
                            <?php if ($st == 'Selesai'): ?>
                                <?php if (!$sudah_ulas): ?>
                                    <a href="beri_ulasan.php?pesanan=<?php echo $id_pesanan; ?>&produk=<?php echo $id_produk; ?>" class="btn btn-primary" style="padding: 5px 15px; font-size: 12px; border-radius: 20px; text-decoration: none;">
                                        <i class="fas fa-star"></i> Beri Ulasan
                                    </a>
                                <?php else: ?>
                                    <span style="font-size: 12px; color: #27ae60; background: #e8f5e9; padding: 5px 10px; border-radius: 20px;">
                                        <i class="fas fa-check"></i> Sudah Diulas
                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                    
                    <hr style="border: 0; border-top: 1px dashed #eee; margin: 15px 0;">
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                        <div style="font-size: 14px;">Total Bayar: <strong style="color: var(--primary-color);">Rp <?php echo number_format($row['total_bayar'], 0, ',', '.'); ?></strong></div>
                        
                        <?php if($st == 'Pending'): ?>
                            <a href="pembayaran.php?id=<?php echo $row['id']; ?>" class="btn btn-primary" style="padding: 8px 20px;">Bayar Sekarang</a>
                        <?php elseif($st == 'Dikirim'): ?>
                            <!-- Tombol Konfirmasi Terima -->
                            <form method="POST" onsubmit="return confirm('Apakah barang sudah diterima dengan baik?');" style="margin: 0;">
                                <input type="hidden" name="terima_pesanan" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn" style="background: #27ae60; color: white; padding: 8px 20px; border-radius: 5px;">Pesanan Diterima</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="card-container" style="text-align: center; padding: 50px;">
            <i class="fas fa-shopping-bag fa-4x" style="color: #ddd; margin-bottom: 20px;"></i>
            <p>Belum ada riwayat pesanan.</p>
            <a href="index.php" class="btn btn-primary">Belanja Sekarang</a>
        </div>
    <?php endif; ?>
    </div>
</div>

<?php 
// Logika Sederhana Konfirmasi Terima Barang
if (isset($_POST['terima_pesanan'])) {
    $id_p = intval($_POST['terima_pesanan']);
    // Update status ke Selesai
    $conn->query("UPDATE pesanan SET status='Selesai' WHERE id=$id_p AND id_user=$id_user");
    echo "<script>window.location='pesanan_saya.php';</script>";
}

include_once 'footer.php'; 
?>
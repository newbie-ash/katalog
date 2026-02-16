<?php
// pesanan_saya.php
include_once 'db_koneksi.php';
include_once 'header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location='login.php';</script>";
    exit;
}

$id_user = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM pesanan WHERE id_user = $id_user ORDER BY id DESC");
?>

<div class="container">
    <h2 style="color:white; text-shadow: 1px 1px 2px black;">Pesanan Saya</h2>
    
    <div style="display: flex; flex-direction: column; gap: 20px;">
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="card-container" style="padding: 0; overflow: hidden;">
                <!-- Header Status -->
                <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-weight: bold;">#<?php echo $row['id']; ?> | <?php echo date('d M Y', strtotime($row['tanggal'])); ?></span>
                    <span style="background: #3498db; color: white; padding: 4px 10px; border-radius: 15px; font-size: 12px;"><?php echo $row['status']; ?></span>
                </div>

                <!-- Detail Produk -->
                <div style="padding: 20px;">
                    <?php
                    $id_pesanan = $row['id'];
                    $q_detail = $conn->query("SELECT dp.*, p.nama_barang, p.gambar FROM detail_pesanan dp JOIN produk p ON dp.id_produk = p.id WHERE dp.id_pesanan = $id_pesanan");
                    while($item = $q_detail->fetch_assoc()):
                    ?>
                        <div style="display: flex; gap: 15px; margin-bottom: 15px; align-items: center;">
                            <img src="images/<?php echo $item['gambar']; ?>" style="width: 50px; height: 50px; object-fit: cover;">
                            <div>
                                <div style="font-weight: 500;"><?php echo htmlspecialchars($item['nama_barang']); ?></div>
                                <div style="font-size: 13px; color: #777;"><?php echo $item['qty']; ?> x Rp <?php echo number_format($item['harga_deal'], 0, ',', '.'); ?></div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    
                    <hr style="margin: 15px 0; border-top: 1px dashed #eee;">
                    
                    <!-- Rincian Total -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                        <div style="font-size: 13px; color: #666;">
                            <div>Subtotal: Rp <?php echo number_format($row['total_bayar'] - $row['ongkir'], 0, ',', '.'); ?></div>
                            <div>Ongkir: Rp <?php echo number_format($row['ongkir'], 0, ',', '.'); ?></div>
                            <div style="font-weight: bold; color: #333; font-size: 15px; margin-top: 5px;">Total: Rp <?php echo number_format($row['total_bayar'], 0, ',', '.'); ?></div>
                        </div>
                        
                        <?php if($row['status'] == 'Pending'): ?>
                            <a href="pembayaran.php?id=<?php echo $row['id']; ?>" class="btn btn-primary" style="padding: 5px 15px;">Bayar</a>
                        <?php elseif($row['status'] == 'Dikirim'): ?>
                            <form method="POST">
                                <input type="hidden" name="terima" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn" style="background: #27ae60; color: white;">Terima</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center; color:white;">Belum ada pesanan.</p>
    <?php endif; ?>
    </div>
</div>

<?php 
if(isset($_POST['terima'])) {
    $conn->query("UPDATE pesanan SET status='Selesai' WHERE id=".intval($_POST['terima']));
    echo "<script>window.location='pesanan_saya.php';</script>";
}
include_once 'footer.php'; 
?>
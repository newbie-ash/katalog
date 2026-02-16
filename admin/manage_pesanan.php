<?php
// admin/manage_pesanan.php
include '../db_koneksi.php';
include 'header.php';

// Update Status
if (isset($_POST['update_status'])) {
    $id = intval($_POST['id_pesanan']);
    $status = $_POST['status'];
    $conn->query("UPDATE pesanan SET status='$status' WHERE id=$id");
    echo "<script>window.location='manage_pesanan.php';</script>";
}

$sql = "SELECT p.*, u.nama as nama_pemesan, u.no_hp FROM pesanan p JOIN user u ON p.id_user = u.id ORDER BY p.id DESC";
$result = $conn->query($sql);
?>

<h2 class="page-title">Kelola Pesanan</h2>

<style>
    .card-box { background: #fff; padding: 25px; border-radius: 5px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
    .table { width: 100%; border-collapse: collapse; }
    .table th, .table td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; vertical-align: top; }
    .badge-status { padding: 4px 8px; border-radius: 4px; font-size: 11px; color: white; font-weight: bold; }
</style>

<div class="card-box">
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>#ID & Waktu</th>
                    <th>Pemesan</th>
                    <th>Keuangan (Rincian)</th>
                    <th>Status & Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): 
                    $subtotal = $row['total_bayar'] - $row['ongkir'];
                ?>
                <tr>
                    <td>
                        <strong>#<?php echo $row['id']; ?></strong><br>
                        <small><?php echo date('d M Y H:i', strtotime($row['tanggal'])); ?></small>
                    </td>
                    <td>
                        <strong><?php echo htmlspecialchars($row['nama_pemesan']); ?></strong><br>
                        <small><?php echo $row['no_hp']; ?></small><br>
                        <small style="color:#666;"><?php echo $row['alamat_kirim']; ?></small>
                    </td>
                    <td>
                        <div style="font-size: 13px;">Brg: Rp <?php echo number_format($subtotal); ?></div>
                        <div style="font-size: 13px;">Ongkir: Rp <?php echo number_format($row['ongkir']); ?></div>
                        <div style="font-weight: bold; color: #2196f3; margin-top: 5px;">Total: Rp <?php echo number_format($row['total_bayar']); ?></div>
                        
                        <?php if(!empty($row['bukti_bayar'])): ?>
                            <a href="../images/bukti/<?php echo $row['bukti_bayar']; ?>" target="_blank" style="font-size: 11px; text-decoration: underline; color: #2ecc71;">[Lihat Bukti]</a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="id_pesanan" value="<?php echo $row['id']; ?>">
                            <select name="status" style="padding: 4px; margin-bottom: 5px;">
                                <option value="<?php echo $row['status']; ?>"><?php echo $row['status']; ?> (Saat Ini)</option>
                                <option value="Pending">Pending</option>
                                <option value="Menunggu Konfirmasi">Menunggu Konfirmasi</option>
                                <option value="Dikemas">Dikemas</option>
                                <option value="Dikirim">Dikirim</option>
                                <option value="Selesai">Selesai</option>
                                <option value="Dibatalkan">Dibatalkan</option>
                            </select>
                            <button type="submit" name="update_status" style="cursor: pointer;">Update</button>
                        </form>
                        <a href="cetak_nota.php?id=<?php echo $row['id']; ?>" target="_blank" style="font-size: 12px; color: #555;"><i class="fas fa-print"></i> Cetak Nota</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
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

<div class="container">
    <div style="background: white; padding: 20px; border-radius: 4px;">
        <h3>Kelola Pesanan Masuk</h3>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pemesan</th>
                        <th>Total</th>
                        <th>Bukti Bayar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?><br><small><?php echo date('d/m/y H:i', strtotime($row['tanggal'])); ?></small></td>
                        <td>
                            <?php echo htmlspecialchars($row['nama_pemesan']); ?><br>
                            <small><?php echo $row['no_hp']; ?></small><br>
                            <small style="color:blue"><?php echo $row['alamat_kirim']; ?></small>
                        </td>
                        <td>Rp <?php echo number_format($row['total_bayar'], 0, ',', '.'); ?></td>
                        <td>
                            <?php if(!empty($row['bukti_bayar'])): ?>
                                <a href="../images/bukti/<?php echo $row['bukti_bayar']; ?>" target="_blank" style="text-decoration:underline; color:blue;">Lihat Bukti</a>
                            <?php else: ?>
                                <span style="color:red;">Belum upload</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" style="display:flex; gap:5px;">
                                <input type="hidden" name="id_pesanan" value="<?php echo $row['id']; ?>">
                                <select name="status" style="padding:5px; border:1px solid #ccc; font-size:12px;">
                                    <option value="Pending" <?php echo ($row['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Menunggu Konfirmasi" <?php echo ($row['status'] == 'Menunggu Konfirmasi') ? 'selected' : ''; ?>>Tunggu Konfirmasi</option>
                                    <option value="Dikemas" <?php echo ($row['status'] == 'Dikemas') ? 'selected' : ''; ?>>Dikemas</option>
                                    <option value="Dikirim" <?php echo ($row['status'] == 'Dikirim') ? 'selected' : ''; ?>>Dikirim</option>
                                    <option value="Selesai" <?php echo ($row['status'] == 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                                    <option value="Dibatalkan" <?php echo ($row['status'] == 'Dibatalkan') ? 'selected' : ''; ?>>Batal</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-primary" style="padding:2px 5px;"><i class="fas fa-check"></i></button>
                            </form>
                        </td>
                        <td style="display:flex; gap:5px;">
                            <!-- Tombol Cetak Nota -->
                            <a href="cetak_nota.php?id=<?php echo $row['id']; ?>" target="_blank" class="btn btn-secondary" style="padding:5px; font-size:12px; background:#333; color:white;" title="Cetak Nota">
                                <i class="fas fa-print"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
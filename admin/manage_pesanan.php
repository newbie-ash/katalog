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
    .card-box {
        background: #fff;
        padding: 25px;
        border-radius: 5px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }
    .table { width: 100%; border-collapse: collapse; }
    .table th { background: #f8f9fa; padding: 15px; text-align: left; border-bottom: 2px solid #eee; font-weight: 600; color: #555; }
    .table td { padding: 15px; border-bottom: 1px solid #eee; vertical-align: top; color: #444; }
    
    .status-select {
        padding: 6px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 13px;
        outline: none;
    }
    
    .btn-update {
        background: #2196f3; color: white; border: none; padding: 7px 10px; 
        border-radius: 4px; cursor: pointer; margin-left: 5px;
    }
    .btn-print {
        background: #34495e; color: white; padding: 8px 12px; 
        border-radius: 4px; font-size: 13px; display: inline-flex; align-items: center; gap: 5px;
    }
    
    .badge-status {
        display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 11px; color: white; font-weight: bold; margin-bottom: 5px;
    }
</style>

<div class="card-box">
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>#ID & Tanggal</th>
                    <th>Info Pemesan</th>
                    <th>Detail Transaksi</th>
                    <th>Status & Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td width="150">
                        <strong style="color: #2196f3; font-size: 16px;">#<?php echo $row['id']; ?></strong><br>
                        <span style="font-size: 12px; color: #888;">
                            <i class="far fa-calendar-alt"></i> <?php echo date('d M Y', strtotime($row['tanggal'])); ?><br>
                            <i class="far fa-clock"></i> <?php echo date('H:i', strtotime($row['tanggal'])); ?> WIB
                        </span>
                    </td>
                    <td width="250">
                        <strong style="font-size: 15px;"><?php echo htmlspecialchars($row['nama_pemesan']); ?></strong><br>
                        <a href="https://wa.me/<?php echo preg_replace('/^0/', '62', $row['no_hp']); ?>" target="_blank" style="font-size: 13px; color: #27ae60;">
                            <i class="fab fa-whatsapp"></i> <?php echo $row['no_hp']; ?>
                        </a>
                        <p style="font-size: 13px; color: #666; margin-top: 5px; line-height: 1.4;">
                            <i class="fas fa-map-marker-alt"></i> <?php echo $row['alamat_kirim']; ?>
                        </p>
                    </td>
                    <td>
                        <div style="font-size: 16px; font-weight: bold; color: #333;">
                            Rp <?php echo number_format($row['total_bayar'], 0, ',', '.'); ?>
                        </div>
                        <div style="margin-top: 8px;">
                            <?php if(!empty($row['bukti_bayar'])): ?>
                                <a href="../images/bukti/<?php echo $row['bukti_bayar']; ?>" target="_blank" style="font-size: 12px; color: #2196f3; text-decoration: underline;">
                                    <i class="fas fa-image"></i> Lihat Bukti Bayar
                                </a>
                            <?php else: ?>
                                <span style="font-size: 12px; color: #e74c3c; background: #ffebee; padding: 2px 6px; border-radius: 3px;">
                                    Belum Upload Bukti
                                </span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td width="250">
                        <!-- Badge Status Visual -->
                        <?php 
                        $st = $row['status'];
                        $bg = '#95a5a6'; // Default grey
                        if($st=='Pending') $bg='#f39c12';
                        elseif($st=='Menunggu Konfirmasi') $bg='#3498db';
                        elseif($st=='Dikemas') $bg='#9b59b6';
                        elseif($st=='Dikirim') $bg='#1abc9c';
                        elseif($st=='Selesai') $bg='#27ae60';
                        elseif($st=='Dibatalkan') $bg='#e74c3c';
                        ?>
                        <span class="badge-status" style="background: <?php echo $bg; ?>;"><?php echo $st; ?></span>

                        <!-- Form Update -->
                        <form method="POST" style="margin-top: 5px; display: flex; align-items: center;">
                            <input type="hidden" name="id_pesanan" value="<?php echo $row['id']; ?>">
                            <select name="status" class="status-select">
                                <option value="Pending" <?php echo ($st == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="Menunggu Konfirmasi" <?php echo ($st == 'Menunggu Konfirmasi') ? 'selected' : ''; ?>>Cek Bukti</option>
                                <option value="Dikemas" <?php echo ($st == 'Dikemas') ? 'selected' : ''; ?>>Dikemas</option>
                                <option value="Dikirim" <?php echo ($st == 'Dikirim') ? 'selected' : ''; ?>>Dikirim</option>
                                <option value="Selesai" <?php echo ($st == 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                                <option value="Dibatalkan" <?php echo ($st == 'Dibatalkan') ? 'selected' : ''; ?>>Batal</option>
                            </select>
                            <button type="submit" name="update_status" class="btn-update" title="Simpan Status">
                                <i class="fas fa-save"></i>
                            </button>
                        </form>

                        <div style="margin-top: 10px;">
                            <a href="cetak_nota.php?id=<?php echo $row['id']; ?>" target="_blank" class="btn-print">
                                <i class="fas fa-print"></i> Cetak Nota
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
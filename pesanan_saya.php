<?php
// pesanan_saya.php
include_once 'db_koneksi.php';
include_once 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['user_id'];
$sql = "SELECT * FROM pesanan WHERE id_user = $id_user ORDER BY id DESC";
$result = $conn->query($sql);
?>

<div class="container">
    <h2>Riwayat Pesanan Saya</h2>
    
    <div class="card-container table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tanggal</th>
                    <th>Total Belanja</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td><?php echo date('d M Y H:i', strtotime($row['tanggal'])); ?></td>
                            <td>Rp <?php echo number_format($row['total_bayar'], 0, ',', '.'); ?></td>
                            <td>
                                <?php 
                                $status = $row['status'];
                                $badge_color = '#777';
                                if($status == 'Pending') $badge_color = '#f0ad4e';
                                elseif($status == 'Menunggu Konfirmasi') $badge_color = '#5bc0de';
                                elseif($status == 'Dikemas') $badge_color = '#337ab7';
                                elseif($status == 'Dikirim') $badge_color = '#5bc0de';
                                elseif($status == 'Selesai') $badge_color = '#5cb85c';
                                elseif($status == 'Dibatalkan') $badge_color = '#d9534f';
                                ?>
                                <span style="background: <?php echo $badge_color; ?>; color: white; padding: 3px 8px; border-radius: 4px; font-size: 12px;"><?php echo $status; ?></span>
                            </td>
                            <td>
                                <?php if ($row['status'] == 'Pending'): ?>
                                    <a href="pembayaran.php?id=<?php echo $row['id']; ?>" class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;">Bayar</a>
                                <?php elseif ($row['status'] == 'Dikirim'): ?>
                                    <a href="terima_pesanan.php?id=<?php echo $row['id']; ?>" class="btn btn-primary" onclick="return confirm('Pesanan sudah diterima?')" style="padding: 5px 10px; font-size: 12px;">Terima</a>
                                <?php else: ?>
                                    <span style="font-size:12px; color:#aaa;">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Belum ada riwayat pesanan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once 'footer.php'; ?>
<?php
// admin/cetak_nota.php
include '../db_koneksi.php';

if (!isset($_GET['id'])) {
    die("ID Pesanan tidak ditemukan");
}

$id_pesanan = intval($_GET['id']);
$sql = "SELECT p.*, u.nama as nama_pemesan, u.no_hp, u.email 
        FROM pesanan p JOIN user u ON p.id_user = u.id 
        WHERE p.id = $id_pesanan";
$order = $conn->query($sql)->fetch_assoc();

$sql_detail = "SELECT d.*, pr.nama_barang, pr.satuan 
               FROM detail_pesanan d 
               JOIN produk pr ON d.id_produk = pr.id 
               WHERE d.id_pesanan = $id_pesanan";
$items = $conn->query($sql_detail);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota #<?php echo $id_pesanan; ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; width: 100%; max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px dashed #000; padding-bottom: 10px; }
        .info { display: flex; justify-content: space-between; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border-bottom: 1px solid #ddd; padding: 8px; text-align: left; }
        .total { text-align: right; font-weight: bold; font-size: 1.2em; }
        .footer { text-align: center; margin-top: 30px; font-size: 0.8em; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <button onclick="window.print()" class="no-print" style="padding:10px; cursor:pointer;">Cetak Sekarang</button>
    <a href="manage_pesanan.php" class="no-print">Kembali</a>

    <div class="header">
        <h2>MATRIA.MART</h2>
        <p>Pusat Material Bangunan Terlengkap</p>
    </div>

    <div class="info">
        <div>
            <strong>Penerima:</strong><br>
            <?php echo htmlspecialchars($order['nama_pemesan']); ?><br>
            <?php echo htmlspecialchars($order['no_hp']); ?><br>
            <?php echo nl2br(htmlspecialchars($order['alamat_kirim'])); ?>
        </div>
        <div style="text-align: right;">
            <strong>No. Nota: #<?php echo $order['id']; ?></strong><br>
            Tanggal: <?php echo date('d/m/Y', strtotime($order['tanggal'])); ?><br>
            Status: <?php echo $order['status']; ?>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Barang</th>
                <th>Qty</th>
                <th>Harga</th>
                <th style="text-align:right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php while($item = $items->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['nama_barang']); ?></td>
                <td><?php echo $item['qty'] . ' ' . $item['satuan']; ?></td>
                <td>Rp <?php echo number_format($item['harga_deal'],0,',','.'); ?></td>
                <td style="text-align:right;">Rp <?php echo number_format($item['subtotal'],0,',','.'); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="total">
        Total Bayar: Rp <?php echo number_format($order['total_bayar'], 0, ',', '.'); ?>
    </div>

    <div class="footer">
        <p>Terima kasih telah berbelanja di Matria.Mart!</p>
        <p>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.</p>
    </div>

</body>
</html>
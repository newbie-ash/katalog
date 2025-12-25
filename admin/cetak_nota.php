<?php
// admin/cetak_nota.php
include '../db_koneksi.php';

if (!isset($_GET['id'])) die("ID Pesanan tidak ditemukan");

$id = intval($_GET['id']);
$order = $conn->query("SELECT p.*, u.nama, u.no_hp FROM pesanan p JOIN user u ON p.id_user = u.id WHERE p.id = $id")->fetch_assoc();
$items = $conn->query("SELECT d.*, p.nama_barang, p.satuan FROM detail_pesanan d JOIN produk p ON d.id_produk = p.id WHERE d.id_pesanan = $id");

$subtotal_barang = $order['total_bayar'] - $order['ongkir'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Nota #<?php echo $id; ?></title>
    <style>
        body { font-family: monospace; max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; }
        .header { text-align: center; border-bottom: 2px dashed #000; padding-bottom: 10px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 5px 0; }
        .right { text-align: right; }
        .totals { margin-top: 20px; border-top: 1px dashed #000; padding-top: 10px; }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>MATRIA.MART</h2>
        <p>Jl. Contoh No. 123, Indonesia</p>
    </div>

    <p>
        No: #<?php echo $order['id']; ?><br>
        Tgl: <?php echo date('d/m/Y', strtotime($order['tanggal'])); ?><br>
        Kepada: <?php echo htmlspecialchars($order['nama']); ?> (<?php echo $order['no_hp']; ?>)
    </p>

    <table>
        <tr><th>Barang</th><th class="right">Total</th></tr>
        <?php while($item = $items->fetch_assoc()): ?>
        <tr>
            <td><?php echo $item['nama_barang']; ?> (<?php echo $item['qty']; ?> <?php echo $item['satuan']; ?>)</td>
            <td class="right">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <div class="totals">
        <table>
            <tr><td>Subtotal Barang</td><td class="right">Rp <?php echo number_format($subtotal_barang, 0, ',', '.'); ?></td></tr>
            <tr><td>Ongkos Kirim</td><td class="right">Rp <?php echo number_format($order['ongkir'], 0, ',', '.'); ?></td></tr>
            <tr><td><strong>Total Bayar</strong></td><td class="right"><strong>Rp <?php echo number_format($order['total_bayar'], 0, ',', '.'); ?></strong></td></tr>
        </table>
    </div>

    <center style="margin-top: 30px;">Terima Kasih!</center>
</body>
</html>
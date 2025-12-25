<?php
// pembayaran.php
include_once 'db_koneksi.php';
include_once 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_pesanan = isset($_GET['id']) ? intval($_GET['id']) : 0;
$id_user = $_SESSION['user_id'];

// Cek Pesanan
$cek = $conn->query("SELECT * FROM pesanan WHERE id = $id_pesanan AND id_user = $id_user");
if ($cek->num_rows == 0) {
    echo "<div class='container'><div class='message error'>Pesanan tidak ditemukan.</div></div>";
    include_once 'footer.php';
    exit;
}
$order = $cek->fetch_assoc();

// Upload Bukti Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['bukti'])) {
    $target_dir = "images/bukti/";
    if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }

    $ext = pathinfo($_FILES["bukti"]["name"], PATHINFO_EXTENSION);
    $filename = "bukti_" . $id_pesanan . "_" . time() . "." . $ext;
    
    if (move_uploaded_file($_FILES["bukti"]["tmp_name"], $target_dir . $filename)) {
        $conn->query("UPDATE pesanan SET bukti_bayar = '$filename', status = 'Menunggu Konfirmasi' WHERE id = $id_pesanan");
        echo "<script>alert('Bukti berhasil diupload!'); window.location='pesanan_saya.php';</script>";
    } else {
        echo "<script>alert('Gagal upload.');</script>";
    }
}

// Hitung Subtotal (Total Bayar - Ongkir)
$subtotal_barang = $order['total_bayar'] - $order['ongkir'];
?>

<div class="container">
    <div class="auth-wrapper" style="min-height: auto;">
        <div class="auth-box" style="max-width: 500px; text-align: left;">
            <h3 class="text-center">Pembayaran #<?php echo $id_pesanan; ?></h3>
            
            <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <table style="width: 100%; color: #555;">
                    <tr>
                        <td>Total Barang</td>
                        <td style="text-align: right;">Rp <?php echo number_format($subtotal_barang, 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td>Ongkos Kirim</td>
                        <td style="text-align: right;">Rp <?php echo number_format($order['ongkir'], 0, ',', '.'); ?></td>
                    </tr>
                    <tr><td colspan="2"><hr></td></tr>
                    <tr style="font-weight: bold; font-size: 18px; color: #333;">
                        <td>Total Tagihan</td>
                        <td style="text-align: right; color: var(--primary-color);">Rp <?php echo number_format($order['total_bayar'], 0, ',', '.'); ?></td>
                    </tr>
                </table>

                <hr style="margin: 15px 0;">
                <p>Silakan transfer ke:</p>
                <ul style="margin-left: 20px;">
                    <li><strong>BCA:</strong> 123-456-7890 (Matria Mart)</li>
                </ul>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Upload Bukti Transfer</label>
                    <input type="file" name="bukti" required accept="image/*" style="width: 100%; border: 1px solid #ddd; padding: 5px;">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Kirim Bukti</button>
            </form>
            <br>
            <a href="pesanan_saya.php" style="display:block; text-align:center;">&larr; Kembali</a>
        </div>
    </div>
</div>

<?php include_once 'footer.php'; ?>
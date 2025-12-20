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

// Cek apakah pesanan milik user ini
$cek = $conn->query("SELECT * FROM pesanan WHERE id = $id_pesanan AND id_user = $id_user");
if ($cek->num_rows == 0) {
    echo "<div class='container'><div class='message error'>Pesanan tidak ditemukan.</div></div>";
    include_once 'footer.php';
    exit;
}
$order = $cek->fetch_assoc();

// Proses Upload Bukti
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['bukti'])) {
    $target_dir = "images/bukti/";
    // Buat folder jika belum ada
    if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }

    $ext = pathinfo($_FILES["bukti"]["name"], PATHINFO_EXTENSION);
    $filename = "bukti_" . $id_pesanan . "_" . time() . "." . $ext;
    $target_file = $target_dir . $filename;

    // Validasi file
    $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
    if (in_array(strtolower($ext), $allowed)) {
        if (move_uploaded_file($_FILES["bukti"]["tmp_name"], $target_file)) {
            // Update Database
            $conn->query("UPDATE pesanan SET bukti_bayar = '$filename', status = 'Menunggu Konfirmasi' WHERE id = $id_pesanan");
            echo "<script>alert('Bukti pembayaran berhasil diupload!'); window.location='pesanan_saya.php';</script>";
        } else {
            echo "<script>alert('Gagal mengupload file.');</script>";
        }
    } else {
        echo "<script>alert('Format file harus JPG, PNG, atau PDF.');</script>";
    }
}
?>

<div class="container">
    <div class="auth-wrapper" style="min-height: auto;">
        <div class="auth-box" style="max-width: 500px; text-align: left;">
            <h3 class="text-center">Pembayaran Pesanan #<?php echo $id_pesanan; ?></h3>
            
            <div style="background: #f9f9f9; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                <p><strong>Total Tagihan:</strong> <br> <span style="font-size: 24px; color: var(--primary-color); font-weight: bold;">Rp <?php echo number_format($order['total_bayar'], 0, ',', '.'); ?></span></p>
                <hr>
                <p>Silakan transfer ke rekening berikut:</p>
                <ul style="margin-left: 20px;">
                    <li><strong>BCA:</strong> 123-456-7890 (Toko Bangunan)</li>
                    <li><strong>BRI:</strong> 9876-01-000000-50-0 (Toko Bangunan)</li>
                </ul>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Upload Bukti Transfer (Foto/Screenshot)</label>
                    <input type="file" name="bukti" required accept="image/*,application/pdf" style="padding: 5px;">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Kirim Bukti Pembayaran</button>
            </form>
            <br>
            <a href="pesanan_saya.php" style="display:block; text-align:center;">Kembali ke Pesanan Saya</a>
        </div>
    </div>
</div>

<?php include_once 'footer.php'; ?>
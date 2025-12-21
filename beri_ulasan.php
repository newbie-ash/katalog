<?php
// beri_ulasan.php
include_once 'db_koneksi.php';
include_once 'header.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location='login.php';</script>";
    exit;
}

$id_user = $_SESSION['user_id'];
$id_produk = isset($_GET['produk']) ? intval($_GET['produk']) : 0;
$id_pesanan = isset($_GET['pesanan']) ? intval($_GET['pesanan']) : 0;

// 1. VALIDASI: Pastikan user benar-benar membeli produk ini & status Selesai
$cek_beli = $conn->query("
    SELECT dp.*, p.nama_barang, p.gambar 
    FROM detail_pesanan dp 
    JOIN pesanan ps ON dp.id_pesanan = ps.id 
    JOIN produk p ON dp.id_produk = p.id
    WHERE dp.id_pesanan = $id_pesanan 
      AND dp.id_produk = $id_produk 
      AND ps.id_user = $id_user 
      AND ps.status = 'Selesai'
");

if ($cek_beli->num_rows == 0) {
    echo "<div class='container' style='padding:50px; text-align:center;'>
            <h3>Produk tidak valid atau pesanan belum selesai.</h3>
            <a href='pesanan_saya.php' class='btn btn-primary'>Kembali</a>
          </div>";
    include_once 'footer.php';
    exit;
}

$produk = $cek_beli->fetch_assoc();

// 2. CEK DUPLIKASI: Apakah sudah pernah diulas?
$cek_ulasan = $conn->query("SELECT * FROM ulasan WHERE id_pesanan = $id_pesanan AND id_produk = $id_produk");
if ($cek_ulasan->num_rows > 0) {
    echo "<div class='container' style='padding:50px; text-align:center;'>
            <h3>Anda sudah mengulas produk ini.</h3>
            <a href='produk.php?id=$id_produk' class='btn btn-primary'>Lihat Ulasan</a>
          </div>";
    include_once 'footer.php';
    exit;
}

// 3. PROSES SIMPAN ULASAN
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = intval($_POST['rating']);
    $komentar = $conn->real_escape_string($_POST['komentar']);

    // Validasi rating 1-5
    if ($rating < 1 || $rating > 5) {
        echo "<script>alert('Rating harus antara 1 sampai 5 bintang.');</script>";
    } else {
        $insert = $conn->query("INSERT INTO ulasan (id_user, id_produk, id_pesanan, rating, komentar) VALUES ($id_user, $id_produk, $id_pesanan, $rating, '$komentar')");
        
        if ($insert) {
            echo "<script>alert('Terima kasih! Ulasan Anda berhasil disimpan.'); window.location='pesanan_saya.php';</script>";
            exit;
        } else {
            echo "<script>alert('Gagal menyimpan ulasan. Silakan coba lagi.');</script>";
        }
    }
}
?>

<div class="container">
    <div class="auth-wrapper" style="min-height: auto; margin-top: 20px;">
        <div class="auth-box" style="max-width: 500px; text-align: left;">
            <h3 style="border-bottom: 1px solid #eee; padding-bottom: 15px; margin-top: 0;">Beri Ulasan Produk</h3>
            
            <!-- Info Produk -->
            <div style="display: flex; gap: 15px; margin-bottom: 20px; background: #f9f9f9; padding: 10px; border-radius: 5px;">
                <img src="images/<?php echo $produk['gambar']; ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                <div>
                    <strong style="display: block; font-size: 14px;"><?php echo htmlspecialchars($produk['nama_barang']); ?></strong>
                    <small style="color: #666;">No. Pesanan: #<?php echo $id_pesanan; ?></small>
                </div>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label>Rating Bintang</label>
                    
                    <!-- CSS Star Rating -->
                    <div class="star-rating">
                        <input type="radio" id="star5" name="rating" value="5" required /><label for="star5" title="Sempurna"></label>
                        <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="Bagus"></label>
                        <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="Cukup"></label>
                        <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="Buruk"></label>
                        <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="Sangat Buruk"></label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Komentar Anda</label>
                    <textarea name="komentar" rows="4" class="form-control" placeholder="Bagaimana kualitas produk ini? Ceritakan pengalaman Anda..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="justify-content: center;">
                    Kirim Ulasan
                </button>
            </form>
        </div>
    </div>
</div>

<style>
/* CSS Simple Star Rating */
.star-rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}
.star-rating input { display: none; }
.star-rating label {
    font-size: 30px;
    color: #ddd;
    cursor: pointer;
    padding: 0 5px;
    transition: 0.2s;
}
.star-rating label:before { content: '\f005'; font-family: "Font Awesome 6 Free", "Font Awesome 5 Free"; font-weight: 900; }
.star-rating input:checked ~ label { color: #ffc107; } /* Warna Kuning Emas */
.star-rating label:hover,
.star-rating label:hover ~ label { color: #ffdb70; }
</style>

<?php include_once 'footer.php'; ?>
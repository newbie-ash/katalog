<?php
// produk.php
include_once 'db_koneksi.php';
include_once 'header.php';

$id_produk = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("SELECT p.*, k.nama_kategori FROM produk p JOIN kategori k ON p.id_kategori = k.id WHERE p.id = ?");
$stmt->bind_param("i", $id_produk);
$stmt->execute();
$result = $stmt->get_result();

// --- QUERY RATING & ULASAN ---
// 1. Hitung Rata-rata Rating
$q_avg = $conn->query("SELECT AVG(rating) as rata, COUNT(*) as total FROM ulasan WHERE id_produk = $id_produk");
$stats = $q_avg->fetch_assoc();
$rating_avg = round($stats['rata'], 1); // Pembulatan 1 desimal (cth: 4.5)
$rating_total = $stats['total'];

// 2. Ambil Daftar Ulasan Terbaru (Join ke User untuk nama & foto)
$q_reviews = $conn->query("
    SELECT u.*, us.nama, us.foto 
    FROM ulasan u 
    JOIN user us ON u.id_user = us.id 
    WHERE u.id_produk = $id_produk 
    ORDER BY u.tanggal DESC 
    LIMIT 5
");
?>

<div class="container">
    <?php if ($result->num_rows > 0): 
        $row = $result->fetch_assoc();
        $gambar = !empty($row['gambar']) ? 'images/' . $row['gambar'] : 'https://via.placeholder.com/500x500?text=No+Image';
    ?>
        <div class="detail-wrapper">
            <!-- Kolom Gambar -->
            <div class="detail-image">
                <img src="<?php echo $gambar; ?>" alt="<?php echo htmlspecialchars($row['nama_barang']); ?>">
            </div>
            
            <!-- Kolom Info -->
            <div class="detail-info">
                <h1 style="margin-top:0; font-size: 1.5rem; margin-bottom: 5px;"><?php echo htmlspecialchars($row['nama_barang']); ?></h1>
                
                <!-- TAMPILAN BINTANG RATA-RATA -->
                <div style="margin-bottom: 15px; color: #ffc107; font-size: 14px; display: flex; align-items: center; gap: 5px;">
                    <?php 
                    for($i=1; $i<=5; $i++) {
                        if($i <= $rating_avg) echo '<i class="fas fa-star"></i>'; // Bintang Penuh
                        elseif($i - 0.5 <= $rating_avg) echo '<i class="fas fa-star-half-alt"></i>'; // Setengah Bintang
                        else echo '<i class="far fa-star" style="color:#ccc;"></i>'; // Bintang Kosong
                    }
                    ?>
                    <span style="color: #666; font-size: 13px; margin-left: 5px;">
                        (<?php echo $rating_avg > 0 ? $rating_avg : '0'; ?>/5 dari <?php echo $rating_total; ?> Ulasan)
                    </span>
                </div>

                <div class="detail-price-box">
                    <div style="font-size: 1.8rem; color: var(--primary-color); font-weight: bold;">
                        Rp <?php echo number_format($row['harga_ecer'], 0, ',', '.'); ?>
                        <span style="font-size: 1rem; color: #666; font-weight: normal;"> / <?php echo $row['satuan']; ?></span>
                    </div>
                    
                    <div style="margin-top: 10px; padding: 10px; background: white; border: 1px dashed var(--primary-color); border-radius: 4px;">
                        <strong style="color: var(--primary-color);">HARGA GROSIR TERSEDIA!</strong><br>
                        Beli minimal <strong><?php echo $row['min_belanja_grosir']; ?> <?php echo $row['satuan']; ?></strong>, 
                        dapatkan harga <strong>Rp <?php echo number_format($row['harga_grosir'], 0, ',', '.'); ?></strong>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <strong>Deskripsi Produk:</strong>
                    <p style="color: #555; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($row['deskripsi'])); ?></p>
                </div>

                <!-- Form Tambah ke Keranjang -->
                <form action="keranjang.php" method="POST" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="id_produk" value="<?php echo $row['id']; ?>">
                    
                    <div style="flex: 0 0 auto;">
                        <label style="font-size: 12px; color: #666; display: block; margin-bottom: 5px;">Jumlah</label>
                        <div style="display: flex; align-items: center; border: 1px solid #ccc; border-radius: 4px; overflow: hidden; background-color: white;">
                            <button type="button" onclick="ubahQty(-1)" style="width: 45px; height: 45px; background: #f0f0f0; color: #333; border: none; border-right: 1px solid #ccc; cursor: pointer; display: flex; align-items: center; justify-content: center;"><i class="fas fa-minus"></i></button>
                            <input type="number" name="qty" id="inputQty" value="1" min="1" max="<?php echo $row['stok']; ?>" style="width: 60px; height: 45px; text-align: center; border: none; font-weight: bold; font-size: 16px; outline: none; -moz-appearance: textfield; background: white;">
                            <button type="button" onclick="ubahQty(1)" style="width: 45px; height: 45px; background: #f0f0f0; color: #333; border: none; border-left: 1px solid #ccc; cursor: pointer; display: flex; align-items: center; justify-content: center;"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="flex: 1; padding: 12px; height: 47px; font-size: 16px; min-width: 200px;">
                        <i class="fas fa-cart-plus"></i> Masukkan Keranjang
                    </button>
                </form>
            </div>
        </div>

        <!-- SECTION DAFTAR ULASAN PEMBELI -->
        <div class="card-container" style="margin-top: 30px; padding: 30px;">
            <h3 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 15px; color: #333;">Ulasan Pembeli</h3>
            
            <?php if ($q_reviews->num_rows > 0): ?>
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <?php while($rev = $q_reviews->fetch_assoc()): 
                        // Gunakan foto user jika ada
                        $foto_user = !empty($rev['foto']) ? 'images/users/'.$rev['foto'] : 'https://via.placeholder.com/50?text=User';
                    ?>
                        <div style="display: flex; gap: 15px; border-bottom: 1px solid #f9f9f9; padding-bottom: 15px;">
                            <img src="<?php echo $foto_user; ?>" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 1px solid #eee;">
                            <div>
                                <div style="font-weight: bold; font-size: 14px;"><?php echo htmlspecialchars($rev['nama']); ?></div>
                                <div style="color: #ffc107; font-size: 12px; margin: 3px 0;">
                                    <?php for($k=0; $k<$rev['rating']; $k++) echo '<i class="fas fa-star"></i>'; ?>
                                    <span style="color: #999; margin-left: 10px; font-size: 11px;"><?php echo date('d M Y', strtotime($rev['tanggal'])); ?></span>
                                </div>
                                <p style="margin: 5px 0 0; color: #555; font-size: 14px; line-height: 1.4;">
                                    <?php echo htmlspecialchars($rev['komentar']); ?>
                                </p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; color: #999; padding: 20px;">
                    <i class="far fa-comment-dots fa-2x"></i>
                    <p>Belum ada ulasan untuk produk ini.</p>
                </div>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <div class="card-container text-center">
            <h3>Produk tidak ditemukan</h3>
            <a href="index.php" class="btn btn-primary">Kembali ke Beranda</a>
        </div>
    <?php endif; ?>
</div>

<script>
    function ubahQty(val) {
        var input = document.getElementById('inputQty');
        var current = parseInt(input.value);
        var max = parseInt(input.getAttribute('max'));
        var min = parseInt(input.getAttribute('min'));
        var newVal = current + val;
        if (newVal >= min && newVal <= max) input.value = newVal;
    }
</script>

<?php include_once 'footer.php'; ?>
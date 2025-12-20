<?php
// produk.php
include_once 'db_koneksi.php';
include_once 'header.php';

$id_produk = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("SELECT p.*, k.nama_kategori FROM produk p JOIN kategori k ON p.id_kategori = k.id WHERE p.id = ?");
$stmt->bind_param("i", $id_produk);
$stmt->execute();
$result = $stmt->get_result();
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
                <h1 style="margin-top:0; font-size: 1.5rem;"><?php echo htmlspecialchars($row['nama_barang']); ?></h1>
                
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

                <div style="display: flex; gap: 20px; align-items: center; margin-bottom: 20px;">
                    <div>
                        <small>Kategori</small><br>
                        <strong><?php echo htmlspecialchars($row['nama_kategori']); ?></strong>
                    </div>
                    <div>
                        <small>Stok</small><br>
                        <strong><?php echo $row['stok']; ?> <?php echo $row['satuan']; ?></strong>
                    </div>
                </div>

                <form action="keranjang.php" method="POST" style="display: flex; gap: 10px; align-items: flex-end;">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="id_produk" value="<?php echo $row['id']; ?>">
                    
                    <div style="flex: 0 0 80px;">
                        <label style="font-size: 12px; color: #666;">Jumlah</label>
                        <input type="number" name="qty" value="1" min="1" max="<?php echo $row['stok']; ?>" style="width: 100%; padding: 10px; text-align: center; border: 1px solid #ddd;">
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="flex: 1; padding: 12px; font-size: 16px;">
                        <i class="fas fa-cart-plus"></i> Masukkan Keranjang
                    </button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="card-container text-center">
            <h3>Produk tidak ditemukan</h3>
            <a href="index.php" class="btn btn-primary">Kembali ke Beranda</a>
        </div>
    <?php endif; ?>
</div>

<?php include_once 'footer.php'; ?>
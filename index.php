<?php
// index.php
include_once 'db_koneksi.php';
include_once 'header.php';

// Ambil Produk
$sql = "SELECT p.*, k.nama_kategori 
        FROM produk p
        JOIN kategori k ON p.id_kategori = k.id
        ORDER BY p.id DESC";
$result = $conn->query($sql);
?>

<div class="container">
    <!-- Banner Sederhana -->
    <div style="background: white; padding: 20px; border-radius: 4px; margin-bottom: 20px; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
        <h2 style="margin:0; color: var(--primary-color);">Selamat Datang di Katalog Grosir!</h2>
        <p style="margin: 5px 0; color: #666;">Dapatkan harga spesial untuk pembelian dalam jumlah banyak.</p>
    </div>

    <!-- Grid Produk -->
    <div class="product-grid">
        <?php
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $harga = number_format($row['harga_ecer'], 0, ',', '.');
                $harga_grosir = number_format($row['harga_grosir'], 0, ',', '.');
                $gambar = !empty($row['gambar']) ? 'images/' . $row['gambar'] : 'https://via.placeholder.com/300x300?text=No+Image';
                ?>
                
                <a href="produk.php?id=<?php echo $row['id']; ?>" class="product-card">
                    <img src="<?php echo $gambar; ?>" alt="<?php echo htmlspecialchars($row['nama_barang']); ?>">
                    <div class="product-info">
                        <div class="badge-category"><?php echo htmlspecialchars($row['nama_kategori']); ?></div>
                        <h3 class="product-title"><?php echo htmlspecialchars($row['nama_barang']); ?></h3>
                        
                        <div class="product-price">Rp <?php echo $harga; ?></div>
                        
                        <div class="product-grosir">
                            <i class="fas fa-tags"></i> Grosir: Rp <?php echo $harga_grosir; ?> 
                            (Min. <?php echo $row['min_belanja_grosir']; ?> <?php echo $row['satuan']; ?>)
                        </div>
                    </div>
                </a>

                <?php
            }
        } else {
            echo "<div style='grid-column: 1/-1; text-align:center; padding: 20px; background:white;'>Belum ada produk.</div>";
        }
        ?>
    </div>
</div>

<?php
include_once 'footer.php';
$conn->close();
?>
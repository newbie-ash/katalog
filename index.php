<?php
// index.php
include_once 'db_koneksi.php';
include_once 'header.php';

// Logika Pencarian & Filter
$where_clause = "1=1";
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $keyword = $conn->real_escape_string($_GET['q']);
    $where_clause .= " AND (p.nama_barang LIKE '%$keyword%' OR p.deskripsi LIKE '%$keyword%')";
}
if (isset($_GET['kategori']) && !empty($_GET['kategori'])) {
    $kat_id = intval($_GET['kategori']);
    $where_clause .= " AND p.id_kategori = $kat_id";
}

// Ambil Produk dengan Filter
$sql = "SELECT p.*, k.nama_kategori 
        FROM produk p
        JOIN kategori k ON p.id_kategori = k.id
        WHERE $where_clause
        ORDER BY p.id DESC";
$result = $conn->query($sql);

// Ambil Daftar Kategori untuk Sidebar
$sql_kat = "SELECT * FROM kategori";
$res_kat = $conn->query($sql_kat);
?>

<div class="container">
    <!-- Layout Grid: Sidebar (Kiri) & Produk (Kanan) -->
    <div style="display: flex; gap: 20px; align-items: flex-start;">
        
        <!-- SIDEBAR KATEGORI (Hanya muncul di Layar Besar) -->
        <div style="flex: 1; min-width: 200px; background: white; padding: 15px; border-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.1);" class="sidebar-desktop">
            <h4 style="margin-top:0; border-bottom: 2px solid #eee; padding-bottom: 10px;">Kategori</h4>
            <ul style="font-size: 14px;">
                <li style="margin-bottom: 8px;">
                    <a href="index.php" style="<?php echo !isset($_GET['kategori']) ? 'font-weight:bold; color:var(--primary-color);' : ''; ?>">
                        Semua Produk
                    </a>
                </li>
                <?php while($kat = $res_kat->fetch_assoc()): ?>
                <li style="margin-bottom: 8px;">
                    <a href="index.php?kategori=<?php echo $kat['id']; ?>" style="<?php echo (isset($_GET['kategori']) && $_GET['kategori'] == $kat['id']) ? 'font-weight:bold; color:var(--primary-color);' : ''; ?>">
                        <?php echo htmlspecialchars($kat['nama_kategori']); ?>
                    </a>
                </li>
                <?php endwhile; ?>
            </ul>
        </div>

        <!-- KONTEN UTAMA -->
        <div style="flex: 4; width: 100%;">
            <!-- Banner (Hanya muncul jika tidak sedang mencari) -->
            <?php if(!isset($_GET['q']) && !isset($_GET['kategori'])): ?>
            <div style="background: white; padding: 25px; border-radius: 4px; margin-bottom: 20px; box-shadow: 0 1px 2px rgba(0,0,0,0.1); border-left: 5px solid var(--primary-color);">
                <h2 style="margin:0; color: var(--primary-color); text-transform: uppercase;">Selamat Datang di MATRIA.MART</h2>
                <p style="margin: 5px 0; color: #666;">Pusat belanja material bangunan modern, lengkap, dan terpercaya.</p>
            </div>
            <?php else: ?>
                <div style="margin-bottom: 15px;">
                    Menampilkan hasil: <strong><?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : 'Filter Kategori'; ?></strong>
                    <a href="index.php" style="font-size: 12px; color: red; margin-left: 10px;">(Reset Filter)</a>
                </div>
            <?php endif; ?>

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
                    echo "<div style='grid-column: 1/-1; text-align:center; padding: 50px; background:white; width:100%;'>
                            <i class='fas fa-search fa-3x' style='color:#ccc; margin-bottom:10px;'></i>
                            <p>Produk tidak ditemukan.</p>
                            <a href='index.php' class='btn btn-primary'>Lihat Semua Produk</a>
                          </div>";
                }
                ?>
            </div>
        </div>
    </div>
</div>

<style>
/* Sembunyikan sidebar di HP */
@media (max-width: 768px) {
    .sidebar-desktop { display: none; }
}
</style>

<?php
include_once 'footer.php';
$conn->close();
?>
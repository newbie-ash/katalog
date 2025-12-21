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

// AMBIL DATA BANNER (Baru)
$banners = $conn->query("SELECT * FROM banner WHERE aktif = 1 ORDER BY id DESC");
?>

<div class="container">
    
    <!-- Tombol Filter Mobile -->
    <div class="mobile-filter-btn" style="display: none; margin-bottom: 15px;">
        <button onclick="document.querySelector('.sidebar-desktop').classList.toggle('active')" class="btn btn-secondary" style="width: 100%; justify-content: space-between;">
            <span><i class="fas fa-filter"></i> Kategori Produk</span>
            <i class="fas fa-chevron-down"></i>
        </button>
    </div>

    <!-- Layout Grid -->
    <div style="display: flex; gap: 20px; align-items: flex-start; position: relative;">
        
        <!-- SIDEBAR KATEGORI -->
        <div class="sidebar-desktop" style="flex: 1; min-width: 200px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); position: sticky; top: 90px;">
            <h4 style="margin-top:0; border-bottom: 2px solid #eee; padding-bottom: 15px; color: #333; text-transform: uppercase; font-size: 14px; letter-spacing: 1px;">Kategori</h4>
            <ul style="font-size: 14px;">
                <li style="margin-bottom: 10px;">
                    <a href="index.php" style="display: block; padding: 8px 10px; border-radius: 5px; <?php echo !isset($_GET['kategori']) ? 'background: #f0f0f0; color:var(--primary-color); font-weight:bold;' : 'color: #555;'; ?>">
                        Semua Produk
                    </a>
                </li>
                <?php while($kat = $res_kat->fetch_assoc()): ?>
                <li style="margin-bottom: 5px;">
                    <a href="index.php?kategori=<?php echo $kat['id']; ?>" style="display: block; padding: 8px 10px; border-radius: 5px; <?php echo (isset($_GET['kategori']) && $_GET['kategori'] == $kat['id']) ? 'background: #f0f0f0; color:var(--primary-color); font-weight:bold;' : 'color: #555;'; ?>">
                        <?php echo htmlspecialchars($kat['nama_kategori']); ?>
                    </a>
                </li>
                <?php endwhile; ?>
            </ul>
        </div>

        <!-- KONTEN UTAMA -->
        <div class="main-content-area" style="flex: 4; width: 100%;">
            
            <!-- BANNER SLIDER DINAMIS (Hanya muncul jika tidak mencari) -->
            <?php if(!isset($_GET['q']) && !isset($_GET['kategori']) && $banners->num_rows > 0): ?>
                
                <style>
                    /* Simple CSS Slider */
                    .slider-container {
                        width: 100%;
                        height: 300px;
                        overflow: hidden;
                        position: relative;
                        border-radius: 8px;
                        margin-bottom: 25px;
                        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                    }
                    .slides {
                        display: flex;
                        width: 100%;
                        height: 100%;
                        transition: transform 0.5s ease-in-out;
                    }
                    .slide {
                        min-width: 100%;
                        height: 100%;
                        position: relative;
                    }
                    .slide img {
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                    }
                    /* Tombol Navigasi */
                    .slider-nav {
                        position: absolute;
                        top: 50%;
                        width: 100%;
                        display: flex;
                        justify-content: space-between;
                        padding: 0 20px;
                        transform: translateY(-50%);
                        pointer-events: none; /* Agar klik tembus ke gambar jika tombol kecil */
                    }
                    .slider-btn {
                        pointer-events: auto;
                        background: rgba(0,0,0,0.5);
                        color: white;
                        border: none;
                        width: 40px;
                        height: 40px;
                        border-radius: 50%;
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 18px;
                        transition: 0.3s;
                    }
                    .slider-btn:hover { background: var(--primary-color); }
                </style>

                <div class="slider-container" id="promoSlider">
                    <div class="slides" id="slidesTrack">
                        <?php while($ban = $banners->fetch_assoc()): ?>
                            <div class="slide">
                                <a href="<?php echo !empty($ban['link']) ? $ban['link'] : '#'; ?>">
                                    <img src="images/banner/<?php echo $ban['gambar']; ?>" alt="<?php echo htmlspecialchars($ban['judul']); ?>">
                                </a>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    
                    <div class="slider-nav">
                        <button class="slider-btn" onclick="moveSlide(-1)"><i class="fas fa-chevron-left"></i></button>
                        <button class="slider-btn" onclick="moveSlide(1)"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>

                <script>
                    let currentSlide = 0;
                    const track = document.getElementById('slidesTrack');
                    const totalSlides = track.children.length;

                    // Auto Play (3 detik)
                    let autoPlay = setInterval(() => moveSlide(1), 4000);

                    function moveSlide(direction) {
                        currentSlide += direction;
                        if (currentSlide >= totalSlides) currentSlide = 0;
                        if (currentSlide < 0) currentSlide = totalSlides - 1;
                        
                        track.style.transform = `translateX(-${currentSlide * 100}%)`;
                        
                        // Reset timer saat diklik manual
                        clearInterval(autoPlay);
                        autoPlay = setInterval(() => moveSlide(1), 4000);
                    }
                </script>

            <?php elseif(!isset($_GET['q']) && !isset($_GET['kategori'])): ?>
                <!-- Fallback Banner Statis jika Database Kosong -->
                <div style="background: white; padding: 30px; border-radius: 8px; margin-bottom: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); border-left: 5px solid var(--primary-color); background-image: url('https://www.transparenttextures.com/patterns/cubes.png');">
                    <h2 style="margin:0; color: var(--primary-color); text-transform: uppercase; font-size: 1.5rem;">Selamat Datang di MATRIA.MART</h2>
                    <p style="margin: 10px 0 0; color: #666; font-size: 1rem;">Pusat belanja material bangunan modern, lengkap, dan terpercaya.</p>
                </div>
            <?php else: ?>
                <!-- Header Hasil Pencarian -->
                <div style="margin-bottom: 20px; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center;">
                    <span>Menampilkan hasil: <strong><?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : 'Filter Kategori'; ?></strong></span>
                    <a href="index.php" style="font-size: 13px; color: #e74c3c; font-weight: 500;"><i class="fas fa-times"></i> Reset Filter</a>
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
                            <img src="<?php echo $gambar; ?>" alt="<?php echo htmlspecialchars($row['nama_barang']); ?>" loading="lazy">
                            <div class="product-info">
                                <div class="badge-category"><?php echo htmlspecialchars($row['nama_kategori']); ?></div>
                                <h3 class="product-title"><?php echo htmlspecialchars($row['nama_barang']); ?></h3>
                                
                                <div class="product-price">Rp <?php echo $harga; ?></div>
                                
                                <div class="product-grosir">
                                    <i class="fas fa-tags"></i> Grosir: Rp <?php echo $harga_grosir; ?> 
                                    <span style="display: block; font-size: 10px; margin-top: 2px;">(Min. <?php echo $row['min_belanja_grosir']; ?> <?php echo $row['satuan']; ?>)</span>
                                </div>
                            </div>
                        </a>
                        <?php
                    }
                } else {
                    echo "<div style='grid-column: 1/-1; text-align:center; padding: 60px 20px; background:white; width:100%; border-radius: 8px;'>
                            <i class='fas fa-search fa-3x' style='color:#e0e0e0; margin-bottom:20px;'></i>
                            <p style='color: #666; font-size: 1.1rem;'>Produk tidak ditemukan.</p>
                            <a href='index.php' class='btn btn-primary' style='margin-top: 10px;'>Lihat Semua Produk</a>
                          </div>";
                }
                ?>
            </div>
        </div>
    </div>
</div>

<style>
/* Style Tambahan Khusus Halaman Index untuk Mobile */
@media (max-width: 768px) {
    .mobile-filter-btn { display: block !important; }
    .sidebar-desktop { 
        display: none; 
        width: 100%; 
        margin-bottom: 20px;
        position: static !important; 
    }
    .sidebar-desktop.active { display: block !important; animation: fadeIn 0.3s; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
}
</style>

<?php
include_once 'footer.php';
$conn->close();
?>
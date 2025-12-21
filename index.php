<?php
// index.php
include_once 'db_koneksi.php';
include_once 'header.php';

// --- LOGIKA PENCARIAN & FILTER YANG LEBIH AMAN (PREPARED STATEMENTS) ---
$sql = "SELECT p.*, k.nama_kategori 
        FROM produk p
        JOIN kategori k ON p.id_kategori = k.id 
        WHERE 1=1";

$types = "";
$params = [];

// Filter Pencarian (Keyword)
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $keyword = "%" . $_GET['q'] . "%";
    $sql .= " AND (p.nama_barang LIKE ? OR p.deskripsi LIKE ?)";
    $types .= "ss";
    $params[] = $keyword;
    $params[] = $keyword;
}

// Filter Kategori
if (isset($_GET['kategori']) && !empty($_GET['kategori'])) {
    $kat_id = intval($_GET['kategori']);
    $sql .= " AND p.id_kategori = ?";
    $types .= "i";
    $params[] = $kat_id;
}

$sql .= " ORDER BY p.id DESC";

// Eksekusi Query
$stmt = $conn->prepare($sql);
if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Ambil Daftar Kategori untuk Sidebar
$res_kat = $conn->query("SELECT * FROM kategori");

// AMBIL DATA BANNER (Cek tabel banner ada atau tidak untuk menghindari error)
$banners = false;
$cek_tabel = $conn->query("SHOW TABLES LIKE 'banner'");
if ($cek_tabel->num_rows > 0) {
    $banners = $conn->query("SELECT * FROM banner WHERE aktif = 1 ORDER BY id DESC");
}
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
        <div class="sidebar-desktop">
            <h4 class="sidebar-title">Kategori</h4>
            <ul class="category-list">
                <li>
                    <a href="index.php" class="<?php echo !isset($_GET['kategori']) ? 'active' : ''; ?>">
                        <i class="fas fa-th-large"></i> Semua Produk
                    </a>
                </li>
                <?php while($kat = $res_kat->fetch_assoc()): ?>
                <li>
                    <a href="index.php?kategori=<?php echo $kat['id']; ?>" class="<?php echo (isset($_GET['kategori']) && $_GET['kategori'] == $kat['id']) ? 'active' : ''; ?>">
                        <i class="fas fa-angle-right"></i> <?php echo htmlspecialchars($kat['nama_kategori']); ?>
                    </a>
                </li>
                <?php endwhile; ?>
            </ul>
        </div>

        <!-- KONTEN UTAMA -->
        <div class="main-content-area" style="flex: 4; width: 100%;">
            
            <!-- BANNER SLIDER DINAMIS -->
            <?php if(!isset($_GET['q']) && !isset($_GET['kategori']) && $banners && $banners->num_rows > 0): ?>
                
                <style>
                    .slider-container {
                        width: 100%; height: 320px; overflow: hidden; position: relative;
                        border-radius: 10px; margin-bottom: 25px;
                        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                    }
                    .slides { display: flex; width: 100%; height: 100%; transition: transform 0.5s ease-in-out; }
                    .slide { min-width: 100%; height: 100%; position: relative; }
                    .slide img { width: 100%; height: 100%; object-fit: cover; }
                    .slider-nav {
                        position: absolute; top: 50%; width: 100%; display: flex;
                        justify-content: space-between; padding: 0 20px; transform: translateY(-50%); pointer-events: none;
                    }
                    .slider-btn {
                        pointer-events: auto; background: rgba(0,0,0,0.4); color: white;
                        border: none; width: 45px; height: 45px; border-radius: 50%;
                        cursor: pointer; display: flex; align-items: center; justify-content: center;
                        font-size: 18px; transition: 0.3s; backdrop-filter: blur(2px);
                    }
                    .slider-btn:hover { background: var(--primary-color); transform: scale(1.1); }
                </style>

                <div class="slider-container" id="promoSlider">
                    <div class="slides" id="slidesTrack">
                        <?php while($ban = $banners->fetch_assoc()): 
                            $img_banner = !empty($ban['gambar']) && file_exists("images/banner/".$ban['gambar']) 
                                ? "images/banner/".$ban['gambar'] 
                                : "https://via.placeholder.com/1200x400?text=Promo+Matria+Mart";
                        ?>
                            <div class="slide">
                                <a href="<?php echo !empty($ban['link']) ? $ban['link'] : '#'; ?>">
                                    <img src="<?php echo $img_banner; ?>" alt="<?php echo htmlspecialchars($ban['judul']); ?>">
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
                    if(track) {
                        const totalSlides = track.children.length;
                        let autoPlay = setInterval(() => moveSlide(1), 5000); // 5 detik

                        function moveSlide(direction) {
                            currentSlide += direction;
                            if (currentSlide >= totalSlides) currentSlide = 0;
                            if (currentSlide < 0) currentSlide = totalSlides - 1;
                            track.style.transform = `translateX(-${currentSlide * 100}%)`;
                            clearInterval(autoPlay);
                            autoPlay = setInterval(() => moveSlide(1), 5000);
                        }
                    }
                </script>

            <?php elseif(!isset($_GET['q']) && !isset($_GET['kategori'])): ?>
                <!-- Fallback Banner Statis -->
                <div class="welcome-banner">
                    <div class="welcome-text">
                        <h2>Selamat Datang di MATRIA.MART</h2>
                        <p>Pusat belanja material bangunan modern, lengkap, dan terpercaya.</p>
                        <a href="#produk-list" class="btn btn-light" style="margin-top: 15px; color: var(--primary-color);">Belanja Sekarang</a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Header Hasil Pencarian -->
                <div class="search-header">
                    <span>Menampilkan hasil: <strong><?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : 'Filter Kategori'; ?></strong></span>
                    <a href="index.php" class="reset-filter"><i class="fas fa-times"></i> Reset Filter</a>
                </div>
            <?php endif; ?>

            <!-- Grid Produk -->
            <div class="product-grid" id="produk-list">
                <?php
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $harga = number_format($row['harga_ecer'], 0, ',', '.');
                        $harga_grosir = number_format($row['harga_grosir'], 0, ',', '.');
                        $gambar = !empty($row['gambar']) && file_exists("images/".$row['gambar']) 
                                  ? 'images/' . $row['gambar'] 
                                  : 'https://via.placeholder.com/300x300?text=No+Image';
                        ?>
                        
                        <a href="produk.php?id=<?php echo $row['id']; ?>" class="product-card">
                            <div class="img-wrapper">
                                <img src="<?php echo $gambar; ?>" alt="<?php echo htmlspecialchars($row['nama_barang']); ?>" loading="lazy">
                                <div class="overlay-hover">Lihat Detail</div>
                            </div>
                            <div class="product-info">
                                <div class="badge-category"><?php echo htmlspecialchars($row['nama_kategori']); ?></div>
                                <h3 class="product-title"><?php echo htmlspecialchars($row['nama_barang']); ?></h3>
                                
                                <div class="product-price">Rp <?php echo $harga; ?></div>
                                
                                <div class="product-grosir">
                                    <i class="fas fa-tags"></i> Grosir: Rp <?php echo $harga_grosir; ?> 
                                    <span>(Min. <?php echo $row['min_belanja_grosir']; ?> <?php echo $row['satuan']; ?>)</span>
                                </div>
                            </div>
                        </a>
                        <?php
                    }
                } else {
                    echo "<div class='empty-state'>
                            <i class='fas fa-search fa-3x'></i>
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
/* CSS Tambahan Khusus Halaman Index */
.welcome-banner {
    background: linear-gradient(135deg, var(--primary-color), #ff8a50);
    padding: 40px; border-radius: 10px; margin-bottom: 25px;
    box-shadow: 0 10px 20px rgba(255, 87, 34, 0.2);
    color: white; position: relative; overflow: hidden;
}
.welcome-banner::before {
    content: ''; position: absolute; top: -50%; right: -10%; width: 300px; height: 300px;
    background: rgba(255,255,255,0.1); border-radius: 50%;
}
.search-header {
    margin-bottom: 20px; background: white; padding: 15px; border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center;
}
.reset-filter { font-size: 13px; color: #e74c3c; font-weight: 500; }
.sidebar-desktop {
    flex: 1; min-width: 220px; background: white; padding: 0; border-radius: 8px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05); position: sticky; top: 90px; overflow: hidden;
}
.sidebar-title {
    margin: 0; padding: 15px 20px; background: var(--nav-bg); color: white;
    text-transform: uppercase; font-size: 14px; letter-spacing: 1px;
}
.category-list { font-size: 14px; }
.category-list li { border-bottom: 1px solid #f0f0f0; }
.category-list a {
    display: block; padding: 12px 20px; color: #555; transition: 0.2s;
    display: flex; align-items: center; gap: 10px;
}
.category-list a:hover, .category-list a.active {
    background: #fdfdfd; color: var(--primary-color); padding-left: 25px; font-weight: 500;
}
.category-list i { font-size: 12px; color: #ccc; }
.category-list a:hover i { color: var(--primary-color); }

.empty-state {
    grid-column: 1/-1; text-align:center; padding: 60px 20px;
    background:white; width:100%; border-radius: 8px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}
.empty-state i { color:#e0e0e0; margin-bottom:20px; }
.empty-state p { color: #666; font-size: 1.1rem; margin-bottom: 15px; }

@media (max-width: 768px) {
    .mobile-filter-btn { display: block !important; }
    .sidebar-desktop { 
        display: none; width: 100%; margin-bottom: 20px; position: static !important; 
    }
    .sidebar-desktop.active { display: block !important; animation: fadeIn 0.3s; }
}
@keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<?php
include_once 'footer.php';
?>
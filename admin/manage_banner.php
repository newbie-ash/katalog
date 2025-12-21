<?php
// admin/manage_banner.php
include '../db_koneksi.php';
include 'header.php';

// --- LOGIKA HAPUS ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Hapus file fisik
    $q_img = $conn->query("SELECT gambar FROM banner WHERE id=$id");
    if ($q_img->num_rows > 0) {
        $file = $q_img->fetch_assoc()['gambar'];
        if (file_exists("../images/banner/" . $file)) {
            unlink("../images/banner/" . $file);
        }
    }
    // Hapus data DB
    $conn->query("DELETE FROM banner WHERE id=$id");
    echo "<script>window.location='manage_banner.php';</script>";
    exit;
}

// --- LOGIKA TAMBAH BANNER ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $link = $_POST['link'];
    
    // Upload Gambar
    $gambar = '';
    if (!empty($_FILES['gambar']['name'])) {
        $target_dir = "../images/banner/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = "banner_" . time() . "." . $ext;
        
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_dir . $gambar)) {
            $stmt = $conn->prepare("INSERT INTO banner (judul, gambar, link, aktif) VALUES (?, ?, ?, 1)");
            $stmt->bind_param("sss", $judul, $gambar, $link);
            $stmt->execute();
            echo "<script>alert('Banner berhasil ditambahkan!'); window.location='manage_banner.php';</script>";
        } else {
            echo "<script>alert('Gagal upload gambar.');</script>";
        }
    } else {
        echo "<script>alert('Pilih gambar terlebih dahulu.');</script>";
    }
}

$banners = $conn->query("SELECT * FROM banner ORDER BY id DESC");
?>

<h2 class="page-title">Kelola Banner Promo</h2>

<style>
    .card-box { background: #fff; padding: 25px; border-radius: 5px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
    .table { width: 100%; border-collapse: collapse; }
    .table th, .table td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
    .form-group { margin-bottom: 15px; }
    .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
    .btn { padding: 10px 15px; border: none; border-radius: 4px; color: white; cursor: pointer; }
    .btn-primary { background: #2196f3; }
    .btn-danger { background: #e74c3c; }
</style>

<div style="display: flex; gap: 30px; flex-wrap: wrap;">
    
    <!-- Form Tambah (Kiri) -->
    <div style="flex: 1; min-width: 300px;">
        <div class="card-box">
            <h3 style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">Tambah Banner Baru</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Judul Banner (Opsional)</label>
                    <input type="text" name="judul" class="form-control" placeholder="Contoh: Promo Agustusan">
                </div>
                <div class="form-group">
                    <label>Link Tujuan (Opsional)</label>
                    <input type="text" name="link" class="form-control" placeholder="Contoh: produk.php?id=5 (Kosongkan jika tidak ada)">
                </div>
                <div class="form-group">
                    <label>Gambar Banner (Wajib)</label>
                    <input type="file" name="gambar" class="form-control" required accept="image/*">
                    <small style="color: #888;">Disarankan ukuran 1200x400 px</small>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-upload"></i> Upload Banner
                </button>
            </form>
        </div>
    </div>

    <!-- List Banner (Kanan) -->
    <div style="flex: 2; min-width: 400px;">
        <div class="card-box">
            <h3 style="margin-bottom: 20px;">Daftar Banner Aktif</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th width="120">Preview</th>
                        <th>Info</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($banners->num_rows > 0): ?>
                        <?php while($row = $banners->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <img src="../images/banner/<?php echo $row['gambar']; ?>" style="width: 100px; height: 50px; object-fit: cover; border-radius: 4px;">
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['judul']); ?></strong><br>
                                    <small style="color: #666;">Link: <?php echo $row['link'] ? $row['link'] : '-'; ?></small>
                                </td>
                                <td>
                                    <a href="manage_banner.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Hapus banner ini?')" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="3" style="text-align: center;">Belum ada banner.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
<?php
// admin/manage_produk.php
include '../db_koneksi.php';
include 'header.php';

// Hapus Produk
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM produk WHERE id=$id");
    echo "<script>window.location='manage_produk.php';</script>";
}

// Tambah/Edit Logic
$mode = 'tambah';
$id_edit = 0;
$data_edit = [];

if (isset($_GET['edit'])) {
    $mode = 'edit';
    $id_edit = intval($_GET['edit']);
    $data_edit = $conn->query("SELECT * FROM produk WHERE id=$id_edit")->fetch_assoc();
}

// Proses Simpan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama_barang'];
    $kategori = $_POST['id_kategori'];
    $harga_ecer = $_POST['harga_ecer'];
    $harga_grosir = $_POST['harga_grosir'];
    $min_grosir = $_POST['min_belanja_grosir'];
    $stok = $_POST['stok'];
    $satuan = $_POST['satuan'];
    $deskripsi = $_POST['deskripsi'];
    
    // Handle Gambar
    $gambar = ($mode == 'edit') ? $data_edit['gambar'] : '';
    if (!empty($_FILES['gambar']['name'])) {
        $target_dir = "../images/";
        if (!file_exists($target_dir)) mkdir($target_dir);
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = "prod_" . time() . "." . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], $target_dir . $gambar);
    }

    if ($mode == 'tambah') {
        $stmt = $conn->prepare("INSERT INTO produk (id_kategori, nama_barang, deskripsi, harga_ecer, harga_grosir, min_belanja_grosir, stok, satuan, gambar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issiiiiss", $kategori, $nama, $deskripsi, $harga_ecer, $harga_grosir, $min_grosir, $stok, $satuan, $gambar);
    } else {
        $stmt = $conn->prepare("UPDATE produk SET id_kategori=?, nama_barang=?, deskripsi=?, harga_ecer=?, harga_grosir=?, min_belanja_grosir=?, stok=?, satuan=?, gambar=? WHERE id=?");
        $stmt->bind_param("issiiiissi", $kategori, $nama, $deskripsi, $harga_ecer, $harga_grosir, $min_grosir, $stok, $satuan, $gambar, $id_edit);
    }
    
    if($stmt->execute()) {
        echo "<script>alert('Berhasil menyimpan produk'); window.location='manage_produk.php';</script>";
    }
}

$produk = $conn->query("SELECT p.*, k.nama_kategori FROM produk p JOIN kategori k ON p.id_kategori = k.id ORDER BY p.id DESC");
$kategoris = $conn->query("SELECT * FROM kategori");
?>

<h2 class="page-title">Kelola Produk</h2>

<style>
    /* Custom style for this page */
    .card-box {
        background: #fff;
        padding: 25px;
        border-radius: 5px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: 500; color: #555; }
    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }
    .form-control:focus { outline: none; border-color: #2196f3; }
    
    .btn { padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; color: white; font-size: 14px; }
    .btn-primary { background: #2196f3; }
    .btn-secondary { background: #7f8c8d; }
    .btn-danger { background: #e74c3c; }
    .btn-block { display: block; width: 100%; }

    .table { width: 100%; border-collapse: collapse; }
    .table th { background: #f8f9fa; padding: 12px; text-align: left; border-bottom: 2px solid #eee; color: #666; }
    .table td { padding: 12px; border-bottom: 1px solid #eee; color: #444; vertical-align: middle; }
</style>

<div style="display: flex; gap: 30px; flex-wrap: wrap;">
    
    <!-- Bagian Form (Kiri) -->
    <div style="flex: 1; min-width: 300px;">
        <div class="card-box">
            <h3 style="margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #eee;">
                <?php echo ($mode == 'edit') ? 'Edit Produk' : 'Tambah Produk Baru'; ?>
            </h3>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Nama Barang</label>
                    <input type="text" name="nama_barang" class="form-control" required value="<?php echo @$data_edit['nama_barang']; ?>">
                </div>
                
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="id_kategori" class="form-control" required>
                        <?php 
                        $kategoris->data_seek(0); // Reset pointer
                        while($kat = $kategoris->fetch_assoc()): 
                        ?>
                            <option value="<?php echo $kat['id']; ?>" <?php echo (@$data_edit['id_kategori'] == $kat['id']) ? 'selected' : ''; ?>>
                                <?php echo $kat['nama_kategori']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div style="display:flex; gap:10px;">
                    <div class="form-group" style="flex:1;">
                        <label>Harga Ecer</label>
                        <input type="number" name="harga_ecer" class="form-control" required value="<?php echo @$data_edit['harga_ecer']; ?>">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Harga Grosir</label>
                        <input type="number" name="harga_grosir" class="form-control" required value="<?php echo @$data_edit['harga_grosir']; ?>">
                    </div>
                </div>

                <div style="display:flex; gap:10px;">
                    <div class="form-group" style="flex:1;">
                        <label>Min. Beli Grosir</label>
                        <input type="number" name="min_belanja_grosir" class="form-control" required value="<?php echo @$data_edit['min_belanja_grosir']; ?>">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Stok</label>
                        <input type="number" name="stok" class="form-control" required value="<?php echo @$data_edit['stok']; ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Satuan (zak, pcs, meter)</label>
                    <input type="text" name="satuan" class="form-control" required value="<?php echo @$data_edit['satuan']; ?>">
                </div>

                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="form-control"><?php echo @$data_edit['deskripsi']; ?></textarea>
                </div>

                <div class="form-group">
                    <label>Gambar</label>
                    <input type="file" name="gambar" class="form-control" style="padding: 5px;">
                    <?php if(!empty(@$data_edit['gambar'])): ?>
                        <div style="margin-top:5px; font-size:12px; color:#666;">
                            File saat ini: <?php echo $data_edit['gambar']; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-save"></i> Simpan Produk
                </button>
                
                <?php if($mode == 'edit'): ?>
                    <a href="manage_produk.php" class="btn btn-secondary btn-block" style="margin-top:10px; text-align:center; display:block;">Batal Edit</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Bagian Tabel (Kanan) -->
    <div style="flex: 2; min-width: 400px;">
        <div class="card-box">
            <h3 style="margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #eee;">Daftar Produk</h3>
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($produk->num_rows > 0): ?>
                            <?php while($row = $produk->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <strong style="color:#2c3e50; font-size:15px;"><?php echo htmlspecialchars($row['nama_barang']); ?></strong><br>
                                    <span style="font-size:12px; background:#e1f5fe; color:#0288d1; padding:2px 6px; border-radius:4px;">
                                        <?php echo $row['nama_kategori']; ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="font-size:13px;">Ecer: <b>Rp <?php echo number_format($row['harga_ecer']); ?></b></div>
                                    <div style="font-size:12px; color:#666;">Grosir: Rp <?php echo number_format($row['harga_grosir']); ?></div>
                                </td>
                                <td>
                                    <span style="background:#f1f8e9; color:#33691e; padding:3px 8px; border-radius:4px; font-weight:bold;">
                                        <?php echo $row['stok']; ?> <?php echo $row['satuan']; ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display:flex; gap:5px;">
                                        <a href="manage_produk.php?edit=<?php echo $row['id']; ?>" class="btn btn-secondary" style="padding:6px 10px;" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="manage_produk.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Hapus produk ini?')" class="btn btn-danger" style="padding:6px 10px;" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" style="text-align:center; padding:20px;">Belum ada data produk.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php include 'footer.php'; ?>
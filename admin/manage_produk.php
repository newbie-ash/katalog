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

// Tambah/Edit Logic ada di file terpisah atau modal, tapi kita buat sederhana di satu file untuk kemudahan user
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

<div class="container">
    <div style="display:flex; gap: 20px;">
        <!-- Form Section -->
        <div style="flex: 1; background: white; padding: 20px; border-radius: 4px; height: fit-content;">
            <h3><?php echo ($mode == 'edit') ? 'Edit Produk' : 'Tambah Produk Baru'; ?></h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Nama Barang</label>
                    <input type="text" name="nama_barang" required value="<?php echo @$data_edit['nama_barang']; ?>">
                </div>
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="id_kategori" required style="width:100%; padding:10px;">
                        <?php while($kat = $kategoris->fetch_assoc()): ?>
                            <option value="<?php echo $kat['id']; ?>" <?php echo (@$data_edit['id_kategori'] == $kat['id']) ? 'selected' : ''; ?>>
                                <?php echo $kat['nama_kategori']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div style="display:flex; gap:10px;">
                    <div class="form-group" style="flex:1;">
                        <label>Harga Ecer</label>
                        <input type="number" name="harga_ecer" required value="<?php echo @$data_edit['harga_ecer']; ?>">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Harga Grosir</label>
                        <input type="number" name="harga_grosir" required value="<?php echo @$data_edit['harga_grosir']; ?>">
                    </div>
                </div>
                <div style="display:flex; gap:10px;">
                    <div class="form-group" style="flex:1;">
                        <label>Min. Beli Grosir</label>
                        <input type="number" name="min_belanja_grosir" required value="<?php echo @$data_edit['min_belanja_grosir']; ?>">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Stok</label>
                        <input type="number" name="stok" required value="<?php echo @$data_edit['stok']; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Satuan (zak, pcs, meter)</label>
                    <input type="text" name="satuan" required value="<?php echo @$data_edit['satuan']; ?>">
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" rows="3" style="width:100%"><?php echo @$data_edit['deskripsi']; ?></textarea>
                </div>
                <div class="form-group">
                    <label>Gambar</label>
                    <input type="file" name="gambar">
                    <?php if(!empty(@$data_edit['gambar'])): ?>
                        <br><small>Gambar saat ini: <?php echo $data_edit['gambar']; ?></small>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Simpan Produk</button>
                <?php if($mode == 'edit'): ?>
                    <a href="manage_produk.php" class="btn btn-secondary btn-block" style="margin-top:5px;">Batal</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Table Section -->
        <div style="flex: 2; background: white; padding: 20px; border-radius: 4px;">
            <h3>Daftar Produk</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $produk->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <b><?php echo htmlspecialchars($row['nama_barang']); ?></b><br>
                                <small><?php echo $row['nama_kategori']; ?></small>
                            </td>
                            <td>
                                Ecer: <?php echo number_format($row['harga_ecer']); ?><br>
                                Grosir: <?php echo number_format($row['harga_grosir']); ?>
                            </td>
                            <td><?php echo $row['stok']; ?> <?php echo $row['satuan']; ?></td>
                            <td>
                                <a href="manage_produk.php?edit=<?php echo $row['id']; ?>" class="btn btn-secondary" style="padding:2px 5px; font-size:12px;"><i class="fas fa-edit"></i></a>
                                <a href="manage_produk.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Hapus?')" class="btn btn-danger" style="padding:2px 5px; font-size:12px;"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
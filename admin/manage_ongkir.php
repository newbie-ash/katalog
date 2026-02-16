<?php
// admin/manage_ongkir.php
include '../db_koneksi.php';
include 'header.php';

// --- HAPUS DATA ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM ongkir WHERE id=$id");
    echo "<script>window.location='manage_ongkir.php';</script>";
}

// --- TAMBAH / EDIT DATA ---
$mode = 'tambah';
$id_edit = 0;
$data_edit = [];

if (isset($_GET['edit'])) {
    $mode = 'edit';
    $id_edit = intval($_GET['edit']);
    $data_edit = $conn->query("SELECT * FROM ongkir WHERE id=$id_edit")->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kota = $_POST['nama_kota'];
    $tarif = intval($_POST['tarif']);

    if ($mode == 'tambah') {
        $stmt = $conn->prepare("INSERT INTO ongkir (nama_kota, tarif) VALUES (?, ?)");
        $stmt->bind_param("si", $kota, $tarif);
    } else {
        $stmt = $conn->prepare("UPDATE ongkir SET nama_kota=?, tarif=? WHERE id=?");
        $stmt->bind_param("sii", $kota, $tarif, $id_edit);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Data Ongkir Berhasil Disimpan'); window.location='manage_ongkir.php';</script>";
    }
}

$data_ongkir = $conn->query("SELECT * FROM ongkir ORDER BY nama_kota ASC");
?>

<h2 class="page-title">Kelola Ongkos Kirim</h2>

<style>
    .card-box { background: #fff; padding: 25px; border-radius: 5px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
    .table { width: 100%; border-collapse: collapse; }
    .table th, .table td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
    .form-group { margin-bottom: 15px; }
    .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
    .btn { padding: 10px 15px; border: none; border-radius: 4px; color: white; cursor: pointer; }
    .btn-primary { background: #2196f3; }
    .btn-warning { background: #f39c12; }
    .btn-danger { background: #e74c3c; }
</style>

<div style="display: flex; gap: 30px; flex-wrap: wrap;">
    
    <!-- Form Input (Kiri) -->
    <div style="flex: 1; min-width: 300px;">
        <div class="card-box">
            <h3 style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                <?php echo ($mode == 'edit') ? 'Edit Tarif' : 'Tambah Tarif Kota'; ?>
            </h3>
            <form method="POST">
                <div class="form-group">
                    <label>Nama Kota / Kabupaten</label>
                    <input type="text" name="nama_kota" class="form-control" required placeholder="Contoh: Jakarta Selatan" value="<?php echo @$data_edit['nama_kota']; ?>">
                </div>
                <div class="form-group">
                    <label>Tarif Ongkir (Rp)</label>
                    <input type="number" name="tarif" class="form-control" required placeholder="Contoh: 15000" value="<?php echo @$data_edit['tarif']; ?>">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Simpan Data</button>
                <?php if($mode == 'edit'): ?>
                    <a href="manage_ongkir.php" class="btn btn-warning" style="display:block; text-align:center; margin-top:10px;">Batal</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Tabel Data (Kanan) -->
    <div style="flex: 2; min-width: 400px;">
        <div class="card-box">
            <h3 style="margin-bottom: 20px;">Daftar Ongkos Kirim</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Kota Tujuan</th>
                        <th>Tarif</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data_ongkir->num_rows > 0): ?>
                        <?php while($row = $data_ongkir->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['nama_kota']); ?></td>
                                <td>Rp <?php echo number_format($row['tarif'], 0, ',', '.'); ?></td>
                                <td>
                                    <a href="manage_ongkir.php?edit=<?php echo $row['id']; ?>" class="btn btn-warning" style="padding: 5px 10px; font-size: 12px;"><i class="fas fa-edit"></i></a>
                                    <a href="manage_ongkir.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Hapus data ini?')" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="3" style="text-align: center;">Belum ada data ongkir.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
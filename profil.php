<?php
// profil.php
include_once 'db_koneksi.php';
include_once 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['user_id'];
$msg = "";

// Update Profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama   = $_POST['nama'];
    $hp     = $_POST['no_hp'];
    $alamat = $_POST['alamat'];
    $kota   = $_POST['kota'];

    $stmt = $conn->prepare("UPDATE user SET nama=?, no_hp=?, alamat=?, kota=? WHERE id=?");
    $stmt->bind_param("ssssi", $nama, $hp, $alamat, $kota, $id_user);
    
    if ($stmt->execute()) {
        $_SESSION['user_nama'] = $nama; // Update sesi nama
        $msg = "<div class='message success'>Profil berhasil diperbarui!</div>";
    } else {
        $msg = "<div class='message error'>Gagal memperbarui profil.</div>";
    }
}

// Ambil Data User
$data = $conn->query("SELECT * FROM user WHERE id = $id_user")->fetch_assoc();
?>

<div class="container">
    <div class="auth-wrapper" style="min-height: auto; margin-top: 20px;">
        <div class="auth-box" style="max-width: 600px; text-align: left;">
            <h2 style="border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">Profil Saya</h2>
            <?php echo $msg; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" value="<?php echo htmlspecialchars($data['nama']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email (Tidak dapat diubah)</label>
                    <input type="email" value="<?php echo htmlspecialchars($data['email']); ?>" disabled style="background: #eee;">
                </div>
                <div class="form-group">
                    <label>Nomor HP</label>
                    <input type="text" name="no_hp" value="<?php echo htmlspecialchars($data['no_hp']); ?>" required>
                </div>
                
                <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
                <h4 style="margin-bottom: 15px;">Alamat Pengiriman (Wajib diisi untuk belanja)</h4>

                <div class="form-group">
                    <label>Kota / Kabupaten</label>
                    <input type="text" name="kota" value="<?php echo htmlspecialchars($data['kota']); ?>" placeholder="Contoh: Surabaya" required>
                </div>
                <div class="form-group">
                    <label>Alamat Lengkap</label>
                    <textarea name="alamat" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ccc;" required><?php echo htmlspecialchars($data['alamat']); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>

<?php include_once 'footer.php'; ?>
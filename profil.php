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

// Ambil Data User Awal
$data = $conn->query("SELECT * FROM user WHERE id = $id_user")->fetch_assoc();

// Update Profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama   = $_POST['nama'];
    $hp     = $_POST['no_hp'];
    $alamat = $_POST['alamat'];
    $kota   = $_POST['kota'];
    
    // Handle Upload Foto
    $foto_name = $data['foto']; // Default pakai foto lama
    if (!empty($_FILES['foto']['name'])) {
        $target_dir = "images/users/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $new_name = "user_" . $id_user . "_" . time() . "." . $ext;
        
        if(move_uploaded_file($_FILES['foto']['tmp_name'], $target_dir . $new_name)) {
            $foto_name = $new_name;
            // Update session foto jika perlu (opsional)
            $_SESSION['user_foto'] = $foto_name;
        }
    }

    $stmt = $conn->prepare("UPDATE user SET nama=?, no_hp=?, alamat=?, kota=?, foto=? WHERE id=?");
    $stmt->bind_param("sssssi", $nama, $hp, $alamat, $kota, $foto_name, $id_user);
    
    if ($stmt->execute()) {
        $_SESSION['user_nama'] = $nama; // Update sesi nama
        $msg = "<div class='message success'>Profil berhasil diperbarui!</div>";
        // Refresh data agar tampilan foto langsung berubah
        $data = $conn->query("SELECT * FROM user WHERE id = $id_user")->fetch_assoc();
    } else {
        $msg = "<div class='message error'>Gagal memperbarui profil.</div>";
    }
}

// Tentukan URL Foto
$foto_profil = !empty($data['foto']) ? 'images/users/' . $data['foto'] : 'https://via.placeholder.com/150?text=No+Photo';
?>

<div class="container">
    <div class="auth-wrapper" style="min-height: auto; margin-top: 20px;">
        <div class="auth-box" style="max-width: 600px; text-align: left; position: relative;">
            <h2 style="border-bottom: 2px solid var(--primary-color); padding-bottom: 10px; color: #333;">Profil Saya</h2>
            <?php echo $msg; ?>

            <form method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 20px;">
                
                <!-- Bagian Foto Profil -->
                <div style="text-align: center; margin-bottom: 10px;">
                    <div style="width: 120px; height: 120px; margin: 0 auto 10px; border-radius: 50%; overflow: hidden; border: 3px solid var(--primary-color); background: #eee;">
                        <img src="<?php echo $foto_profil; ?>" alt="Foto Profil" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <label for="foto-upload" style="cursor: pointer; color: var(--primary-color); font-weight: bold; font-size: 14px;">
                        <i class="fas fa-camera"></i> Ganti Foto
                    </label>
                    <input type="file" name="foto" id="foto-upload" style="display: none;" accept="image/*" onchange="previewImage(this)">
                </div>

                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" value="<?php echo htmlspecialchars($data['nama']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email (Tidak dapat diubah)</label>
                    <input type="email" value="<?php echo htmlspecialchars($data['email']); ?>" disabled style="background: #eee; cursor: not-allowed;">
                </div>
                <div class="form-group">
                    <label>Nomor HP</label>
                    <input type="text" name="no_hp" value="<?php echo htmlspecialchars($data['no_hp']); ?>" required>
                </div>
                
                <hr style="margin: 10px 0; border: 0; border-top: 1px solid #eee;">
                <h4 style="margin-bottom: 10px; color: #555;">Alamat Pengiriman</h4>

                <div class="form-group">
                    <label>Kota / Kabupaten</label>
                    <input type="text" name="kota" value="<?php echo htmlspecialchars($data['kota']); ?>" placeholder="Contoh: Surabaya" required>
                </div>
                <div class="form-group">
                    <label>Alamat Lengkap</label>
                    <textarea name="alamat" rows="3" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit;" required><?php echo htmlspecialchars($data['alamat']); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="justify-content: center;">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.querySelector('.auth-box img').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include_once 'footer.php'; ?>
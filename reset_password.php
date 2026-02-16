<?php
// reset_password.php
session_start();
include_once 'db_koneksi.php';
include_once 'header.php';

$msg = "";
$token = isset($_GET['token']) ? $_GET['token'] : '';
$valid_token = false;

// 1. Validasi Token saat halaman dibuka
if (!empty($token)) {
    $now = date("Y-m-d H:i:s");
    $stmt = $conn->prepare("SELECT id FROM user WHERE reset_token = ? AND reset_expiry > ?");
    $stmt->bind_param("ss", $token, $now);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $valid_token = true;
    } else {
        $msg = "<div class='message error'>Link reset password tidak valid atau sudah kadaluarsa.</div>";
    }
} else {
    $msg = "<div class='message error'>Token tidak ditemukan.</div>";
}

// 2. Proses Ubah Password
if ($_SERVER["REQUEST_METHOD"] == "POST" && $valid_token) {
    $pass1 = $_POST['password'];
    $pass2 = $_POST['confirm_password'];
    
    if ($pass1 !== $pass2) {
        $msg = "<div class='message error'>Konfirmasi password tidak cocok.</div>";
    } elseif (strlen($pass1) < 6) {
        $msg = "<div class='message error'>Password minimal 6 karakter.</div>";
    } else {
        $new_hash = password_hash($pass1, PASSWORD_DEFAULT);
        
        // Update password & Hapus token agar tidak bisa dipakai lagi
        $update = $conn->prepare("UPDATE user SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE reset_token = ?");
        $update->bind_param("ss", $new_hash, $token);
        
        if ($update->execute()) {
            echo "<div class='auth-wrapper'>
                    <div class='auth-box' style='text-align:center;'>
                        <i class='fas fa-check-circle fa-4x' style='color:#2ecc71; margin-bottom:20px;'></i>
                        <h3>Berhasil!</h3>
                        <p>Password Anda telah diperbarui.</p>
                        <a href='login.php' class='btn btn-primary btn-block' style='margin-top:20px;'>Login Sekarang</a>
                    </div>
                  </div>";
            include_once 'footer.php';
            exit;
        } else {
            $msg = "<div class='message error'>Gagal mengupdate password.</div>";
        }
    }
}
?>

<div class="auth-wrapper">
    <div class="auth-box">
        <h2 style="color: #333; margin-bottom: 5px;">Reset Password</h2>
        <p style="color: #777; font-size: 14px; margin-bottom: 25px;">Silakan buat password baru Anda.</p>
        
        <?php echo $msg; ?>

        <?php if ($valid_token): ?>
        <form method="POST">
            <div class="form-group">
                <label>Password Baru</label>
                <input type="password" name="password" required placeholder="Minimal 6 karakter">
            </div>
            <div class="form-group">
                <label>Konfirmasi Password Baru</label>
                <input type="password" name="confirm_password" required placeholder="Ulangi password baru">
            </div>
            <button type="submit" class="btn btn-primary btn-block" style="justify-content: center; padding: 12px;">
                SIMPAN PASSWORD BARU
            </button>
        </form>
        <?php else: ?>
            <a href="lupa_password.php" class="btn btn-primary btn-block" style="text-align:center; margin-top:20px;">Kirim Ulang Link</a>
        <?php endif; ?>
    </div>
</div>

<?php include_once 'footer.php'; ?>
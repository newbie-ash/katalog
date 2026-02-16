<?php
// register.php
include_once 'db_koneksi.php';
include_once 'header.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $message = "<div class='message error'>Password tidak cocok.</div>";
    } else {
        // Cek Email
        $cek = $conn->prepare("SELECT id FROM user WHERE email = ?");
        $cek->bind_param("s", $email);
        $cek->execute();
        
        if ($cek->get_result()->num_rows > 0) {
            $message = "<div class='message error'>Email sudah terdaftar.</div>";
        } else {
            // Insert
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO user (nama, email, password, role) VALUES (?, ?, ?, 'pembeli')");
            $stmt->bind_param("sss", $nama, $email, $hash);
            
            if ($stmt->execute()) {
                echo "<script>alert('Pendaftaran Berhasil! Silakan Login.'); window.location='login.php';</script>";
                exit;
            } else {
                $message = "<div class='message error'>Gagal mendaftar.</div>";
            }
        }
    }
}
?>

<div class="auth-wrapper">
    <div class="auth-box">
        <h2 style="color: #333; margin-bottom: 5px;">Buat Akun Baru</h2>
        <p style="color: #777; font-size: 14px; margin-bottom: 25px;">Gabung dan nikmati kemudahan belanja material.</p>
        
        <?php echo $message; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" required placeholder="Nama Anda">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="user@email.com">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Minimal 6 karakter">
            </div>
            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="confirm_password" required placeholder="Ulangi password">
            </div>
            <button type="submit" class="btn btn-primary btn-block" style="justify-content: center; padding: 12px;">
                DAFTAR SEKARANG
            </button>
        </form>
        
        <div style="margin-top: 25px; font-size: 14px; color: #666; border-top: 1px solid #eee; padding-top: 20px;">
            Sudah punya akun? <a href="login.php" style="color: var(--primary-color); font-weight: bold;">Login</a>
        </div>
    </div>
</div>

<?php include_once 'footer.php'; ?>
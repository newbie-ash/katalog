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
        <h2 style="color: var(--primary-color);">Daftar Akun Baru</h2>
        <?php echo $message; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">DAFTAR SEKARANG</button>
        </form>
        
        <p style="margin-top: 20px; font-size: 14px;">
            Sudah punya akun? <a href="login.php" style="color: var(--primary-color); font-weight: bold;">Login</a>
        </p>
    </div>
</div>

<?php include_once 'footer.php'; ?>
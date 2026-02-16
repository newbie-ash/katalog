<?php
// lupa_password.php
session_start();
include_once 'db_koneksi.php';
include_once 'header.php';

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    // 1. Cek apakah email terdaftar
    $stmt = $conn->prepare("SELECT id, nama FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // 2. Generate Token Unik & Expiry (1 Jam)
        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));
        
        // 3. Simpan Token ke Database
        $update = $conn->prepare("UPDATE user SET reset_token = ?, reset_expiry = ? WHERE email = ?");
        $update->bind_param("sss", $token, $expiry, $email);
        
        if ($update->execute()) {
            // --- SIMULASI PENGIRIMAN EMAIL (LOCALHOST) ---
            // Karena di localhost biasanya belum ada SMTP server, kita tampilkan linknya di layar.
            // Nanti di hosting asli, bagian ini diganti dengan fungsi mail() atau PHPMailer.
            
            $resetLink = "http://localhost/katalog2/reset_password.php?token=" . $token;
            // Sesuaikan "localhost/katalog2/" dengan folder project Anda
            
            $msg = "<div class='message success'>
                        <strong>Simulasi Email Terkirim!</strong><br>
                        Silakan klik link berikut untuk reset password:<br>
                        <a href='$resetLink' style='word-break:break-all;'>$resetLink</a>
                    </div>";
        } else {
            $msg = "<div class='message error'>Terjadi kesalahan sistem.</div>";
        }
    } else {
        // Demi keamanan, jangan beritahu jika email tidak ditemukan, tapi beri pesan umum
        // Atau untuk development boleh jujur.
        $msg = "<div class='message error'>Email tidak ditemukan dalam sistem kami.</div>";
    }
}
?>

<div class="auth-wrapper">
    <div class="auth-box">
        <h2 style="color: #333; margin-bottom: 5px;">Lupa Password?</h2>
        <p style="color: #777; font-size: 14px; margin-bottom: 25px;">Masukkan email Anda, kami akan mengirimkan link untuk reset password.</p>
        
        <?php echo $msg; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email Terdaftar</label>
                <input type="email" name="email" required placeholder="Contoh: user@email.com">
            </div>
            <button type="submit" class="btn btn-primary btn-block" style="justify-content: center; padding: 12px;">
                KIRIM LINK RESET
            </button>
        </form>
        
        <div style="margin-top: 25px; font-size: 14px; color: #666; border-top: 1px solid #eee; padding-top: 20px;">
            Sudah ingat password? <a href="login.php" style="color: var(--primary-color); font-weight: bold;">Login</a>
        </div>
    </div>
</div>

<?php include_once 'footer.php'; ?>
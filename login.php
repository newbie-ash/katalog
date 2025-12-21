<?php
// login.php
session_start();
include_once 'db_koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error_message = "Email dan password wajib diisi.";
    } else {
        $stmt = $conn->prepare("SELECT id, nama, password, role FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && (password_verify($password, $user['password']) || $password == $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nama'] = $user['nama'];
            $_SESSION['user_role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $error_message = "Email atau password salah.";
        }
    }
}
include_once 'header.php';
?>

<div class="auth-wrapper">
    <div class="auth-box">
        <div style="margin-bottom: 25px;">
            <i class="fas fa-user-circle" style="font-size: 50px; color: var(--primary-color);"></i>
        </div>
        <h2 style="color: #333; margin-bottom: 5px;">Selamat Datang</h2>
        <p style="color: #777; font-size: 14px; margin-bottom: 25px;">Silakan login untuk melanjutkan belanja.</p>
        
        <?php if ($error_message): ?>
            <div class="message error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="Contoh: user@email.com">
            </div>
            
            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <label style="margin-bottom: 0;">Password</label>
                    <!-- LINK LUPA PASSWORD DITAMBAHKAN DI SINI -->
                    <a href="lupa_password.php" style="font-size: 12px; color: var(--primary-color);">Lupa Password?</a>
                </div>
                <input type="password" name="password" required placeholder="Masukkan password Anda">
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="justify-content: center; padding: 12px;">
                MASUK SEKARANG
            </button>
        </form>
        
        <div style="margin-top: 25px; font-size: 14px; color: #666; border-top: 1px solid #eee; padding-top: 20px;">
            Belum punya akun? <a href="register.php" style="color: var(--primary-color); font-weight: bold;">Daftar disini</a>
        </div>
    </div>
</div>

<?php include_once 'footer.php'; ?>
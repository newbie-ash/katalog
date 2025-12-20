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
        <h2 style="color: var(--primary-color);">Login</h2>
        
        <?php if ($error_message): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="Masukkan email">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Masukkan password">
            </div>
            <button type="submit" class="btn btn-primary btn-block">MASUK</button>
        </form>
        
        <p style="margin-top: 20px; font-size: 14px;">
            Belum punya akun? <a href="register.php" style="color: var(--primary-color); font-weight: bold;">Daftar</a>
        </p>
    </div>
</div>

<?php include_once 'footer.php'; ?>
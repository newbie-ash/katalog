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

            // Redirect logic based on role
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

<!-- STYLE LOADING PAGE -->
<style>
    /* Variabel CSS untuk kecepatan animasi dinamis */
    :root {
        --bounce-speed: 1s; /* Default speed */
    }

    #loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.98); /* Sedikit transparan */
        z-index: 9999;
        display: none;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        transition: opacity 0.3s ease;
    }

    /* Style untuk Gambar Logo Loading */
    .store-icon-loading {
        width: 120px;  /* Atur lebar logo sesuai kebutuhan */
        height: auto;  /* Tinggi menyesuaikan proporsi */
        margin-bottom: 20px;
        border-radius: 10px; /* Opsional: memberi sudut melengkung pada gambar */
        
        /* Menggunakan variabel --bounce-speed agar bisa diubah via JS */
        animation: bounceStore var(--bounce-speed) infinite ease-in-out;
    }

    .loading-text {
        font-family: 'Roboto', sans-serif;
        font-size: 18px;
        color: #333;
        font-weight: 500;
        letter-spacing: 1px;
        animation: fadeText var(--bounce-speed) infinite ease-in-out;
        text-align: center;
        padding: 0 20px;
    }

    .network-info {
        margin-top: 10px;
        font-size: 12px;
        color: #888;
        font-style: italic;
    }

    @keyframes bounceStore {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.15); } /* Efek denyut sedikit diperhalus untuk gambar */
    }

    @keyframes fadeText {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
</style>

<!-- HTML STRUKTUR LOADING -->
<div id="loading-overlay">
    <!-- Ganti Icon FontAwesome dengan Gambar Logo -->
    <img src="images/1000099938.jpg" alt="Logo Loading" class="store-icon-loading" id="loadingIcon">
    
    <div class="loading-text" id="loadingText">Sedang Memproses...</div>
    <div class="network-info" id="networkInfo"></div>
</div>

<!-- KONTEN UTAMA HALAMAN LOGIN -->
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

        <form method="POST" id="loginForm">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="Contoh: user@email.com">
            </div>
            
            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <label style="margin-bottom: 0;">Password</label>
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

<!-- SCRIPT PENGENDALI LOADING BERDASARKAN JARINGAN -->
<script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        // Cegah submit form default dulu
        e.preventDefault(); 
        
        const overlay = document.getElementById('loading-overlay');
        const textElement = document.getElementById('loadingText');
        const infoElement = document.getElementById('networkInfo');
        const iconElement = document.getElementById('loadingIcon');
        const form = this;

        // Tampilkan overlay
        overlay.style.display = 'flex';

        // Deteksi Koneksi
        const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
        let connectionType = connection ? connection.effectiveType : '4g'; // Default ke 4g jika API tidak support
        
        let delayTime = 0;

        console.log("Tipe Koneksi: " + connectionType);

        if (connectionType === '4g') {
            // JIKA KONEKSI CEPAT (4G)
            document.documentElement.style.setProperty('--bounce-speed', '0.8s'); 
            textElement.innerHTML = "Koneksi Stabil! Membuka Toko...";
            infoElement.innerHTML = "Jaringan terdeteksi 4G (Cepat)";
            delayTime = 1500; // 1.5 Detik delay buatan agar logo terlihat
            
        } else if (connectionType === '3g') {
            // JIKA KONEKSI SEDANG (3G)
            document.documentElement.style.setProperty('--bounce-speed', '1.5s');
            textElement.innerHTML = "Menghubungkan ke Server...";
            infoElement.innerHTML = "Jaringan terdeteksi 3G";
            delayTime = 500; 
            
        } else {
            // JIKA KONEKSI LAMBAT (2G / Slow-2G)
            document.documentElement.style.setProperty('--bounce-speed', '2.5s'); 
            textElement.innerHTML = "Koneksi Anda agak lambat, mohon bersabar...";
            infoElement.innerHTML = "Mengoptimalkan data untuk jaringan lambat...";
            delayTime = 0; // Langsung submit
        }

        // Eksekusi Submit setelah delay yang ditentukan
        setTimeout(function() {
            form.submit();
        }, delayTime);
    });

    // Reset saat user kembali (Back button)
    window.onpageshow = function(event) {
        if (event.persisted) {
            document.getElementById('loading-overlay').style.display = 'none';
        }
    };
</script>

<?php include_once 'footer.php'; ?>
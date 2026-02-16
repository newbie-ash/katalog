<?php
// pesan.php
include_once 'db_koneksi.php';
// Header di-include nanti setelah logic agar redirect aman (jika ada)

// Start session manual jika belum
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['user_id'];

// --- UPDATE STATUS BACA (Agar notifikasi hilang) ---
// Tandai semua pesan dari admin ke user ini sebagai sudah dibaca
$conn->query("UPDATE pesan SET is_read = 1 WHERE id_user = $id_user AND pengirim = 'admin'");

// Kirim Pesan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['isi_pesan'])) {
    $isi = $conn->real_escape_string($_POST['isi_pesan']);
    $stmt = $conn->prepare("INSERT INTO pesan (id_user, isi_pesan, pengirim, is_read) VALUES (?, ?, 'user', 0)");
    $stmt->bind_param("is", $id_user, $isi);
    $stmt->execute();
    header("Location: pesan.php"); 
    exit;
}

// Ambil Percakapan
$sql = "SELECT * FROM pesan WHERE id_user = $id_user ORDER BY tanggal ASC";
$chats = $conn->query($sql);

// BARU INCLUDE HEADER DI SINI
include_once 'header.php';
?>

<div class="container">
    <div style="max-width: 800px; margin: 0 auto;">
        
        <!-- Header Chat User -->
        <div style="background: white; padding: 15px 20px; border-radius: 8px 8px 0 0; border-bottom: 1px solid #eee; display: flex; align-items: center; gap: 10px; margin-top: 20px;">
            <div style="width: 40px; height: 40px; background: var(--primary-color); color: white; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 18px;">
                <i class="fas fa-headset"></i>
            </div>
            <div>
                <h3 style="margin: 0; font-size: 16px; color: #333;">Admin Matria.Mart</h3>
                <span style="font-size: 12px; color: #2ecc71;">Siap Membantu</span>
            </div>
        </div>

        <!-- Area Chat -->
        <div class="chat-box" style="background: #fff; border: 1px solid #eee; border-top: none; padding: 20px; height: 400px; overflow-y: auto; display: flex; flex-direction: column; gap: 15px;">
            <?php if ($chats->num_rows > 0): ?>
                <?php while($chat = $chats->fetch_assoc()): ?>
                    <?php 
                        $is_me = ($chat['pengirim'] == 'user'); 
                        $align = $is_me ? 'flex-end' : 'flex-start';
                        $bg = $is_me ? '#dcf8c6' : '#f0f2f5'; 
                        $border_rad = $is_me ? '15px 15px 0 15px' : '15px 15px 15px 0';
                    ?>
                    <div style="display: flex; justify-content: <?php echo $align; ?>;">
                        <div style="max-width: 75%; background: <?php echo $bg; ?>; padding: 10px 15px; border-radius: <?php echo $border_rad; ?>; box-shadow: 0 1px 2px rgba(0,0,0,0.1); position: relative;">
                            <span style="line-height: 1.5; color: #333; font-size: 14px;"><?php echo nl2br(htmlspecialchars($chat['isi_pesan'])); ?></span>
                            <div style="text-align: right; font-size: 10px; color: #999; margin-top: 4px; display: flex; align-items: center; justify-content: flex-end; gap: 3px;">
                                <?php echo date('H:i', strtotime($chat['tanggal'])); ?>
                                <?php if($is_me): ?>
                                    <i class="fas fa-check" style="color: <?php echo ($chat['is_read'] == 1) ? '#3498db' : '#ccc'; ?>;"></i>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; color: #bbb; margin-top: 100px;">
                    <i class="fas fa-comments fa-4x" style="margin-bottom: 15px; color: #eee;"></i>
                    <p>Belum ada percakapan.<br>Silakan tanya ketersediaan stok atau pengiriman.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Form Kirim -->
        <form method="POST" style="display: flex; gap: 10px; background: white; padding: 15px; border-radius: 0 0 8px 8px; border: 1px solid #eee; border-top: none;">
            <input type="text" name="isi_pesan" required placeholder="Tulis pesan..." style="flex: 1; padding: 12px 20px; border: 1px solid #ddd; border-radius: 25px; outline: none; transition: 0.3s;">
            <button type="submit" class="btn btn-primary" style="width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; padding: 0;">
                <i class="fas fa-paper-plane" style="font-size: 18px;"></i>
            </button>
        </form>
    </div>
</div>

<script>
    // Auto scroll ke bawah saat load
    var chatBox = document.querySelector('.chat-box');
    chatBox.scrollTop = chatBox.scrollHeight;
</script>

<?php include_once 'footer.php'; ?>
<?php
// pesan.php
include_once 'db_koneksi.php';
include_once 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['user_id'];

// Kirim Pesan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['isi_pesan'])) {
    $isi = $conn->real_escape_string($_POST['isi_pesan']);
    $stmt = $conn->prepare("INSERT INTO pesan (id_user, isi_pesan, pengirim, is_read) VALUES (?, ?, 'user', 0)");
    $stmt->bind_param("is", $id_user, $isi);
    $stmt->execute();
    header("Location: pesan.php"); // Refresh agar tidak resubmit
    exit;
}

// Ambil Percakapan
$sql = "SELECT * FROM pesan WHERE id_user = $id_user ORDER BY tanggal ASC";
$chats = $conn->query($sql);
?>

<div class="container">
    <div style="max-width: 800px; margin: 0 auto;">
        <h2 style="border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">
            <i class="fas fa-comments"></i> Chat dengan Admin
        </h2>

        <!-- Area Chat -->
        <div class="chat-box" style="background: #fdfdfd; border: 1px solid #ddd; padding: 20px; height: 400px; overflow-y: auto; border-radius: 4px; margin-bottom: 20px; display: flex; flex-direction: column; gap: 10px;">
            <?php if ($chats->num_rows > 0): ?>
                <?php while($chat = $chats->fetch_assoc()): ?>
                    <?php 
                        $is_me = ($chat['pengirim'] == 'user'); 
                        $align = $is_me ? 'flex-end' : 'flex-start';
                        $bg = $is_me ? '#dcf8c6' : '#fff'; // Hijau muda ala WA untuk user, putih untuk admin
                        $border = $is_me ? 'none' : '1px solid #eee';
                    ?>
                    <div style="display: flex; justify-content: <?php echo $align; ?>;">
                        <div style="max-width: 70%; background: <?php echo $bg; ?>; border: <?php echo $border; ?>; padding: 10px 15px; border-radius: 10px; box-shadow: 0 1px 1px rgba(0,0,0,0.1);">
                            <small style="font-weight: bold; color: var(--primary-color); display: block; margin-bottom: 3px;">
                                <?php echo $is_me ? 'Saya' : 'Admin Matria.Mart'; ?>
                            </small>
                            <span style="line-height: 1.4; color: #333;"><?php echo nl2br(htmlspecialchars($chat['isi_pesan'])); ?></span>
                            <div style="text-align: right; font-size: 10px; color: #999; margin-top: 5px;">
                                <?php echo date('d/m H:i', strtotime($chat['tanggal'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; color: #aaa; margin-top: 150px;">
                    <i class="fas fa-paper-plane fa-3x"></i>
                    <p>Belum ada pesan. Silakan tanya sesuatu kepada Admin.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Form Kirim -->
        <form method="POST" style="display: flex; gap: 10px;">
            <textarea name="isi_pesan" required placeholder="Tulis pesan Anda di sini..." style="flex: 1; padding: 15px; border: 1px solid #ccc; border-radius: 4px; resize: none; height: 50px;"></textarea>
            <button type="submit" class="btn btn-primary" style="padding: 0 25px;">
                <i class="fas fa-paper-plane"></i> Kirim
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
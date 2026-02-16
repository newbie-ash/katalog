<?php
// admin/manage_pesan.php
include '../db_koneksi.php';

// --- BAGIAN 1: LOGIKA PROSES (Sebelum Output HTML) ---

// Cek Login & Session Manual (Karena header.php belum di-include)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Proses Kirim Balasan (Pindahkan ke sini agar redirect berfungsi)
if (isset($_GET['reply_to']) && $_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['isi_pesan'])) {
    $id_user_target = intval($_GET['reply_to']);
    $isi = $conn->real_escape_string($_POST['isi_pesan']);
    
    // Insert Pesan
    $stmt = $conn->prepare("INSERT INTO pesan (id_user, isi_pesan, pengirim, is_read) VALUES (?, ?, 'admin', 0)");
    $stmt->bind_param("is", $id_user_target, $isi);
    $stmt->execute();
    
    // Tandai pesan user sbg read
    $conn->query("UPDATE pesan SET is_read = 1 WHERE id_user = $id_user_target AND pengirim = 'user'");
    
    // Redirect aman karena belum ada output HTML
    header("Location: manage_pesan.php?reply_to=$id_user_target");
    exit;
}

// --- BAGIAN 2: TAMPILAN HALAMAN (Output HTML dimulai di sini) ---
include 'header.php'; 

if (isset($_GET['reply_to'])) {
    // MODE CHAT
    $id_user_target = intval($_GET['reply_to']);
    $user_info = $conn->query("SELECT nama FROM user WHERE id = $id_user_target")->fetch_assoc();
    $chats = $conn->query("SELECT * FROM pesan WHERE id_user = $id_user_target ORDER BY tanggal ASC");
    ?>
    
    <h2 class="page-title">Chat Pelanggan</h2>

    <div style="background: white; border-radius: 5px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); overflow: hidden; display: flex; flex-direction: column; height: 70vh;">
        
        <!-- Chat Header -->
        <div style="padding: 15px 20px; border-bottom: 1px solid #eee; display: flex; align-items: center; justify-content: space-between; background: #f9f9f9;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 40px; height: 40px; background: #3498db; color: white; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 18px;">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <h4 style="margin: 0; font-size: 16px; color: #333;"><?php echo htmlspecialchars($user_info['nama']); ?></h4>
                    <span style="font-size: 12px; color: #2ecc71;">Online</span>
                </div>
            </div>
            <a href="manage_pesan.php" style="color: #666; font-size: 14px;"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>

        <!-- Chat Body -->
        <div class="chat-box" style="flex: 1; padding: 20px; overflow-y: auto; background: #fff;">
            <?php while($chat = $chats->fetch_assoc()): ?>
                <?php 
                    $is_admin = ($chat['pengirim'] == 'admin'); 
                    $align = $is_admin ? 'flex-end' : 'flex-start';
                    $bg = $is_admin ? '#dcf8c6' : '#f1f0f0'; 
                ?>
                <div style="display: flex; justify-content: <?php echo $align; ?>; margin-bottom: 15px;">
                    <div style="max-width: 70%; background: <?php echo $bg; ?>; padding: 12px 15px; border-radius: 15px; border-top-<?php echo $is_admin ? 'right' : 'left'; ?>-radius: 0; position: relative;">
                        <div style="font-size: 14px; color: #333; line-height: 1.5;">
                            <?php echo nl2br(htmlspecialchars($chat['isi_pesan'])); ?>
                        </div>
                        <div style="text-align: right; font-size: 11px; color: #999; margin-top: 5px;">
                            <?php echo date('H:i, d M', strtotime($chat['tanggal'])); ?>
                            <?php if($is_admin): ?>
                                <i class="fas fa-check-double" style="color: #3498db;"></i>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Chat Footer -->
        <div style="padding: 15px; background: #f9f9f9; border-top: 1px solid #eee;">
            <form method="POST" style="display: flex; gap: 10px;">
                <input type="text" name="isi_pesan" required placeholder="Tulis balasan..." style="flex: 1; padding: 12px 15px; border: 1px solid #ddd; border-radius: 25px; outline: none;">
                <button type="submit" style="width: 50px; height: 50px; background: #2196f3; color: white; border: none; border-radius: 50%; cursor: pointer; display: flex; justify-content: center; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>

    </div>
    
    <script>
        var cb = document.querySelector('.chat-box');
        cb.scrollTop = cb.scrollHeight;
    </script>
    <?php

} else {
    // MODE LIST PESAN
    $sql_list = "SELECT p.*, u.nama, u.email 
                 FROM pesan p 
                 JOIN user u ON p.id_user = u.id 
                 WHERE p.id IN (
                    SELECT MAX(id) FROM pesan GROUP BY id_user
                 ) 
                 ORDER BY p.tanggal DESC";
    $list = $conn->query($sql_list);
    ?>

    <h2 class="page-title">Kotak Masuk</h2>

    <div style="background: white; border-radius: 5px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); padding: 0;">
        <?php if ($list->num_rows > 0): ?>
            <table style="width: 100%; border-collapse: collapse;">
                <?php while($row = $list->fetch_assoc()): ?>
                    <?php 
                        $unread = ($row['is_read'] == 0 && $row['pengirim'] == 'user');
                        $bg_row = $unread ? '#fff8e1' : '#fff'; 
                    ?>
                    <tr style="background: <?php echo $bg_row; ?>; border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 15px 20px; width: 60px;">
                            <div style="width: 45px; height: 45px; background: #34495e; color: white; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 18px;">
                                <?php echo strtoupper(substr($row['nama'], 0, 1)); ?>
                            </div>
                        </td>
                        <td style="padding: 15px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                <strong style="font-size: 15px; color: #333;"><?php echo htmlspecialchars($row['nama']); ?></strong>
                                <span style="font-size: 12px; color: #999;"><?php echo date('d M H:i', strtotime($row['tanggal'])); ?></span>
                            </div>
                            <div style="color: #666; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 500px;">
                                <?php if($row['pengirim'] == 'admin'): ?>
                                    <i class="fas fa-reply" style="margin-right: 5px; color: #aaa;"></i>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($row['isi_pesan']); ?>
                            </div>
                        </td>
                        <td style="padding: 15px; text-align: right;">
                            <a href="manage_pesan.php?reply_to=<?php echo $row['id_user']; ?>" style="padding: 8px 15px; background: #fff; border: 1px solid #ddd; border-radius: 20px; color: #555; font-size: 13px; transition: 0.2s;">
                                Buka Chat
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 50px; color: #aaa;">
                <i class="fas fa-inbox fa-3x" style="margin-bottom: 10px;"></i>
                <p>Belum ada pesan masuk.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php
}
include 'footer.php';
?>
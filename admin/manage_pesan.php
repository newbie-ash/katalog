<?php
// admin/manage_pesan.php
include '../db_koneksi.php';
include 'header.php';

// --- MODE BALAS PESAN ---
if (isset($_GET['reply_to'])) {
    $id_user_target = intval($_GET['reply_to']);
    
    // Proses Kirim Balasan
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['isi_pesan'])) {
        $isi = $conn->real_escape_string($_POST['isi_pesan']);
        $stmt = $conn->prepare("INSERT INTO pesan (id_user, isi_pesan, pengirim, is_read) VALUES (?, ?, 'admin', 0)");
        $stmt->bind_param("is", $id_user_target, $isi);
        $stmt->execute();
        
        // Tandai pesan user sebagai sudah dibaca (opsional)
        $conn->query("UPDATE pesan SET is_read = 1 WHERE id_user = $id_user_target AND pengirim = 'user'");
        
        header("Location: manage_pesan.php?reply_to=$id_user_target");
        exit;
    }

    // Ambil Data User
    $user_info = $conn->query("SELECT nama FROM user WHERE id = $id_user_target")->fetch_assoc();

    // Ambil Percakapan
    $chats = $conn->query("SELECT * FROM pesan WHERE id_user = $id_user_target ORDER BY tanggal ASC");
    ?>
    
    <div class="container">
        <a href="manage_pesan.php" class="btn btn-secondary" style="margin-bottom: 10px;">&laquo; Kembali ke Daftar Pesan</a>
        
        <div style="max-width: 800px; margin: 0 auto;">
            <div style="background: white; padding: 15px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin:0;">Chat dengan: <?php echo htmlspecialchars($user_info['nama']); ?></h3>
            </div>

            <div class="chat-box" style="background: #fdfdfd; border: 1px solid #ddd; padding: 20px; height: 400px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px;">
                <?php while($chat = $chats->fetch_assoc()): ?>
                    <?php 
                        $is_admin = ($chat['pengirim'] == 'admin'); 
                        $align = $is_admin ? 'flex-end' : 'flex-start';
                        $bg = $is_admin ? '#dcf8c6' : '#fff'; 
                        $border = $is_admin ? 'none' : '1px solid #eee';
                    ?>
                    <div style="display: flex; justify-content: <?php echo $align; ?>;">
                        <div style="max-width: 70%; background: <?php echo $bg; ?>; border: <?php echo $border; ?>; padding: 10px 15px; border-radius: 10px; box-shadow: 0 1px 1px rgba(0,0,0,0.1);">
                            <small style="font-weight: bold; color: var(--primary-color); display: block; margin-bottom: 3px;">
                                <?php echo $is_admin ? 'Saya (Admin)' : htmlspecialchars($user_info['nama']); ?>
                            </small>
                            <span><?php echo nl2br(htmlspecialchars($chat['isi_pesan'])); ?></span>
                            <div style="text-align: right; font-size: 10px; color: #999; margin-top: 5px;">
                                <?php echo date('d/m H:i', strtotime($chat['tanggal'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <form method="POST" style="display: flex; gap: 10px; margin-top: 15px;">
                <textarea name="isi_pesan" required placeholder="Balas pesan..." style="flex: 1; padding: 15px; border: 1px solid #ccc; border-radius: 4px; resize: none; height: 50px;"></textarea>
                <button type="submit" class="btn btn-primary" style="padding: 0 25px;">Kirim</button>
            </form>
        </div>
    </div>
    <script>
        var chatBox = document.querySelector('.chat-box');
        chatBox.scrollTop = chatBox.scrollHeight;
    </script>
    <?php

} else {
    // --- MODE DAFTAR PESAN ---
    // Query rumit ini untuk mengambil pesan terakhir dari setiap user
    $sql_list = "SELECT p.*, u.nama, u.email 
                 FROM pesan p 
                 JOIN user u ON p.id_user = u.id 
                 WHERE p.id IN (
                    SELECT MAX(id) FROM pesan GROUP BY id_user
                 ) 
                 ORDER BY p.tanggal DESC";
    $list = $conn->query($sql_list);
    ?>

    <div class="container">
        <h3>Kotak Masuk Pesan</h3>
        <div class="card-container">
            <?php if ($list->num_rows > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Pesan Terakhir</th>
                            <th>Waktu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $list->fetch_assoc()): ?>
                            <tr style="<?php echo ($row['is_read'] == 0 && $row['pengirim'] == 'user') ? 'background-color: #fff8e1;' : ''; ?>">
                                <td>
                                    <strong><?php echo htmlspecialchars($row['nama']); ?></strong><br>
                                    <small><?php echo $row['email']; ?></small>
                                </td>
                                <td>
                                    <?php if($row['pengirim'] == 'admin'): ?>
                                        <i class="fas fa-reply" style="color:#aaa;"></i> 
                                    <?php endif; ?>
                                    <?php echo substr(htmlspecialchars($row['isi_pesan']), 0, 50) . '...'; ?>
                                </td>
                                <td><?php echo date('d M H:i', strtotime($row['tanggal'])); ?></td>
                                <td>
                                    <a href="manage_pesan.php?reply_to=<?php echo $row['id_user']; ?>" class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;">
                                        <i class="fas fa-comments"></i> Buka Chat
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; padding: 20px;">Belum ada pesan masuk.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php
}
include 'footer.php';
?>
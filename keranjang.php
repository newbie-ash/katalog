<?php
// keranjang.php
include_once 'db_koneksi.php';
include_once 'header.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    echo "<div class='auth-wrapper'>
            <div class='auth-box'>
                <i class='fas fa-lock fa-3x' style='color:var(--primary-color); margin-bottom:20px;'></i>
                <h3>Silakan Login</h3>
                <p>Anda harus login untuk mengakses keranjang belanja.</p>
                <a href='login.php' class='btn btn-primary btn-block'>Login Sekarang</a>
            </div>
          </div>";
    include_once 'footer.php';
    exit;
}

$id_user = $_SESSION['user_id'];

// --- LOGIKA TAMBAH BARANG (dari halaman produk) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $id_prod = intval($_POST['id_produk']);
    $qty_in = intval($_POST['qty']);

    // Cek apakah produk sudah ada di keranjang
    $check = $conn->query("SELECT id, qty FROM keranjang WHERE id_user=$id_user AND id_produk=$id_prod");
    
    if ($check->num_rows > 0) {
        // Update Qty
        $curr = $check->fetch_assoc();
        $new_qty = $curr['qty'] + $qty_in;
        $conn->query("UPDATE keranjang SET qty = $new_qty WHERE id = " . $curr['id']);
    } else {
        // Insert Baru
        $conn->query("INSERT INTO keranjang (id_user, id_produk, qty) VALUES ($id_user, $id_prod, $qty_in)");
    }
    echo "<script>window.location='keranjang.php';</script>";
    exit;
}

// --- LOGIKA CHECKOUT (SAMA SEPERTI SEBELUMNYA) ---
if (isset($_POST['checkout'])) {
    // 1. Cek User Alamat
    $stmt_user = $conn->prepare("SELECT nama, alamat, kota FROM user WHERE id = ?");
    $stmt_user->bind_param("i", $id_user);
    $stmt_user->execute();
    $user = $stmt_user->get_result()->fetch_assoc();
    $stmt_user->close();

    if (empty($user['alamat']) || empty($user['kota'])) {
        echo "<script>alert('Lengkapi alamat pengiriman di Profil sebelum Checkout!'); window.location='profil.php';</script>";
        exit;
    }

    $alamat_lengkap = $user['alamat'] . ", " . $user['kota'];

    // 2. Ambil Keranjang
    $sql_cart = "SELECT k.*, p.harga_ecer, p.harga_grosir, p.min_belanja_grosir FROM keranjang k JOIN produk p ON k.id_produk = p.id WHERE k.id_user = $id_user";
    $cart = $conn->query($sql_cart);

    if ($cart->num_rows > 0) {
        $conn->begin_transaction();
        try {
            $total_bayar = 0; $total_barang = 0; $items = [];

            while($row = $cart->fetch_assoc()) {
                $harga = ($row['qty'] >= $row['min_belanja_grosir']) ? $row['harga_grosir'] : $row['harga_ecer'];
                $subtotal = $harga * $row['qty'];
                $total_bayar += $subtotal;
                $total_barang += $row['qty'];
                $row['harga_fix'] = $harga;
                $row['subtotal_fix'] = $subtotal;
                $items[] = $row;
            }

            // Insert Pesanan
            $stmt_order = $conn->prepare("INSERT INTO pesanan (id_user, tanggal, alamat_kirim, total_barang, total_bayar, status, metode_bayar) VALUES (?, NOW(), ?, ?, ?, 'Pending', 'Transfer')");
            $stmt_order->bind_param("isii", $id_user, $alamat_lengkap, $total_barang, $total_bayar);
            $stmt_order->execute();
            $id_pesanan_baru = $conn->insert_id;
            $stmt_order->close();

            // Insert Detail
            $stmt_detail = $conn->prepare("INSERT INTO detail_pesanan (id_pesanan, id_produk, qty, harga_deal, subtotal) VALUES (?, ?, ?, ?, ?)");
            foreach ($items as $item) {
                $stmt_detail->bind_param("iiiii", $id_pesanan_baru, $item['id_produk'], $item['qty'], $item['harga_fix'], $item['subtotal_fix']);
                $stmt_detail->execute();
            }
            $stmt_detail->close();

            // Hapus Keranjang
            $conn->query("DELETE FROM keranjang WHERE id_user = $id_user");
            $conn->commit();
            echo "<script>window.location='pembayaran.php?id=$id_pesanan_baru';</script>";
        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>alert('Gagal: " . $e->getMessage() . "');</script>";
        }
    }
}

// --- HAPUS ITEM ---
if (isset($_GET['hapus'])) {
    $id_hapus = intval($_GET['hapus']);
    $conn->query("DELETE FROM keranjang WHERE id = $id_hapus AND id_user = $id_user");
    echo "<script>window.location='keranjang.php';</script>";
}

$result = $conn->query("SELECT k.id as id_keranjang, k.qty, p.nama_barang, p.harga_ecer, p.harga_grosir, p.min_belanja_grosir, p.gambar, p.satuan FROM keranjang k JOIN produk p ON k.id_produk = p.id WHERE k.id_user = $id_user");
?>

<div class="container">
    <h2 style="color:white; text-shadow: 1px 1px 2px black;">Keranjang Belanja</h2>
    
    <div style="display: flex; flex-direction: column; gap: 15px;">
    <?php if ($result->num_rows > 0): 
        $total_all = 0;
        while($row = $result->fetch_assoc()): 
            $harga = ($row['qty'] >= $row['min_belanja_grosir']) ? $row['harga_grosir'] : $row['harga_ecer'];
            $is_grosir = ($row['qty'] >= $row['min_belanja_grosir']);
            $subtotal = $harga * $row['qty'];
            $total_all += $subtotal;
            $gambar = !empty($row['gambar']) ? 'images/'.$row['gambar'] : 'https://via.placeholder.com/100';
    ?>
        <div class="card-container" style="display: flex; gap: 15px; align-items: center; padding: 15px;">
            <img src="<?php echo $gambar; ?>" style="width: 80px; height: 80px; object-fit: cover; border: 1px solid #eee;">
            
            <div style="flex: 1;">
                <h4 style="margin: 0 0 5px;"><?php echo htmlspecialchars($row['nama_barang']); ?></h4>
                
                <div style="font-size: 14px; color: #666;">
                    <?php if($is_grosir): ?>
                        <span style="color: green; font-weight: bold; font-size: 12px; border: 1px solid green; padding: 0 4px; border-radius: 2px;">Grosir</span>
                    <?php else: ?>
                        Harga Ecer
                    <?php endif; ?>
                    @ Rp <?php echo number_format($harga,0,',','.'); ?>
                </div>

                <div style="margin-top: 5px; font-weight: bold; color: var(--primary-color);">
                    Subtotal: Rp <?php echo number_format($subtotal,0,',','.'); ?>
                </div>
            </div>

            <div style="text-align: right;">
                <div style="margin-bottom: 5px;">Qty: <strong><?php echo $row['qty']; ?> <?php echo $row['satuan']; ?></strong></div>
                <a href="keranjang.php?hapus=<?php echo $row['id_keranjang']; ?>" onclick="return confirm('Hapus?')" style="color: #999; font-size: 12px;">
                    <i class="fas fa-trash"></i> Hapus
                </a>
            </div>
        </div>
    <?php endwhile; ?>

        <!-- Checkout Bar -->
        <div style="position: sticky; bottom: 0; background: white; padding: 20px; box-shadow: 0 -2px 10px rgba(0,0,0,0.1); border-top: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; z-index: 100;">
            <div>
                <span style="font-size: 14px; color: #666;">Total Pembayaran:</span><br>
                <span style="font-size: 24px; font-weight: bold; color: var(--primary-color);">Rp <?php echo number_format($total_all, 0, ',', '.'); ?></span>
            </div>
            
            <form method="POST">
                <button type="submit" name="checkout" class="btn btn-primary" style="padding: 12px 40px; font-size: 16px;">
                    Checkout
                </button>
            </form>
        </div>

    <?php else: ?>
        <div class="card-container" style="text-align: center; padding: 50px;">
            <i class="fas fa-shopping-basket fa-4x" style="color: #ddd; margin-bottom: 20px;"></i>
            <p>Keranjang belanja Anda kosong.</p>
            <a href="index.php" class="btn btn-primary">Belanja Sekarang</a>
        </div>
    <?php endif; ?>
    </div>
</div>

<?php include_once 'footer.php'; ?>
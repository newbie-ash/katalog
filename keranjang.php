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

// --- LOGIKA UPDATE QTY (PLUS / MINUS) ---
if (isset($_GET['act']) && isset($_GET['id'])) {
    $id_k = intval($_GET['id']);
    $act = $_GET['act'];

    // Ambil data keranjang & stok produk
    $cek = $conn->query("SELECT k.qty, p.stok FROM keranjang k JOIN produk p ON k.id_produk = p.id WHERE k.id = $id_k AND k.id_user = $id_user");
    
    if ($cek->num_rows > 0) {
        $row = $cek->fetch_assoc();
        $qty_now = $row['qty'];
        $stok_max = $row['stok'];

        if ($act == 'plus') {
            if ($qty_now < $stok_max) {
                $conn->query("UPDATE keranjang SET qty = qty + 1 WHERE id = $id_k");
            }
        } elseif ($act == 'min') {
            if ($qty_now > 1) {
                $conn->query("UPDATE keranjang SET qty = qty - 1 WHERE id = $id_k");
            }
        }
    }
    echo "<script>window.location='keranjang.php';</script>";
    exit;
}

// --- LOGIKA TAMBAH BARANG ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $id_prod = intval($_POST['id_produk']);
    $qty_in = intval($_POST['qty']);

    $check = $conn->query("SELECT id, qty FROM keranjang WHERE id_user=$id_user AND id_produk=$id_prod");
    
    if ($check->num_rows > 0) {
        $curr = $check->fetch_assoc();
        $new_qty = $curr['qty'] + $qty_in;
        $conn->query("UPDATE keranjang SET qty = $new_qty WHERE id = " . $curr['id']);
    } else {
        $conn->query("INSERT INTO keranjang (id_user, id_produk, qty) VALUES ($id_user, $id_prod, $qty_in)");
    }
    echo "<script>window.location='keranjang.php';</script>";
    exit;
}

// --- LOGIKA CHECKOUT ---
if (isset($_POST['checkout'])) {
    // 1. Cek Profil User
    $stmt_user = $conn->prepare("SELECT nama, alamat, kota FROM user WHERE id = ?");
    $stmt_user->bind_param("i", $id_user);
    $stmt_user->execute();
    $user = $stmt_user->get_result()->fetch_assoc();
    $stmt_user->close();

    // Validasi Profil Lengkap
    if (empty($user['alamat']) || empty($user['kota'])) {
        echo "<script>alert('Mohon lengkapi KOTA dan ALAMAT di menu Profil agar ongkir dapat dihitung!'); window.location='profil.php';</script>";
        exit;
    }

    $alamat_lengkap = $user['alamat'] . ", " . $user['kota'];
    $kota_user = $user['kota'];

    // 2. Hitung Ongkir Otomatis (Cari kota yang cocok)
    $ongkir = 0;
    // Query mencari kota yang mirip (Contoh: "Surabaya Selatan" akan cocok dengan "Surabaya")
    $q_ongkir = $conn->query("SELECT tarif FROM ongkir WHERE '$kota_user' LIKE CONCAT('%', nama_kota, '%') OR nama_kota LIKE '%$kota_user%' ORDER BY tarif DESC LIMIT 1");
    
    if ($q_ongkir && $q_ongkir->num_rows > 0) {
        $ongkir = $q_ongkir->fetch_assoc()['tarif'];
    }

    // 3. Proses Checkout
    $sql_cart = "SELECT k.*, p.harga_ecer, p.harga_grosir, p.min_belanja_grosir FROM keranjang k JOIN produk p ON k.id_produk = p.id WHERE k.id_user = $id_user";
    $cart = $conn->query($sql_cart);

    if ($cart->num_rows > 0) {
        $conn->begin_transaction();
        try {
            $total_belanja = 0; 
            $total_barang = 0; 
            $items = [];

            while($row = $cart->fetch_assoc()) {
                $harga = ($row['qty'] >= $row['min_belanja_grosir']) ? $row['harga_grosir'] : $row['harga_ecer'];
                $subtotal = $harga * $row['qty'];
                $total_belanja += $subtotal;
                $total_barang += $row['qty'];
                $row['harga_fix'] = $harga;
                $row['subtotal_fix'] = $subtotal;
                $items[] = $row;
            }

            // TOTAL BAYAR = Belanja + Ongkir
            $total_bayar = $total_belanja + $ongkir;

            // Simpan ke Pesanan (Sertakan Ongkir)
            $stmt_order = $conn->prepare("INSERT INTO pesanan (id_user, tanggal, alamat_kirim, total_barang, ongkir, total_bayar, status, metode_bayar) VALUES (?, NOW(), ?, ?, ?, ?, 'Pending', 'Transfer')");
            $stmt_order->bind_param("isiii", $id_user, $alamat_lengkap, $total_barang, $ongkir, $total_bayar);
            $stmt_order->execute();
            $id_pesanan_baru = $conn->insert_id;
            $stmt_order->close();

            // Simpan Detail Barang
            $stmt_detail = $conn->prepare("INSERT INTO detail_pesanan (id_pesanan, id_produk, qty, harga_deal, subtotal) VALUES (?, ?, ?, ?, ?)");
            foreach ($items as $item) {
                $stmt_detail->bind_param("iiiii", $id_pesanan_baru, $item['id_produk'], $item['qty'], $item['harga_fix'], $item['subtotal_fix']);
                $stmt_detail->execute();
            }
            $stmt_detail->close();

            // Kosongkan Keranjang
            $conn->query("DELETE FROM keranjang WHERE id_user = $id_user");
            $conn->commit();
            echo "<script>window.location='pembayaran.php?id=$id_pesanan_baru';</script>";
        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>alert('Gagal Checkout: " . $e->getMessage() . "');</script>";
        }
    }
}

// --- HAPUS ITEM ---
if (isset($_GET['hapus'])) {
    $id_hapus = intval($_GET['hapus']);
    $conn->query("DELETE FROM keranjang WHERE id = $id_hapus AND id_user = $id_user");
    echo "<script>window.location='keranjang.php';</script>";
}

// --- DATA KERANJANG & ESTIMASI ONGKIR ---
$result = $conn->query("SELECT k.id as id_keranjang, k.qty, p.nama_barang, p.harga_ecer, p.harga_grosir, p.min_belanja_grosir, p.gambar, p.satuan FROM keranjang k JOIN produk p ON k.id_produk = p.id WHERE k.id_user = $id_user");

// Hitung Estimasi Ongkir untuk Tampilan
$estimasi_ongkir = 0;
$kota_user_display = "-";
$q_user = $conn->query("SELECT kota FROM user WHERE id=$id_user");
if($q_user->num_rows > 0) {
    $u = $q_user->fetch_assoc();
    if(!empty($u['kota'])) {
        $kota_user_display = $u['kota'];
        $q_tarif = $conn->query("SELECT tarif FROM ongkir WHERE '$kota_user_display' LIKE CONCAT('%', nama_kota, '%') OR nama_kota LIKE '%$kota_user_display%' LIMIT 1");
        if($q_tarif && $q_tarif->num_rows > 0) {
            $estimasi_ongkir = $q_tarif->fetch_assoc()['tarif'];
        }
    } else {
        $kota_user_display = "Belum diset";
    }
}
?>

<div class="container">
    <h2 style="color:white; text-shadow: 1px 1px 2px black;">Keranjang Belanja</h2>
    
    <div style="display: flex; flex-direction: column; gap: 15px;">
    <?php if ($result->num_rows > 0): 
        $total_belanja = 0;
        while($row = $result->fetch_assoc()): 
            $harga = ($row['qty'] >= $row['min_belanja_grosir']) ? $row['harga_grosir'] : $row['harga_ecer'];
            $is_grosir = ($row['qty'] >= $row['min_belanja_grosir']);
            $subtotal = $harga * $row['qty'];
            $total_belanja += $subtotal;
            $gambar = !empty($row['gambar']) ? 'images/'.$row['gambar'] : 'https://via.placeholder.com/100';
    ?>
        <div class="card-container" style="display: flex; gap: 15px; align-items: center; padding: 15px;">
            <img src="<?php echo $gambar; ?>" style="width: 80px; height: 80px; object-fit: cover; border: 1px solid #eee; border-radius: 4px;">
            <div style="flex: 1;">
                <h4 style="margin: 0 0 5px;"><?php echo htmlspecialchars($row['nama_barang']); ?></h4>
                <div style="font-size: 14px; color: #666;">
                    <?php if($is_grosir): ?>
                        <span style="color: green; font-weight: bold; border: 1px solid green; padding: 0 4px; border-radius: 2px;">Grosir</span>
                    <?php else: ?>
                        Ecer
                    <?php endif; ?>
                    @ Rp <?php echo number_format($harga,0,',','.'); ?>
                </div>
                <div style="margin-top: 5px; font-weight: bold; color: var(--primary-color);">
                    Subtotal: Rp <?php echo number_format($subtotal,0,',','.'); ?>
                </div>
            </div>
            <!-- QTY Buttons -->
            <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                <div style="display: flex; align-items: center; border: 1px solid #ccc; border-radius: 4px; overflow: hidden; background: white;">
                    <a href="keranjang.php?act=min&id=<?php echo $row['id_keranjang']; ?>" style="width: 30px; height: 30px; background: #f0f0f0; display: flex; align-items: center; justify-content: center;"><i class="fas fa-minus"></i></a>
                    <div style="padding: 0 15px; font-weight: bold;"><?php echo $row['qty']; ?></div>
                    <a href="keranjang.php?act=plus&id=<?php echo $row['id_keranjang']; ?>" style="width: 30px; height: 30px; background: #f0f0f0; display: flex; align-items: center; justify-content: center;"><i class="fas fa-plus"></i></a>
                </div>
                <a href="keranjang.php?hapus=<?php echo $row['id_keranjang']; ?>" onclick="return confirm('Hapus?')" style="color: #999; font-size: 12px;"><i class="fas fa-trash"></i> Hapus</a>
            </div>
        </div>
    <?php endwhile; ?>

        <!-- CHECKOUT & ONGKIR INFO -->
        <div class="card-container" style="padding: 20px;">
            <div style="margin-bottom: 20px; font-size: 14px; color: #555;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span>Total Barang:</span>
                    <span>Rp <?php echo number_format($total_belanja, 0, ',', '.'); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span>Ongkos Kirim (Kota: <?php echo htmlspecialchars($kota_user_display); ?>):</span>
                    <span style="font-weight: bold;">Rp <?php echo number_format($estimasi_ongkir, 0, ',', '.'); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 18px; color: #333; border-top: 1px dashed #ccc; padding-top: 10px;">
                    <span>Total Tagihan:</span>
                    <span style="color: var(--primary-color);">Rp <?php echo number_format($total_belanja + $estimasi_ongkir, 0, ',', '.'); ?></span>
                </div>
            </div>

            <form method="POST" style="text-align: right;">
                <?php if($kota_user_display == "Belum diset"): ?>
                    <div style="color: #e74c3c; margin-bottom: 10px;">Anda belum mengatur Kota di Profil. Ongkir tidak dapat dihitung.</div>
                    <a href="profil.php" class="btn btn-warning">Atur Alamat & Kota</a>
                <?php else: ?>
                    <button type="submit" name="checkout" class="btn btn-primary" style="padding: 12px 40px; font-size: 16px;">
                        Checkout <i class="fas fa-chevron-right"></i>
                    </button>
                <?php endif; ?>
            </form>
        </div>

    <?php else: ?>
        <div class="card-container" style="text-align: center; padding: 50px;">
            <i class="fas fa-shopping-basket fa-4x" style="color: #ddd; margin-bottom: 20px;"></i>
            <p>Keranjang kosong.</p>
            <a href="index.php" class="btn btn-primary">Belanja Sekarang</a>
        </div>
    <?php endif; ?>
    </div>
</div>

<?php include_once 'footer.php'; ?>
<?php
// admin/dashboard.php
include '../db_koneksi.php';
include 'header.php'; // Header baru

// 1. QUERY DATA KARTU STATISTIK (WIDGET ATAS)
$total_produk = $conn->query("SELECT COUNT(*) as total FROM produk")->fetch_assoc()['total'];
$total_user = $conn->query("SELECT COUNT(*) as total FROM user WHERE role='pembeli'")->fetch_assoc()['total'];
$pesanan_pending = $conn->query("SELECT COUNT(*) as total FROM pesanan WHERE status='Pending'")->fetch_assoc()['total'];
$total_pesanan = $conn->query("SELECT COUNT(*) as total FROM pesanan")->fetch_assoc()['total'];

// 2. QUERY DATA UNTUK GRAFIK (Statistik Status Pesanan)
$statuses = [];
$totals = [];
$query_chart = $conn->query("SELECT status, COUNT(*) as jumlah FROM pesanan GROUP BY status");

while($row = $query_chart->fetch_assoc()) {
    $statuses[] = $row['status'];
    $totals[] = $row['jumlah'];
}

$json_statuses = json_encode($statuses);
$json_totals = json_encode($totals);

// 3. QUERY STOK MENIPIS (Fitur Baru)
// Ambil produk yang stoknya <= 10
$limit_stok = 10;
$query_stok = $conn->query("SELECT nama_barang, stok, satuan, gambar FROM produk WHERE stok <= $limit_stok ORDER BY stok ASC LIMIT 5");
?>

<h2 class="page-title">Dashboard</h2>

<style>
    /* CSS Grid untuk Kartu Statistik */
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }
    
    /* Style Kartu Statistik */
    .stat-card {
        background: #fff;
        border-radius: 8px;
        padding: 30px 20px;
        text-align: center;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        transition: transform 0.3s ease;
        text-decoration: none;
        display: block;
        border-bottom: 4px solid transparent;
    }
    
    .stat-card:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .stat-icon { font-size: 45px; margin-bottom: 15px; }
    .stat-number { font-size: 32px; font-weight: 700; color: #333; margin-bottom: 5px; }
    .stat-label { font-size: 14px; color: #888; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; }
    
    /* Layout Dua Kolom: Grafik & Stok Alert */
    .dashboard-content-row {
        display: flex;
        gap: 30px;
        flex-wrap: wrap;
    }
    
    .chart-container, .alert-container {
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        flex: 1;
        min-width: 300px;
    }

    /* Style Tabel Stok */
    .stok-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    .stok-table th { text-align: left; padding: 10px; border-bottom: 2px solid #eee; font-size: 13px; color: #666; }
    .stok-table td { padding: 10px; border-bottom: 1px solid #f5f5f5; vertical-align: middle; }
    .stok-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: bold;
        color: white;
    }
    .bg-danger { background: #e74c3c; } /* Merah jika stok < 5 */
    .bg-warning { background: #f39c12; } /* Kuning jika stok < 10 */

</style>

<!-- Load Library Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="dashboard-grid">
    <!-- Kartu User -->
    <div class="stat-card" style="border-color: #ff9800;">
        <div class="stat-icon" style="color: #ff9800;">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-number"><?php echo $total_user; ?></div>
        <div class="stat-label">Pelanggan</div>
    </div>
    <!-- Kartu Produk -->
    <a href="manage_produk.php" class="stat-card" style="border-color: #2196f3;">
        <div class="stat-icon" style="color: #2196f3;">
            <i class="fas fa-boxes"></i>
        </div>
        <div class="stat-number"><?php echo $total_produk; ?></div>
        <div class="stat-label">Total Produk</div>
    </a>
    <!-- Kartu Pesanan Pending -->
    <a href="manage_pesanan.php" class="stat-card" style="border-color: #e91e63;">
        <div class="stat-icon" style="color: #e91e63;">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="stat-number"><?php echo $pesanan_pending; ?></div>
        <div class="stat-label">Pesanan Baru</div>
    </a>
    <!-- Kartu Transaksi -->
    <div class="stat-card" style="border-color: #4caf50;">
        <div class="stat-icon" style="color: #4caf50;">
            <i class="fas fa-shopping-bag"></i>
        </div>
        <div class="stat-number"><?php echo $total_pesanan; ?></div>
        <div class="stat-label">Total Transaksi</div>
    </div>
</div>

<div class="dashboard-content-row">
    
    <!-- AREA GRAFIK -->
    <div class="chart-container" style="flex: 2;">
        <h3 style="font-weight: 300; margin-bottom: 20px; color: #333; border-bottom: 1px solid #eee; padding-bottom: 15px;">
            <i class="fas fa-chart-bar"></i> Grafik Status Pesanan
        </h3>
        <div style="position: relative; height: 300px; width: 100%;">
            <canvas id="statusChart"></canvas>
        </div>
    </div>

    <!-- AREA NOTIFIKASI STOK MENIPIS (BARU) -->
    <div class="alert-container" style="flex: 1;">
        <h3 style="font-weight: 300; margin-bottom: 20px; color: #333; border-bottom: 1px solid #eee; padding-bottom: 15px; display: flex; align-items: center; justify-content: space-between;">
            <span><i class="fas fa-exclamation-triangle" style="color: #e74c3c;"></i> Stok Menipis</span>
            <small style="font-size: 12px; color: #999;">(<= 10 Item)</small>
        </h3>
        
        <?php if ($query_stok->num_rows > 0): ?>
            <table class="stok-table">
                <thead>
                    <tr>
                        <th width="50">Gbr</th>
                        <th>Produk</th>
                        <th style="text-align: right;">Sisa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($item = $query_stok->fetch_assoc()): 
                        $gbr = !empty($item['gambar']) ? '../images/'.$item['gambar'] : 'https://via.placeholder.com/40';
                        $class_bg = ($item['stok'] <= 5) ? 'bg-danger' : 'bg-warning';
                    ?>
                    <tr>
                        <td>
                            <img src="<?php echo $gbr; ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid #eee;">
                        </td>
                        <td>
                            <div style="font-weight: 500; font-size: 14px; color: #333;"><?php echo htmlspecialchars($item['nama_barang']); ?></div>
                            <div style="font-size: 11px; color: #888;">Satuan: <?php echo $item['satuan']; ?></div>
                        </td>
                        <td style="text-align: right;">
                            <span class="stok-badge <?php echo $class_bg; ?>">
                                <?php echo $item['stok']; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div style="margin-top: 15px; text-align: center;">
                <a href="manage_produk.php" style="font-size: 13px; color: #2196f3; text-decoration: none;">Lihat Semua Produk &rarr;</a>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 40px 0; color: #aaa;">
                <i class="fas fa-check-circle fa-3x" style="color: #2ecc71; margin-bottom: 10px;"></i>
                <p>Aman! Tidak ada stok yang menipis.</p>
            </div>
        <?php endif; ?>

    </div>

</div>

<!-- SCRIPT INISIALISASI CHART -->
<script>
    const labelStatus = <?php echo $json_statuses; ?>;
    const dataJumlah = <?php echo $json_totals; ?>;

    const ctx = document.getElementById('statusChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labelStatus,
            datasets: [{
                label: 'Jumlah Pesanan',
                data: dataJumlah,
                backgroundColor: [
                    'rgba(255, 159, 64, 0.7)', 
                    'rgba(54, 162, 235, 0.7)', 
                    'rgba(75, 192, 192, 0.7)', 
                    'rgba(255, 99, 132, 0.7)', 
                    'rgba(153, 102, 255, 0.7)', 
                    'rgba(255, 205, 86, 0.7)'
                ],
                borderColor: [
                    'rgba(255, 159, 64, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 205, 86, 1)'
                ],
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, font: { family: "'Roboto', sans-serif" } },
                    grid: { color: "#f0f0f0" }
                },
                x: {
                    ticks: { font: { family: "'Roboto', sans-serif" } },
                    grid: { display: false }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: 'rgba(21, 40, 60, 0.9)', padding: 10 }
            }
        }
    });
</script>

<?php 
include 'footer.php'; 
?>
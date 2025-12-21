<?php
// db_koneksi.php
// Konfigurasi Database
$host = 'localhost';
$username = 'root';     
$password = '';         
$database = 'katalog2'; 

// Membuat koneksi dengan Error Handling yang lebih baik
try {
    $conn = new mysqli($host, $username, $password, $database);
    
    // Cek jika ada error koneksi level driver
    if ($conn->connect_error) {
        throw new Exception("Koneksi gagal: " . $conn->connect_error);
    }
    
    // Set charset ke utf8mb4 agar support emoji dsb
    $conn->set_charset("utf8mb4");

} catch (Exception $e) {
    // Tampilan Error yang ramah pengguna
    die("
    <div style='font-family: sans-serif; text-align: center; padding: 50px;'>
        <h2 style='color: #e74c3c;'>Gagal Terhubung ke Database</h2>
        <p>Pastikan XAMPP/MySQL sudah berjalan dan database <b>$database</b> sudah dibuat.</p>
        <code style='background: #eee; padding: 10px; display: block; margin: 20px auto; max-width: 600px;'>Error: " . $e->getMessage() . "</code>
    </div>
    ");
}
?>
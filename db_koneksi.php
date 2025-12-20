<?php
// db_koneksi.php
$host = 'localhost';
$username = 'root';     // Default User Laragon
$password = '';         // Default Password Laragon (Kosong)
$database = 'katalog2'; // <--- PERHATIKAN: Harus 'katalog2', BUKAN 'katalog'

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
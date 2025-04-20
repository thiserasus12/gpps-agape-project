<?php
$host = 'localhost'; // Sesuaikan dengan host Anda
$dbname = 'gpps_agape_management'; // Nama database sesuai file SQL yang diimpor
$username = 'root'; // Username phpMyAdmin
$password = ''; // Password phpMyAdmin




try {
    $conn = new PDO("mysql:host=localhost;dbname=gpps_agape_management", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>


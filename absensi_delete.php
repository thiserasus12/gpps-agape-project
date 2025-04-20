<?php
// Koneksi ke database menggunakan PDO
include '../includes/db.php';

// Cek apakah ada ID yang diterima
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query untuk menghapus data absensi berdasarkan ID
    $query = "DELETE FROM absensi WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Eksekusi query
    if ($stmt->execute()) {
        echo "Data absensi berhasil dihapus.";
    } else {
        echo "Error: Gagal menghapus data.";
    }
} else {
    echo "ID absensi tidak ditemukan.";
}
?>

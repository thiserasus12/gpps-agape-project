<?php
// Koneksi ke database menggunakan PDO
include '../includes/db.php';
include 'header.php';

// Query untuk mengambil data absensi
$query = "SELECT * FROM absensi ORDER BY tanggal_absensi DESC";

// Siapkan statement
$stmt = $conn->prepare($query);

// Eksekusi query
$stmt->execute();

// Ambil data
$absensi = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tampilkan data absensi
if ($stmt->rowCount() > 0) {
    foreach ($absensi as $data) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($data['nomor_anggota']) . "</td>";
        echo "<td>" . htmlspecialchars($data['status_kehadiran']) . "</td>";
        echo "<td>" . htmlspecialchars($data['tanggal_absensi']) . "</td>";
        echo "<td><a href='update.php?id=" . $data['id'] . "'>Edit</a> | <a href='delete.php?id=" . $data['id'] . "'>Hapus</a></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>Tidak ada data absensi.</td></tr>";
}
?>


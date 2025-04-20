<?php
// Koneksi ke database menggunakan PDO
include '../includes/db.php';
include 'header.php';

// Cek apakah ada ID yang diterima
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data absensi berdasarkan ID
    $query = "SELECT * FROM absensi WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Jika data ditemukan
    if ($stmt->rowCount() > 0) {
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "Data tidak ditemukan.";
        exit;
    }
}

// Proses pembaruan data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomor_anggota = $_POST['nomor_anggota'];
    $status_kehadiran = $_POST['status_kehadiran'];
    $tanggal_absensi = $_POST['tanggal_absensi'];

    // Validasi data
    if (!empty($nomor_anggota) && !empty($status_kehadiran) && !empty($tanggal_absensi)) {
        // Query untuk memperbarui data absensi
        $query = "UPDATE absensi SET nomor_anggota = :nomor_anggota, status_kehadiran = :status_kehadiran, tanggal_absensi = :tanggal_absensi WHERE id = :id";

        // Siapkan statement
        $stmt = $conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':nomor_anggota', $nomor_anggota, PDO::PARAM_STR);
        $stmt->bindParam(':status_kehadiran', $status_kehadiran, PDO::PARAM_STR);
        $stmt->bindParam(':tanggal_absensi', $tanggal_absensi, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Eksekusi query
        if ($stmt->execute()) {
            echo "Data absensi berhasil diperbarui.";
        } else {
            echo "Error: Gagal memperbarui data.";
        }
    } else {
        echo "Semua field harus diisi!";
    }
}
?>

<!-- Form untuk mengedit absensi -->
<form action="update.php?id=<?php echo $id; ?>" method="POST">
    <label for="nomor_anggota">Nomor Anggota</label>
    <input type="text" name="nomor_anggota" value="<?php echo $data['nomor_anggota']; ?>" required>

    <label for="status_kehadiran">Status Kehadiran</label>
    <select name="status_kehadiran" required>
        <option value="Hadir" <?php echo ($data['status_kehadiran'] == 'Hadir') ? 'selected' : ''; ?>>Hadir</option>
        <option value="Tidak Hadir" <?php echo ($data['status_kehadiran'] == 'Tidak Hadir') ? 'selected' : ''; ?>>Tidak Hadir</option>
    </select>

    <label for="tanggal_absensi">Tanggal Absensi</label>
    <input type="date" name="tanggal_absensi" value="<?php echo $data['tanggal_absensi']; ?>" required>

    <button type="submit">Simpan Perubahan</button>
</form>

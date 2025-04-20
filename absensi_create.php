<?php
// Koneksi ke database menggunakan PDO
include '../includes/db.php';
include 'header.php';

// Ambil daftar komisi dari database
try {
    $komisi_query = "SELECT DISTINCT komisi FROM jemaat ORDER BY komisi ASC";
    $komisi_stmt = $conn->prepare($komisi_query);
    $komisi_stmt->execute();
    $daftar_komisi = $komisi_stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("Error: Gagal mengambil data komisi. " . $e->getMessage());
}

// Ambil komisi yang dipilih dari GET parameter
$komisi_terpilih = isset($_GET['komisi']) ? $_GET['komisi'] : '';

// Ambil data jemaat sesuai komisi
try {
    if (!empty($komisi_terpilih)) {
        $query = "SELECT nomor_anggota, nama_anggota, komisi FROM jemaat WHERE komisi = :komisi ORDER BY nama_anggota ASC";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':komisi', $komisi_terpilih, PDO::PARAM_STR);
    } else {
        $query = "SELECT nomor_anggota, nama_anggota, komisi FROM jemaat ORDER BY nama_anggota ASC";
        $stmt = $conn->prepare($query);
    }
    $stmt->execute();
    $jemaat = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: Tidak dapat mengambil data jemaat. " . $e->getMessage());
}

// Proses penyimpanan absensi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal_absensi = isset($_POST['tanggal_absensi']) ? $_POST['tanggal_absensi'] : '';
    $kehadiran = isset($_POST['kehadiran']) ? $_POST['kehadiran'] : [];

    if (!empty($tanggal_absensi)) {
        try {
            foreach ($kehadiran as $nomor_anggota => $status_kehadiran) {
                $query = "INSERT INTO absensi (nomor_anggota, status_kehadiran, tanggal_absensi) 
                          VALUES (:nomor_anggota, :status_kehadiran, :tanggal_absensi)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':nomor_anggota', $nomor_anggota, PDO::PARAM_STR);
                $stmt->bindParam(':status_kehadiran', $status_kehadiran, PDO::PARAM_STR);
                $stmt->bindParam(':tanggal_absensi', $tanggal_absensi, PDO::PARAM_STR);
                $stmt->execute();
            }
            echo "<div class='alert alert-success'>Data absensi berhasil disimpan.</div>";
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Error: Gagal menyimpan data absensi. " . $e->getMessage() . "</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>Tanggal absensi harus diisi!</div>";
    }

    // Redirect agar tidak terjadi re-submit saat reload
    header("Location: absensi_create.php?komisi=" . urlencode($komisi_terpilih));
    exit;
}
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Form Absensi Jemaat</h2>

    <!-- Filter Komisi -->
    <form method="GET" action="absensi_create.php" class="mb-4">
        <label for="komisi" class="form-label">Pilih Komisi</label>
        <select class="form-select" id="komisi" name="komisi" onchange="this.form.submit()">
            <option value="">-- Semua Komisi --</option>
            <?php foreach ($daftar_komisi as $komisi): ?>
                <option value="<?= htmlspecialchars($komisi); ?>" <?= ($komisi == $komisi_terpilih) ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($komisi); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <!-- Form Absensi -->
    <form action="absensi_create.php?komisi=<?= urlencode($komisi_terpilih); ?>" method="POST">
        <div class="mb-3">
            <label for="tanggal_absensi" class="form-label">Tanggal Absensi</label>
            <input type="date" class="form-control" id="tanggal_absensi" name="tanggal_absensi" required>
        </div>

        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama Anggota</th>
                    <th>Komisi</th>
                    <th>Status Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($jemaat)): ?>
                    <?php foreach ($jemaat as $index => $row): ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td><?= htmlspecialchars($row['nama_anggota']); ?></td>
                            <td><?= htmlspecialchars($row['komisi']); ?></td>
                            <td>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" 
                                           id="hadir_<?= $row['nomor_anggota']; ?>" 
                                           name="kehadiran[<?= $row['nomor_anggota']; ?>]" 
                                           value="Hadir" required>
                                    <label class="form-check-label" for="hadir_<?= $row['nomor_anggota']; ?>">Hadir</label>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data jemaat.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Simpan Absensi</button>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

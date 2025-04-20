<?php
include '../includes/db.php';
include 'header.php';

$tanggal_mulai = $_GET['tanggal_mulai'] ?? '';
$tanggal_selesai = $_GET['tanggal_selesai'] ?? '';
$filter_komisi = $_GET['komisi'] ?? '';
$status_kehadiran = $_GET['status_kehadiran'] ?? '';

// Inisialisasi array data
$absensi = [];

// Jika filter 'Belum Absen' dipilih
if ($status_kehadiran === 'Belum Absen' && !empty($tanggal_mulai) && !empty($tanggal_selesai)) {
    $query = "
        SELECT 
            jemaat.nomor_anggota,
            jemaat.nama_anggota,
            jemaat.komisi
        FROM jemaat
        WHERE NOT EXISTS (
            SELECT 1 FROM absensi 
            WHERE absensi.nomor_anggota = jemaat.nomor_anggota
            AND absensi.tanggal_absensi BETWEEN :tanggal_mulai AND :tanggal_selesai
        )
    ";
    if (!empty($filter_komisi)) {
        $query .= " AND jemaat.komisi = :komisi";
    }
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':tanggal_mulai', $tanggal_mulai);
    $stmt->bindParam(':tanggal_selesai', $tanggal_selesai);
    if (!empty($filter_komisi)) {
        $stmt->bindParam(':komisi', $filter_komisi);
    }
    $stmt->execute();
    $absensi = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Query data absensi normal
    $query = "
        SELECT 
            absensi.id, 
            absensi.nomor_anggota, 
            absensi.status_kehadiran, 
            absensi.tanggal_absensi, 
            jemaat.nama_anggota, 
            jemaat.komisi 
        FROM absensi
        JOIN jemaat ON absensi.nomor_anggota = jemaat.nomor_anggota
        WHERE 1=1";

    if (!empty($tanggal_mulai) && !empty($tanggal_selesai)) {
        $query .= " AND absensi.tanggal_absensi BETWEEN :tanggal_mulai AND :tanggal_selesai";
    }
    if (!empty($filter_komisi)) {
        $query .= " AND jemaat.komisi = :komisi";
    }
    if (!empty($status_kehadiran) && $status_kehadiran !== 'Belum Absen') {
        $query .= " AND absensi.status_kehadiran = :status_kehadiran";
    }
    $query .= " ORDER BY absensi.tanggal_absensi DESC";

    $stmt = $conn->prepare($query);

    if (!empty($tanggal_mulai) && !empty($tanggal_selesai)) {
        $stmt->bindParam(':tanggal_mulai', $tanggal_mulai);
        $stmt->bindParam(':tanggal_selesai', $tanggal_selesai);
    }
    if (!empty($filter_komisi)) {
        $stmt->bindParam(':komisi', $filter_komisi);
    }
    if (!empty($status_kehadiran) && $status_kehadiran !== 'Belum Absen') {
        $stmt->bindParam(':status_kehadiran', $status_kehadiran);
    }

    $stmt->execute();
    $absensi = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Komisi list
$queryKomisi = "SELECT DISTINCT komisi FROM jemaat WHERE komisi IS NOT NULL";
$stmtKomisi = $conn->prepare($queryKomisi);
$stmtKomisi->execute();
$komisiList = $stmtKomisi->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Absensi Jemaat</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Data Absensi Jemaat</h1>

    <div class="mb-4">
        <a href="absensi_create.php" class="btn btn-success">Tambah Absensi</a>
    </div>
    <div class="mb-4">
        <a href="absensi_chart.php" class="btn btn-info mt-3">Lihat Grafik Kehadiran</a>
    </div>

    <!-- Filter -->
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <label for="tanggal_mulai">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control" value="<?= htmlspecialchars($tanggal_mulai); ?>">
            </div>
            <div class="col-md-3">
                <label for="tanggal_selesai">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" class="form-control" value="<?= htmlspecialchars($tanggal_selesai); ?>">
            </div>
            <div class="col-md-3">
                <label for="komisi">Komisi</label>
                <select name="komisi" class="form-control">
                    <option value="">Semua Komisi</option>
                    <?php foreach ($komisiList as $komisi): ?>
                        <option value="<?= htmlspecialchars($komisi['komisi']); ?>" <?= $filter_komisi === $komisi['komisi'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($komisi['komisi']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="status_kehadiran">Status Kehadiran</label>
                <select name="status_kehadiran" class="form-control">
                    <option value="">Semua</option>
                    <option value="Hadir" <?= $status_kehadiran === 'Hadir' ? 'selected' : ''; ?>>Hadir</option>
                    <option value="Belum Absen" <?= $status_kehadiran === 'Belum Absen' ? 'selected' : ''; ?>> Absen</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Filter</button>
        <a href="absensi_index.php" class="btn btn-danger mt-3">Reset</a>
        <a href="absensi_print.php" class="btn btn-secondary mt-3">Print</a>
    </form>

    <!-- Tabel -->
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>#</th>
            <th>Nama Anggota</th>
            <th>Komisi</th>
            <?php if ($status_kehadiran !== 'Belum Absen'): ?>
                <th>Status Kehadiran</th>
                <th>Tanggal Absensi</th>
            <?php endif; ?>
        </tr>
        </thead>
        <tbody>
        <?php if (count($absensi) > 0): ?>
            <?php foreach ($absensi as $index => $data): ?>
                <tr>
                    <td><?= $index + 1; ?></td>
                    <td><?= htmlspecialchars($data['nama_anggota']); ?></td>
                    <td><?= htmlspecialchars($data['komisi']); ?></td>
                    <?php if ($status_kehadiran !== 'Belum Absen'): ?>
                        <td><?= htmlspecialchars($data['status_kehadiran']); ?></td>
                        <td><?= htmlspecialchars($data['tanggal_absensi']); ?></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="<?= $status_kehadiran === 'Belum Absen' ? '3' : '5'; ?>" class="text-center">Tidak ada data ditemukan.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>

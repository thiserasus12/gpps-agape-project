<?php
include '../includes/db.php';
include 'header.php';

$komisiFilter = $_GET['komisi'] ?? '';
$tanggalDari = $_GET['tanggal_dari'] ?? '';
$tanggalSampai = $_GET['tanggal_sampai'] ?? '';
$statusFilter = $_GET['status'] ?? '';

// Query utama untuk grafik
$query = "
    SELECT 
        a.tanggal_absensi, 
        COUNT(*) AS jumlah 
    FROM absensi a
    INNER JOIN jemaat j ON a.nomor_anggota = j.nomor_anggota
    WHERE 1 = 1
";

if ($statusFilter == 'Hadir') {
    $query .= " AND a.status_kehadiran = 'Hadir'";
} elseif ($statusFilter == 'Belum Hadir') {
    $query .= " AND a.status_kehadiran != 'Hadir'";
}

if (!empty($komisiFilter)) {
    $query .= " AND j.komisi = :komisi";
}
if (!empty($tanggalDari) && !empty($tanggalSampai)) {
    $query .= " AND a.tanggal_absensi BETWEEN :tanggal_dari AND :tanggal_sampai";
}

$query .= " GROUP BY a.tanggal_absensi ORDER BY a.tanggal_absensi";
$stmt = $conn->prepare($query);

// Binding parameter
if (!empty($komisiFilter)) {
    $stmt->bindParam(':komisi', $komisiFilter);
}
if (!empty($tanggalDari) && !empty($tanggalSampai)) {
    $stmt->bindParam(':tanggal_dari', $tanggalDari);
    $stmt->bindParam(':tanggal_sampai', $tanggalSampai);
}
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil data untuk grafik
$tanggal = [];
$jumlah = [];

foreach ($data as $row) {
    $tanggal[] = $row['tanggal_absensi'];
    $jumlah[] = $row['jumlah'];
}

// Query untuk total Hadir
$totalHadirStmt = $conn->prepare("
    SELECT COUNT(*) FROM absensi a
    INNER JOIN jemaat j ON a.nomor_anggota = j.nomor_anggota
    WHERE a.status_kehadiran = 'Hadir'
    " . (!empty($komisiFilter) ? " AND j.komisi = :komisi" : "") .
    (!empty($tanggalDari) && !empty($tanggalSampai) ? " AND a.tanggal_absensi BETWEEN :dari AND :sampai" : "")
);
if (!empty($komisiFilter)) $totalHadirStmt->bindParam(':komisi', $komisiFilter);
if (!empty($tanggalDari) && !empty($tanggalSampai)) {
    $totalHadirStmt->bindParam(':dari', $tanggalDari);
    $totalHadirStmt->bindParam(':sampai', $tanggalSampai);
}
$totalHadirStmt->execute();
$totalHadir = $totalHadirStmt->fetchColumn();

// Query untuk total Tidak Hadir
$totalTidakHadirStmt = $conn->prepare("
    SELECT COUNT(*) FROM absensi a
    INNER JOIN jemaat j ON a.nomor_anggota = j.nomor_anggota
    WHERE a.status_kehadiran != 'Hadir'
    " . (!empty($komisiFilter) ? " AND j.komisi = :komisi" : "") .
    (!empty($tanggalDari) && !empty($tanggalSampai) ? " AND a.tanggal_absensi BETWEEN :dari AND :sampai" : "")
);
if (!empty($komisiFilter)) $totalTidakHadirStmt->bindParam(':komisi', $komisiFilter);
if (!empty($tanggalDari) && !empty($tanggalSampai)) {
    $totalTidakHadirStmt->bindParam(':dari', $tanggalDari);
    $totalTidakHadirStmt->bindParam(':sampai', $tanggalSampai);
}
$totalTidakHadirStmt->execute();
$totalTidakHadir = $totalTidakHadirStmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Grafik Kehadiran Jemaat</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container mt-5" id="printArea">
    <h2>Grafik Kehadiran Jemaat</h2>

    <form method="GET" class="form-inline mb-4">
        <label class="mr-2">Komisi:</label>
        <select name="komisi" class="form-control mr-2">
            <option value="">Semua Komisi</option>
            <option value="Youth" <?= $komisiFilter == 'Youth' ? 'selected' : '' ?>>Youth</option>
            <option value="Umum" <?= $komisiFilter == 'Umum' ? 'selected' : '' ?>>Umum</option>
            <option value="Sekolah Minggu" <?= $komisiFilter == 'Sekolah Minggu' ? 'selected' : '' ?>>Sekolah Minggu</option>
            <option value="Teens" <?= $komisiFilter == 'Teens' ? 'selected' : '' ?>>Tunas Remaja</option>
        </select>

        <label class="mr-2">Status:</label>
        <select name="status" class="form-control mr-2">
            <option value="">Semua</option>
            <option value="Hadir" <?= $statusFilter == 'Hadir' ? 'selected' : '' ?>>Hadir</option>
            <option value="Belum Hadir" <?= $statusFilter == 'Belum Hadir' ? 'selected' : '' ?>>Belum Hadir</option>
        </select>

        <label class="mr-2">Dari:</label>
        <input type="date" name="tanggal_dari" class="form-control mr-2" value="<?= $tanggalDari ?>">

        <label class="mr-2">Sampai:</label>
        <input type="date" name="tanggal_sampai" class="form-control mr-2" value="<?= $tanggalSampai ?>">

        <button type="submit" class="btn btn-primary">Tampilkan</button>
    </form>

    <canvas id="fluktuasiChart" height="100"></canvas>

    <div class="mt-4">
        <h5>Rekap Kehadiran</h5>
        <p>Total Hadir: <strong><?= $totalHadir ?></strong></p>
        <p>Total Tidak Hadir: <strong><?= $totalTidakHadir ?></strong></p>
    </div>

    <br>


    <button class="btn btn-success mt-3" onclick="printDiv()">Cetak Laporan</button>
    <a href="absensi_list.php" class="btn btn-secondary mt-3">Kembali ke Data Absensi</a>
</div>

<br>
<br>

<script>
const ctx = document.getElementById('fluktuasiChart').getContext('2d');
const fluktuasiChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($tanggal); ?>,
        datasets: [{
            label: 'Jumlah Jemaat',
            data: <?= json_encode($jumlah); ?>,
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.2)',
            tension: 0.3,
            fill: true,
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Fluktuasi Kehadiran Jemaat per Tanggal'
            },
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Tanggal'
                }
            },
            y: {
                title: {
                    display: true,
                    text: 'Jumlah Jemaat'
                },
                beginAtZero: true,
                precision: 0
            }
        }
    }
});

// Fungsi Print
function printDiv() {
    var printContents = document.getElementById('printArea').innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload(); // reload halaman agar tetap utuh
}
</script>
</body>
</html>

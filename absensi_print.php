<?php
// Koneksi ke database menggunakan PDO
include '../includes/db.php';

// Inisialisasi filter
$tanggal_mulai = isset($_GET['tanggal_mulai']) ? $_GET['tanggal_mulai'] : '';
$tanggal_selesai = isset($_GET['tanggal_selesai']) ? $_GET['tanggal_selesai'] : '';
$filter_komisi = isset($_GET['komisi']) ? $_GET['komisi'] : '';

// Query dasar
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

// Tambahkan filter tanggal jika diisi
if (!empty($tanggal_mulai) && !empty($tanggal_selesai)) {
    $query .= " AND absensi.tanggal_absensi BETWEEN :tanggal_mulai AND :tanggal_selesai";
}

// Tambahkan filter komisi jika diisi
if (!empty($filter_komisi)) {
    $query .= " AND jemaat.komisi = :komisi";
}

// Tambahkan pengurutan
$query .= " ORDER BY absensi.tanggal_absensi DESC";

// Siapkan statement
$stmt = $conn->prepare($query);

// Bind parameter untuk filter tanggal
if (!empty($tanggal_mulai) && !empty($tanggal_selesai)) {
    $stmt->bindParam(':tanggal_mulai', $tanggal_mulai, PDO::PARAM_STR);
    $stmt->bindParam(':tanggal_selesai', $tanggal_selesai, PDO::PARAM_STR);
}

// Bind parameter untuk filter komisi
if (!empty($filter_komisi)) {
    $stmt->bindParam(':komisi', $filter_komisi, PDO::PARAM_STR);
}

// Eksekusi query
$stmt->execute();

// Ambil data
$absensi = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Query untuk mengambil daftar komisi dari tabel jemaat
$queryKomisi = "SELECT DISTINCT komisi FROM jemaat WHERE komisi IS NOT NULL";
$stmtKomisi = $conn->prepare($queryKomisi);
$stmtKomisi->execute();
$komisiList = $stmtKomisi->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Absensi</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script>
        function printTable() {
            const printContent = document.getElementById('data-table').outerHTML;
            const originalContent = document.body.innerHTML;

            document.body.innerHTML = `<html><head><title>Cetak Tabel</title></head><body>${printContent}</body></html>`;
            window.print();
            document.body.innerHTML = originalContent;
            window.location.reload(); // Reload halaman setelah selesai mencetak
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        
    <div class="d-flex align-items-center mb-4">
    <a href="Absensi_list.php" class="btn btn-outline-secondary me-6" style="margin-right: 20px;">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
    
    <h2 class="text-center mb-2">Data Absensi Jemaat</h2>
</div>


        <!-- Filter Form -->
        <form method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <label for="tanggal_mulai">Tanggal Mulai</label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control" value="<?= htmlspecialchars($tanggal_mulai); ?>">
                </div>
                <div class="col-md-4">
                    <label for="tanggal_selesai">Tanggal Selesai</label>
                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" class="form-control" value="<?= htmlspecialchars($tanggal_selesai); ?>">
                </div>
                <div class="col-md-4">
                    <label for="komisi">Komisi</label>
                    <select id="komisi" name="komisi" class="form-control">
                        <option value="">Semua Komisi</option>
                        <?php foreach ($komisiList as $komisi) : ?>
                            <option value="<?= htmlspecialchars($komisi['komisi']); ?>" <?= $filter_komisi === $komisi['komisi'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($komisi['komisi']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Filter</button>
            <a href="absensi_index.php" class="btn btn-danger mt-3">Reset</a>
            <button type="button" class="btn btn-secondary mt-3" onclick="printTable()">
                <i class="fas fa-print"></i> Cetak Tabel
            </button>
        </form>

        <!-- Data Table -->
        <table class="table table-bordered" id="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Anggota</th>
                    <th>Komisi</th>
                    <th>Status Kehadiran</th>
                    <th>Tanggal Absensi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($stmt->rowCount() > 0) : ?>
                    <?php foreach ($absensi as $index => $data) : ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td><?= htmlspecialchars($data['nama_anggota']); ?></td>
                            <td><?= htmlspecialchars($data['komisi']); ?></td>
                            <td><?= htmlspecialchars($data['status_kehadiran']); ?></td>
                            <td><?= htmlspecialchars($data['tanggal_absensi']); ?></td>
                            
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data absensi.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>

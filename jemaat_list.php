<?php
include '../includes/db.php';
include 'header.php';

$filter_komisi = $_GET['komisi'] ?? '';
$filter_pernikahan = $_GET['status_pernikahan'] ?? '';
$filter_nama = $_GET['nama_anggota'] ?? '';
$filter_baptis = $_GET['status_baptis'] ?? '';



try {
    $query = "SELECT * FROM jemaat WHERE 1";
    
    if ($filter_komisi) {
        $query .= " AND komisi = :komisi";
    }
    if ($filter_pernikahan) {
        $query .= " AND status_pernikahan = :status_pernikahan";
    }
    if ($filter_nama) {
        $query .= " AND nama_anggota LIKE :nama_anggota";
    }

    if ($filter_baptis) {
        $query .= " AND status_baptis = :status_baptis";
    }

    $stmt = $conn->prepare($query);

    if ($filter_komisi) {
        $stmt->bindParam(':komisi', $filter_komisi);
    }
    if ($filter_pernikahan) {
        $stmt->bindParam(':status_pernikahan', $filter_pernikahan);
    }
    if ($filter_nama) {
        $stmt->bindValue(':nama_anggota', '%' . $filter_nama . '%');
    }

    if ($filter_baptis) {
        $stmt->bindParam(':status_baptis', $filter_baptis);
    }

    $stmt->execute();
    $jemaat = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<div class="container mt-4">
    <h2 class="text-center mb-4">Daftar Jemaat</h2>
    <!-- Card Filter -->
    <div class="card p-4 mb-4">
        <h4 class="card-title">Filter Data</h4>
        <form method="GET">
            <div class="row">
                <div class="col-md-3">
                    <label for="komisi">Komisi</label>
                    <select name="komisi" id="komisi" class="form-control">
                        <option value="">--Komisi--</option>
                        <option value="Sekolah Minggu" <?= $filter_komisi == 'Sekolah Minggu' ? 'selected' : ''; ?>>Sekolah Minggu</option>
                        <option value="Teens" <?= $filter_komisi == 'Teens' ? 'selected' : ''; ?>>Teens</option>
                        <option value="Youth" <?= $filter_komisi == 'Youth' ? 'selected' : ''; ?>>Youth</option>
                        <option value="Umum" <?= $filter_komisi == 'Umum' ? 'selected' : ''; ?>>Umum</option>
                        <option value="AMC" <?= $filter_komisi == 'AMC' ? 'selected' : ''; ?>>AMC (Agape Men's Community)</option>
                        <option value="AWC" <?= $filter_komisi == 'AWC' ? 'selected' : ''; ?>>AWC (Agape Women Community)</option>
                        <option value="ACC" <?= $filter_komisi == 'ACC' ? 'selected' : ''; ?>>ACC (Agape Couple Community)</option>


                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status_pernikahan">Status Pernikahan</label>
                    <select name="status_pernikahan" id="status_pernikahan" class="form-control">
                        <option value="">--Status--</option>
                        <option value="Menikah" <?= $filter_pernikahan == 'Menikah' ? 'selected' : ''; ?>>Menikah</option>
                        <option value="Belum Menikah" <?= $filter_pernikahan == 'Belum Menikah' ? 'selected' : ''; ?>>Belum Menikah</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status_baptis">Status Baptis</label>
                    <select name="status_baptis" id="status_baptis" class="form-control">
                        <option value="">--Status Baptis--</option>
                        <option value="Baptis" <?= $filter_pernikahan == 'Baptis' ? 'selected' : ''; ?>>Baptis</option>
                        <option value="Belum Baptis" <?= $filter_pernikahan == 'Belum Baptis' ? 'selected' : ''; ?>>Belum Baptis</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="nama_anggota">Nama Anggota</label>
                    <input type="text" name="nama_anggota" id="nama_anggota" class="form-control" value="<?= htmlspecialchars($filter_nama); ?>" placeholder="Cari Nama Anggota">
                </div>
                <div class="col-md-12 text-right mt-3">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                    <a href="jemaat_list.php" class="btn btn-danger"><i class="fas fa-redo"></i> Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Card Table -->
    <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
    <div>
        <h4 class="mb-0">Data Jemaat</h4>
    </div>
    <div>
        <a href="jemaat_create.php" class="btn btn-success me-2">
            <i class="fas fa-plus"></i> Tambah Jemaat
        </a>
        <a href="list_print.php" class="btn btn-secondary me-2">
            <i class="fas fa-plus"></i> Print
        </a>
    </div>
    </div>

        <div class="card-body p-0" id="print-area">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Nomor Anggota</th>
                            <th>Nama Anggota</th>
                            <th>Jenis Kelamin</th>
                            <th>Komisi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($jemaat)): ?>
                            <?php foreach ($jemaat as $index => $row): ?>
                                <tr>
                                    <td><?= $index + 1; ?></td>
                                    <td><?= htmlspecialchars($row['nomor_anggota']); ?></td>
                                    <td><?= htmlspecialchars($row['nama_anggota']); ?></td>
                                    <td><?= htmlspecialchars($row['jenis_kelamin']); ?></td>
                                    <td><?= htmlspecialchars($row['komisi']); ?></td>
                                    <td>
                                        <a href="jemaat_update.php?id=<?= $row['id']; ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="jemaat_read.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-eye"></i> Read</a>
                                        <a href="jemaat_delete.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?');"><i class="fas fa-trash"></i> Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Data tidak tersedia</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<footer class="mt-5 text-center">
    <?php include 'footer.php'; ?>
</footer>


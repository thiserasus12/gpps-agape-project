<?php
include '../includes/db.php';


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



<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Daftar Jemaat</title>
  </head>
  <body>

  <div class="container mt-4">
  <div class="container mt-4">
    <div class="d-flex align-items-center mb-4">
        <a href="jemaat_list.php" class="btn btn-outline-secondary me-3">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <h2 class="text-center mb-2">Daftar Jemaat</h2>
    </div>
    <!-- Card Filter -->
    <div class="card p-4 mb-4">
        <h4 class="card-title">Filter Data</h4>
        <form method="GET">
            <div class="row">
                <div class="col-md-3">
                    <label for="komisi">Komisi</label>
                    <select name="komisi" id="komisi" class="form-control">
                        <option value="">Semua Komisi</option>
                        <option value="Sekolah Minggu" <?= $filter_komisi == 'Sekolah Minggu' ? 'selected' : ''; ?>>Sekolah Minggu</option>
                        <option value="Teens" <?= $filter_komisi == 'Teens' ? 'selected' : ''; ?>>Teens</option>
                        <option value="Youth" <?= $filter_komisi == 'Youth' ? 'selected' : ''; ?>>Youth</option>
                        <option value="Umum" <?= $filter_komisi == 'Umum' ? 'selected' : ''; ?>>Umum</option>
                        <option value="AMC" <?= $filter_komisi == 'AMC' ? 'selected' : ''; ?>>AMC</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status_pernikahan">Status Pernikahan</label>
                    <select name="status_pernikahan" id="status_pernikahan" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="Menikah" <?= $filter_pernikahan == 'Menikah' ? 'selected' : ''; ?>>Menikah</option>
                        <option value="Belum Menikah" <?= $filter_pernikahan == 'Belum Menikah' ? 'selected' : ''; ?>>Belum Menikah</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status_baptis">Status Baptis</label>
                    <select name="status_baptis" id="status_baptis" class="form-control">
                        <option value="">Semua Status</option>
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
                    <a href="list_print.php" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset</a>
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
            
            <button class="btn btn-secondary" onclick="printCard()">
                <i class="fas fa-print"></i> Print
            </button>
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
                    <th>Alamat</th>
                    <th>Nomor Telepon</th>


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
                            <td><?= htmlspecialchars($row['alamat']); ?></td>
                            <td><?= htmlspecialchars($row['nomor_telepon']); ?></td>

                            
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



<script>
    function printCard() {
        var printContent = document.getElementById('print-area').innerHTML;
        var originalContent = document.body.innerHTML;

        document.body.innerHTML = printContent;
        window.print();
        document.body.innerHTML = originalContent;
    }
</script>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #print-area, #print-area * {
            visibility: visible;
        }
        #print-area {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
        }
    }
</style>

</div>



    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    
  </body>

  <br>
  <br>
  <br>
  <br>
</html>
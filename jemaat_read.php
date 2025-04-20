<?php
include '../includes/db.php';
include 'header.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "ID tidak ditemukan.";
    exit;
}

try {
    // Ambil data jemaat berdasarkan ID
    $stmt = $conn->prepare("SELECT * FROM jemaat WHERE id = ?");
    $stmt->execute([$id]);
    $jemaat = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$jemaat) {
        echo "Data jemaat tidak ditemukan.";
        exit;
    }

    // Cari ID Ayah
    $stmt_ayah = $conn->prepare("SELECT id FROM jemaat WHERE nama_anggota = ?");
    $stmt_ayah->execute([$jemaat['nama_ayah']]);
    $ayah = $stmt_ayah->fetch(PDO::FETCH_ASSOC);

    // Cari ID Ibu
    $stmt_ibu = $conn->prepare("SELECT id FROM jemaat WHERE nama_anggota = ?");
    $stmt_ibu->execute([$jemaat['nama_ibu']]);
    $ibu = $stmt_ibu->fetch(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<div class="container mt-4">
    <h2 class="text-center mb-4">Detail Data Jemaat</h2>
    <div class="card shadow-sm">
        <div class="card-body" id="printableArea">
            <div class="row mb-3">
                <div class="col-md-4">
                    <img 
                        src="<?= !empty($jemaat['image']) ? 'uploads/' . htmlspecialchars($jemaat['image']) : 'path/to/default-image.jpg'; ?>" 
                        alt="Image" 
                        class="img-thumbnail"
                        style="width: 100%; height: auto;">
                </div>
                <div class="col-md-8">
                    <h4 class="card-title">Nama: <?= htmlspecialchars($jemaat['nama_anggota']); ?></h4>
                    <p class="card-text">Nomor Anggota: <?= htmlspecialchars($jemaat['nomor_anggota']); ?></p>
                    <p class="card-text">Jenis Kelamin: <?= htmlspecialchars($jemaat['jenis_kelamin']); ?></p>
                    <p class="card-text">Tanggal Lahir: <?= htmlspecialchars($jemaat['tanggal_lahir']); ?></p>
                    <p class="card-text">Kota Kelahiran: <?= htmlspecialchars($jemaat['kota_kelahiran']); ?></p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Alamat:</strong> <?= htmlspecialchars($jemaat['alamat']); ?></li>
                        <li class="list-group-item"><strong>Nomor Telepon:</strong> <?= htmlspecialchars($jemaat['nomor_telepon']); ?></li>
                        <li class="list-group-item"><strong>Email:</strong> <?= htmlspecialchars($jemaat['alamat_email']); ?></li>
                        <li class="list-group-item"><strong>Golongan Darah:</strong> <?= htmlspecialchars($jemaat['golongan_darah']); ?></li>
                        <li class="list-group-item"><strong>Komisi:</strong> <?= htmlspecialchars($jemaat['komisi']); ?></li>
                        <li class="list-group-item"><strong>Segmen:</strong> <?= htmlspecialchars($jemaat['segmen']); ?></li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Nama Ayah:</strong> 
                            <?= $ayah ? "<a href='jemaat_read.php?id=" . htmlspecialchars($ayah['id']) . "'>" . htmlspecialchars($jemaat['nama_ayah']) . "</a>" 
                                    : htmlspecialchars($jemaat['nama_ayah']); ?>
                        </li>
                        <li class="list-group-item"><strong>Nama Ibu:</strong> 
                            <?= $ibu ? "<a href='jemaat_read.php?id=" . htmlspecialchars($ibu['id']) . "'>" . htmlspecialchars($jemaat['nama_ibu']) . "</a>" 
                                    : htmlspecialchars($jemaat['nama_ibu']); ?>
                        </li>
                        <li class="list-group-item"><strong>Status Baptis:</strong> <?= htmlspecialchars($jemaat['status_baptis']); ?></li>
                        <li class="list-group-item"><strong>Tanggal Baptis:</strong> <?= htmlspecialchars($jemaat['tanggal_baptis']); ?></li>
                        <li class="list-group-item"><strong>Pelaksana Baptis:</strong> <?= htmlspecialchars($jemaat['pelaksana_baptis']); ?></li>
                    </ul>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Status Pernikahan:</strong> <?= htmlspecialchars($jemaat['status_pernikahan']); ?></li>
                        <li class="list-group-item"><strong>Tanggal Pernikahan:</strong> <?= htmlspecialchars($jemaat['tanggal_pernikahan']); ?></li>
                        <li class="list-group-item"><strong>Pelaksana Pernikahan:</strong> <?= htmlspecialchars($jemaat['pelaksana_pernikahan']); ?></li>
                        <li class="list-group-item"><strong>Nama Pasangan:</strong> <?= htmlspecialchars($jemaat['nama_suami'] ?: $jemaat['nama_istri']); ?></li>
                        <li class="list-group-item"><strong>Status Kematian:</strong> <?= htmlspecialchars($jemaat['status_kematian']); ?></li>
                        <li class="list-group-item"><strong>Tanggal Kematian:</strong> <?= htmlspecialchars($jemaat['tanggal_kematian']); ?></li>
                    </ul>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="jemaat_list.php" class="btn btn-secondary">Kembali</a>
                <a href="kartu_jemaat.php?id=<?= htmlspecialchars($jemaat['id']); ?>" class="btn btn-warning">Card</a>
                <button onclick="printCard()" class="btn btn-primary">Print</button>
            </div>
        </div>
    </div>
</div>

<script>
    function printCard() {
        const printContents = document.getElementById('printableArea').innerHTML;
        const originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        window.location.reload();
    }
</script>

<style>
    @media print {
        body * { visibility: hidden; }
        #printableArea, #printableArea * { visibility: visible; }
        #printableArea { position: absolute; left: 0; top: 0; width: 100%; }
    }
</style>

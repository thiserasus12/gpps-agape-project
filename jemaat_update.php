<?php
include '../includes/db.php';
include 'header.php';

// Inisialisasi variabel
$errors = [];
$id = $_GET['id'] ?? null;

// Periksa apakah ID valid
if (!$id) {
    header("Location: list.php");
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
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

// Proses data form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Daftar field yang akan diupdate
    $fields = [
        'nama_anggota',
        'alamat_email',
        'nomor_telepon',
        'jenis_kelamin',
        'tanggal_lahir',
        'kota_kelahiran',
        'alamat',
        'golongan_darah',
        'komisi',
        'nama_ayah',
        'nama_ibu',
        'status_baptis',
        'tanggal_baptis',
        'pelaksana_baptis',
        'status_pernikahan',
        'tanggal_pernikahan',
        'pelaksana_pernikahan',
        'nama_istri',
        'nama_suami',
        'segmen',
        'status_kematian',
        'tanggal_kematian'
    ];

    $updateData = [];
    foreach ($fields as $field) {
        $updateData[$field] = $_POST[$field] ?? null;
    }

    // Proses file gambar jika ada
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        $fileName = basename($_FILES['image']['name']);
        $targetFilePath = $targetDir . $fileName;

        // Periksa apakah folder uploads/ ada
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true); // Buat folder jika belum ada
        }

        // Periksa ekstensi file
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                $updateData['image'] = $fileName;
            } else {
                $errors[] = "Gagal mengunggah gambar. Pastikan folder 'uploads/' memiliki izin yang sesuai.";
            }
        } else {
            $errors[] = "Format file tidak didukung. Hanya JPG, JPEG, PNG, dan GIF.";
        }
    } else {
        $updateData['image'] = $jemaat['image']; // Gunakan gambar lama jika tidak diunggah baru
    }

    // Validasi sederhana
    // (Tambahkan validasi tambahan jika diperlukan)

    // Jika tidak ada error, lakukan update
    if (empty($errors)) {
        try {
            $query = "UPDATE jemaat SET 
                nama_anggota = ?, 
                alamat_email = ?, 
                nomor_telepon = ?, 
                jenis_kelamin = ?, 
                tanggal_lahir = ?, 
                kota_kelahiran = ?, 
                alamat = ?, 
                golongan_darah = ?, 
                komisi = ?, 
                nama_ayah = ?, 
                nama_ibu = ?, 
                status_baptis = ?, 
                tanggal_baptis = ?, 
                pelaksana_baptis = ?, 
                status_pernikahan = ?, 
                tanggal_pernikahan = ?, 
                pelaksana_pernikahan = ?, 
                nama_istri = ?, 
                nama_suami = ?, 
                status_kematian = ?, 
                segmen = ?,
                tanggal_kematian = ?, 
                image = ? 
                WHERE id = ?";

            $stmt = $conn->prepare($query);
            $stmt->execute([...array_values($updateData), $id]);

            // Redirect setelah sukses
            header("Location: jemaat_list.php");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Gagal mengupdate data: " . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Data Jemaat</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
<div class=" form-container container mt-5">
    <h2 class="mb-4 text-center">Form Update Data Jemaat</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <!-- Nomor Anggota dan Nama -->
        <div class="row">
        <div class=" col-md-6 mb-3">
                <label for="nomor_anggota" class="form-label">Nomor Anggota</label>
                <input type="text" class="form-control" id="nomor_anggota" name="nomor_anggota" 
                       value="<?= htmlspecialchars($jemaat['nomor_anggota'] ?? '') ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="nama_anggota" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama_anggota" name="nama_anggota" 
                       value="<?= htmlspecialchars($jemaat['nama_anggota'] ?? '') ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="alamat_email" class="form-label">Email</label>
                <input type="email" class="form-control" id="alamat_email" name="alamat_email" 
                       value="<?= htmlspecialchars($jemaat['alamat_email'] ?? '') ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
                <input type="text" class="form-control" id="nomor_telepon" name="nomor_telepon" 
                       value="<?= htmlspecialchars($jemaat['nomor_telepon'] ?? '') ?>">
            </div>
        </div>

        <!-- Jenis Kelamin dan Tanggal Lahir -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                    <option value="">--Pilih--</option>
                    <option value="Pria" <?= isset($jemaat['jenis_kelamin']) && $jemaat['jenis_kelamin'] === 'Pria' ? 'selected' : '' ?>>Pria</option>
                    <option value="Wanita" <?= isset($jemaat['jenis_kelamin']) && $jemaat['jenis_kelamin'] === 'Wanita' ? 'selected' : '' ?>>Wanita</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="golongan_darah" class="form-label">golongan_darah</label>
                <select class="form-select" id="golongan_darah" name="golongan_darah" required>
                    <option value="">--Pilih--</option>
                    <option value="A" <?= isset($jemaat['golongan_darah']) && $jemaat['golongan_darah'] === 'A' ? 'selected' : '' ?>>A</option>
                    <option value="B" <?= isset($jemaat['golongan_darah']) && $jemaat['golongan_darah'] === 'B' ? 'selected' : '' ?>>B</option>
                    <option value="AB" <?= isset($jemaat['golongan_darah']) && $jemaat['golongan_darah'] === 'AB' ? 'selected' : '' ?>>AB</option>
                    <option value="O" <?= isset($jemaat['golongan_darah']) && $jemaat['golongan_darah'] === 'O' ? 'selected' : '' ?>>O</option>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" 
                       value="<?= htmlspecialchars($jemaat['tanggal_lahir'] ?? '') ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="kota_kelahiran" class="form-label">Kota kelahiran</label>
                <input type="text" class="form-control" id="kota_kelahiran" name="kota_kelahiran" 
                       value="<?= htmlspecialchars($jemaat['kota_kelahiran'] ?? '') ?>" required>
            </div>
        </div>

        <!-- Alamat -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea class="form-control" id="alamat" name="alamat" required><?= htmlspecialchars($jemaat['alamat'] ?? '') ?></textarea>
            </div>
        </div>
            <div class="row">
            <div class="col-md-6 mb-3">
                    <label for="komisi" class="form-label">Komisi</label>
                    <select class="form-select" id="komisi" name="komisi" required>
                        <option value="">--Pilih--</option>
                        <option value="Sekolah Minggu" <?= isset($jemaat['komisi']) && $jemaat['komisi'] === 'Sekolah Minggu' ? 'selected' : '' ?>>Sekolah Minggu</option>
                        <option value="Teens" <?= isset($jemaat['komisi']) && $jemaat['komisi'] === 'Teens' ? 'selected' : '' ?>>Teens</option>
                        <option value="Youth" <?= isset($jemaat['komisi']) && $jemaat['komisi'] === 'Youth' ? 'selected' : '' ?>>Youth</option>
                        <option value="Umum" <?= isset($jemaat['komisi']) && $jemaat['komisi'] === 'Umum' ? 'selected' : '' ?>>Umum</option>
                        <option value="AMC" <?= isset($jemaat['komisi']) && $jemaat['komisi'] === 'AMC' ? 'selected' : '' ?>>AMC</option>
                        <option value="AWC" <?= isset($jemaat['komisi']) && $jemaat['komisi'] === 'AWC' ? 'selected' : '' ?>>AWC</option>
                        <option value="ACC" <?= isset($jemaat['komisi']) && $jemaat['komisi'] === 'ACC' ? 'selected' : '' ?>>ACC</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="segmen" class="form-label">segmen</label>
                    <select class="form-select" id="segmen" name="segmen" required>
                        <option value="">--Pilih--</option>
                        <option value="Anak Anak" <?= isset($jemaat['segmen']) && $jemaat['segmen'] === 'Anak Anak' ? 'selected' : '' ?>>Anak Anak</option>
                        <option value="Remaja" <?= isset($jemaat['segmen']) && $jemaat['segmen'] === 'Remaja' ? 'selected' : '' ?>>Remaja</option>
                        <option value="Dewasa" <?= isset($jemaat['segmen']) && $jemaat['segmen'] === 'Dewasa' ? 'selected' : '' ?>>Dewasa</option>
                        <option value="Lansia" <?= isset($jemaat['segmen']) && $jemaat['segmen'] === 'Lansia' ? 'selected' : '' ?>>Lansia</option>
                    </select>
                </div>


        </div>    

        <div class="row">
        <div class="col-md-6 mb-3">
                <label for="nama_ayah" class="form-label">Nama Ayah</label>
                <input type="text" class="form-control" id="nama_ayah" name="nama_ayah" 
                       value="<?= htmlspecialchars($jemaat['nama_ayah'] ?? '') ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="nama_ibu" class="form-label">Nama Ibu</label>
                <input type="text" class="form-control" id="nama_ibu" name="nama_ibu" 
                       value="<?= htmlspecialchars($jemaat['nama_ibu'] ?? '') ?>" required>
            </div>
        </div>

        <div class="row">
    <div class="col-md-6 mb-3">
        <label for="status_baptis" class="form-label">Baptis</label>
        <select class="form-select" id="status_baptis" name="status_baptis" required>
            <option value="">--Pilih--</option>
            <option value="Baptis" <?= isset($jemaat['status_baptis']) && $jemaat['status_baptis'] === 'Baptis' ? 'selected' : '' ?>>Baptis</option>
            <option value="Belum Baptis" <?= isset($jemaat['status_baptis']) && $jemaat['status_baptis'] === 'Belum Baptis' ? 'selected' : '' ?>>Belum Baptis</option>
        </select>
    </div>
    <div class="col-md-6 mb-3 d-none" id="baptis_details">
        <label for="pelaksana_baptis" class="form-label">Pelaksana Baptisan</label>
        <input type="text" class="form-control" id="pelaksana_baptis" name="pelaksana_baptis" 
               value="<?= htmlspecialchars($jemaat['pelaksana_baptis'] ?? '') ?>">
        <label for="tanggal_baptis" class="form-label mt-2">Tanggal Pembaptisan</label>
        <input type="date" class="form-control" id="tanggal_baptis" name="tanggal_baptis" 
               value="<?= htmlspecialchars($jemaat['tanggal_baptis'] ?? '') ?>">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="status_pernikahan" class="form-label">Status Pernikahan</label>
        <select class="form-select" id="status_pernikahan" name="status_pernikahan">
            <option value="">--Pilih--</option>
            <option value="Menikah" <?= isset($jemaat['status_pernikahan']) && $jemaat['status_pernikahan'] === 'Menikah' ? 'selected' : '' ?>>Menikah</option>
            <option value="Belum Menikah" <?= isset($jemaat['status_pernikahan']) && $jemaat['status_pernikahan'] === 'Belum Menikah' ? 'selected' : '' ?>>Belum Menikah</option>
        </select>
    </div>
</div>

<div class="row d-none" id="marriage_details">
    <div class="col-md-6 mb-3">
        <label for="nama_suami" class="form-label">Nama Suami</label>
        <input type="text" class="form-control" id="nama_suami" name="nama_suami" 
               value="<?= htmlspecialchars($jemaat['nama_suami'] ?? '') ?>">
    </div>
    <div class="col-md-6 mb-3">
        <label for="nama_istri" class="form-label">Nama Istri</label>
        <input type="text" class="form-control" id="nama_istri" name="nama_istri" 
               value="<?= htmlspecialchars($jemaat['nama_istri'] ?? '') ?>">
    </div>
    <div class="col-md-6 mb-3">
        <label for="pelaksana_pemberkatan" class="form-label">Pelaksana Pernikahan</label>
        <input type="text" class="form-control" id="pelaksana_pemberkatan" name="pelaksana_pemberkatan" 
               value="<?= htmlspecialchars($jemaat['pelaksana_pernikahan'] ?? '') ?>">
    </div>
    <div class="col-md-6 mb-3">
        <label for="tanggal_pernikahan" class="form-label">Tanggal Pernikahan</label>
        <input type="date" class="form-control" id="tanggal_pernikahan" name="tanggal_pernikahan" 
               value="<?= htmlspecialchars($jemaat['tanggal_pernikahan'] ?? '') ?>">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="status_kematian" class="form-label">Status Kematian</label>
        <select class="form-select" id="status_kematian" name="status_kematian">
            <option value="">--Pilih--</option>
            <option value="Hidup" <?= isset($jemaat['status_kematian']) && $jemaat['status_kematian'] === 'Hidup' ? 'selected' : '' ?>>Hidup</option>
            <option value="Meninggal" <?= isset($jemaat['status_kematian']) && $jemaat['status_kematian'] === 'Meninggal' ? 'selected' : '' ?>>Meninggal</option>
        </select>
    </div>
</div>

<div class="row d-none" id="death_details">
    <div class="col-md-6 mb-3">
        <label for="tanggal_meninggal" class="form-label">Tanggal Meninggal</label>
        <input type="date" class="form-control" id="tanggal_meninggal" name="tanggal_meninggal" 
               value="<?= htmlspecialchars($jemaat['tanggal_meninggal'] ?? '') ?>">
    </div>
</div>

<div class="mb-3">
    <label for="image" class="form-label">Unggah Gambar</label>
    <input type="file" class="form-control" id="image" name="image">
    <?php if (!empty($jemaat['image'])): ?>
        <p>Gambar saat ini: <img src="uploads/<?= htmlspecialchars($jemaat['image']) ?>" alt="" width="100"></p>
    <?php endif; ?>
</div>

<script>
    document.getElementById('status_baptis').addEventListener('change', function () {
        document.getElementById('baptis_details').classList.toggle('d-none', this.value !== 'Baptis');
    });

    document.getElementById('status_pernikahan').addEventListener('change', function () {
        document.getElementById('marriage_details').classList.toggle('d-none', this.value !== 'Menikah');
    });

    document.getElementById('status_kematian').addEventListener('change', function () {
        document.getElementById('death_details').classList.toggle('d-none', this.value !== 'Meninggal');
    });
</script>

        <!-- Submit Button -->
        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Simpan Data</button>
        </div>
    </form>
</div>

</body>
</html>

<?php include 'footer.php'; ?>


<style>
    body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .hidden {
            display: none;
        }
        .form-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .form-header {
            text-align: center;
            margin-bottom: 20px;
        }
</style>



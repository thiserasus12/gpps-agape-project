<?php
include '../includes/db.php';
include 'header.php';

// Array untuk menyimpan pesan error
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form dengan validasi sederhana
    $fields = [
        'nomor_anggota', 'nama_anggota', 'jenis_kelamin', 'tanggal_lahir', 'kota_kelahiran', 'alamat',
        'nomor_telepon', 'alamat_email', 'golongan_darah', 'komisi', 'segmen', 'nama_ayah', 'nama_ibu', 
        'status_baptis', 'tanggal_baptis', 'pelaksana_baptis', 'status_pernikahan', 'tanggal_pernikahan', 
        'pelaksana_pernikahan', 'nama_istri', 'nama_suami', 'status_kematian', 'tanggal_kematian', 'qrcode'
    ];
    $data = [];
    foreach ($fields as $field) {
        $data[$field] = $_POST[$field] ?? null;
    }

    // File upload handling
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetFilePath = $targetDir . $fileName;
    
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                $image = $fileName;
            } else {
                $errors[] = "Gagal mengunggah gambar.";
            }
        } else {
            $errors[] = "Format file tidak didukung.";
        }
    }

    // Jika tidak ada error, masukkan data ke database
    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("
                INSERT INTO jemaat (
                    nomor_anggota, nama_anggota, jenis_kelamin, tanggal_lahir, kota_kelahiran, alamat, 
                    nomor_telepon, alamat_email, golongan_darah, komisi, segmen, nama_ayah, nama_ibu, 
                    status_baptis, tanggal_baptis, pelaksana_baptis, status_pernikahan, tanggal_pernikahan, 
                    pelaksana_pernikahan, nama_istri, nama_suami, status_kematian, tanggal_kematian, image, qrcode
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $data['nomor_anggota'], $data['nama_anggota'], $data['jenis_kelamin'], $data['tanggal_lahir'], 
                $data['kota_kelahiran'], $data['alamat'], $data['nomor_telepon'], $data['alamat_email'], 
                $data['golongan_darah'], $data['komisi'], $data['segmen'], $data['nama_ayah'], $data['nama_ibu'], 
                $data['status_baptis'], $data['tanggal_baptis'], $data['pelaksana_baptis'], 
                $data['status_pernikahan'], $data['tanggal_pernikahan'], $data['pelaksana_pernikahan'], 
                $data['nama_istri'], $data['nama_suami'], $data['status_kematian'], $data['tanggal_kematian'], 
                $image, $data['qrcode']
            ]);

            header("Location: jemaat_list.php");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Gagal menyimpan data: " . $e->getMessage();
        }
    }
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Jemaat</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
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
</head>
<body>
    <div class="container mt-5">
        <div class="form-container">
            <h2 class="form-header">Tambah Data Jemaat</h2>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nomor_anggota" class="form-label">Nomor Anggota</label>
                        <input type="text" class="form-control" id="nomor_anggota" name="nomor_anggota" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="nama" name="nama_anggota" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                        <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                            <option value="">--Pilih--</option>
                            <option value="Pria">Pria</option>
                            <option value="Wanita">Wanita</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                        <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required>
                    </div>
                </div>

                <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="kota_kelahiran" class="form-label">Tempat Lahir</label>
                    <input type="text" class="form-control" id="kota_kelahiran" name="kota_kelahiran" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="segmen" class="form-label">Segmen</label>
                        <select class="form-select" id="segmen" name="segmen" required>
                            <option value="">--Pilih--</option>
                            <option value="Anak-Anak">Anak-Anak</option>
                            <option value="Remaja">Remaja</option>
                            <option value="Dewasa">Dewasa</option>
                            <option value="Lansia">Lansia</option>
                        </select>
                    </div>

                
                <div class="col-md-6 mb-3">
                    <label for="komisi" class="form-label">Komisi</label>
                    <select class="form-select" id="komisi" name="komisi" required>
                        <option value="">--Pilih--</option>
                        <option value="Sekolah Minggu">Sekolah Minggu</option>
                        <option value="Teens">Teens</option>
                        <option value="Youth">Youth</option>
                        <option value="Umum">Umum</option>
                        <option value="AWC">AWC (Agape Womens Community)</option>
                        <option value="AMC">AMC (Agape Mens Community)</option>
                        <option value="ACC">ACC (Agape Couple Community)</option>
                    </select>
                </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="golongan_darah" class="form-label">Golongan Darah</label>
                    <select class="form-select" id="golongan_darah" name="golongan_darah">
                        <option value="">Pilih</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="AB">AB</option>
                        <option value="O">O</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <input type="text" class="form-control" id="alamat" name="alamat" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nomor_telepon" class="form-label">Telepon</label>
                    <input type="text" class="form-control" id="telepon" name="nomor_telepon" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="alamat_email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="alamat_email">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nama_ayah" class="form-label">Nama Ayah</label>
                    <input type="text" class="form-control" id="nama_ayah" name="nama_ayah" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="nama_ibu" class="form-label">Nama Ibu</label>
                    <input type="text" class="form-control" id="nama_ibu" name="nama_ibu" required>
                </div>
            </div>


                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status_pernikahan" class="form-label">Status Pernikahan</label>
                        <select class="form-select" id="status_pernikahan" name="status_pernikahan">
                            <option value="">--Pilih--</option>
                            <option value="Menikah">Menikah</option>
                            <option value="Belum Menikah">Belum Menikah</option>
                        </select>
                    </div>
                </div>

                <div class="row hidden" id="marriage_details">
                    <div class="col-md-6 mb-3">
                        <label for="nama_suami" class="form-label">Nama Suami</label>
                        <input type="text" class="form-control" id="nama_suami" name="nama_suami">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nama_istri" class="form-label">Nama Istri</label>
                        <input type="text" class="form-control" id="nama_istri" name="nama_istri">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="pelaksana_pemberkatan" class="form-label">Pelaksana Pemberkatan</label>
                        <input type="text" class="form-control" id="pelaksana_pemberkatan" name="pelaksana_pemberkatan">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_pernikahan" class="form-label">Tanggal Pernikahan</label>
                        <input type="date" class="form-control" id="tanggal_pernikahan" name="tanggal_pernikahan">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status_baptis" class="form-label">Status Baptis</label>
                        <select class="form-select" id="status_baptis" name="status_baptis">
                            <option value="">--Pilih--</option>
                            <option value="Baptis">Sudah Baptis</option>
                            <option value="Belum Baptis">Belum Baptis</option>
                        </select>
                    </div>
                </div>

                <div class="row hidden" id="baptis_details">
                    <div class="col-md-6 mb-3">
                        <label for="pelaksana_baptis" class="form-label">Pelaksana Baptis</label>
                        <input type="text" class="form-control" id="pelaksana_baptis" name="pelaksana_baptis">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_baptis" class="form-label">Tanggal Baptis</label>
                        <input type="date" class="form-control" id="tanggal_baptis" name="tanggal_baptis">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status_kematian" class="form-label">Status Kematian</label>
                        <select class="form-select" id="status_kematian" name="status_kematian">
                            <option value="">--Pilih--</option>
                            <option value="Hidup">Hidup</option>
                            <option value="Meninggal">Meninggal</option>
                        </select>
                    </div>
                </div>

                <div class="row hidden" id="death_details">
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_meninggal" class="form-label">Tanggal Meninggal</label>
                        <input type="date" class="form-control" id="tanggal_meninggal" name="tanggal_meninggal">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="file_image" class="form-label">File Image</label>
                        <input type="file" class="form-control" id="file_image" name="image" accept="image/*" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="image" class="form-label">Image Name</label>
                        <input type="text" class="form-control" id="image" name="image" accept="image/*" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('status_pernikahan').addEventListener('change', function () {
            const details = document.getElementById('marriage_details');
            details.classList.toggle('hidden', this.value !== 'Menikah');
        });

        document.getElementById('status_baptis').addEventListener('change', function () {
            const details = document.getElementById('baptis_details');
            details.classList.toggle('hidden', this.value !== 'Baptis');
        });

        document.getElementById('status_kematian').addEventListener('change', function () {
            const details = document.getElementById('death_details');
            details.classList.toggle('hidden', this.value !== 'Meninggal');
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

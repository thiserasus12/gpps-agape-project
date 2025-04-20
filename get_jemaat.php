<?php
include '../includes/db.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=data_jemaat_gpps_agape.xls");

$sql = "SELECT * FROM jemaat";
$stmt = $conn->prepare($sql);
$stmt->execute();
$dataJemaat = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1'>";
echo "<tr>
  <th>No</th>
  <th>Nomor Anggota</th>
  <th>Nama</th>
  <th>Jenis Kelamin</th>
  <th>Tanggal Lahir</th>
  <th>Kota Kelahiran</th>
  <th>Alamat</th>
  <th>Telepon</th>
  <th>Email</th>
  <th>Gol. Darah</th>
  <th>Komisi</th>
  <th>Nama Ayah</th>
  <th>Nama Ibu</th>
  <th>Status Baptis</th>
  <th>Tanggal Baptis</th>
  <th>Pelaksana Baptis</th>
  <th>Status Nikah</th>
  <th>Tanggal Nikah</th>
  <th>Pelaksana Nikah</th>
  <th>Nama Istri</th>
  <th>Nama Suami</th>
  <th>Status Kematian</th>
</tr>";

$no = 1;
foreach ($dataJemaat as $row) {
    echo "<tr>";
    echo "<td>{$no}</td>";
    echo "<td>{$row['nomor_anggota']}</td>";
    echo "<td>{$row['nama_anggota']}</td>";
    echo "<td>{$row['jenis_kelamin']}</td>";
    echo "<td>{$row['tanggal_lahir']}</td>";
    echo "<td>{$row['kota_kelahiran']}</td>";
    echo "<td>{$row['alamat']}</td>";
    echo "<td>{$row['nomor_telepon']}</td>";
    echo "<td>{$row['alamat_email']}</td>";
    echo "<td>{$row['golongan_darah']}</td>";
    echo "<td>{$row['komisi']}</td>";
    echo "<td>{$row['nama_ayah']}</td>";
    echo "<td>{$row['nama_ibu']}</td>";
    echo "<td>{$row['status_baptis']}</td>";
    echo "<td>{$row['tanggal_baptis']}</td>";
    echo "<td>{$row['pelaksana_baptis']}</td>";
    echo "<td>{$row['status_pernikahan']}</td>";
    echo "<td>{$row['tanggal_pernikahan']}</td>";
    echo "<td>{$row['pelaksana_pernikahan']}</td>";
    echo "<td>{$row['nama_istri']}</td>";
    echo "<td>{$row['nama_suami']}</td>";
    echo "<td>{$row['status_kematian']}</td>";
    echo "</tr>";
    $no++;
}

echo "</table>";
?>

<form method="post" action="export_jemaat_excel.php">
  <button type="submit">Download Excel</button>
</form>

<?php include '../includes/db.php'; ?>
<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daftar User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Daftar User</h2>
        <a href="user_add.php" class="btn btn-primary mb-3">Tambah User</a>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $stmt = $conn->query("SELECT * FROM user");
                    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($users) {
                        foreach ($users as $row) {
                            echo "<tr>
                                <td>{$row['id_user']}</td>
                                <td>{$row['username']}</td>
                                <td>{$row['nama_lengkap']}</td>
                                <td>{$row['email']}</td>
                                <td>
                                    <a href='edit.php?id={$row['id_user']}' class='btn btn-sm btn-warning'>Edit</a>
<a href='user_delete.php?user_id={$row['id_user']}' onclick=\"return confirm('Yakin hapus?')\" class='btn btn-sm btn-danger'>Hapus</a>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>Data tidak ditemukan</td></tr>";
                    }
                } catch (PDOException $e) {
                    echo "<tr><td colspan='5'>Terjadi kesalahan: " . $e->getMessage() . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>


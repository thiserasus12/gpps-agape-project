<?php
include '../includes/db.php';
include 'header.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $nama_lengkap = $_POST['nama_lengkap'] ??'';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($email) && !empty($password) && !empty($nama_lengkap) ){
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // hash password

            $stmt = $conn->prepare("INSERT INTO user (username, email, nama_lengkap, password) VALUES (:username, :email, :nama_lengkap, :password)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':nama_lengkap', $nama_lengkap);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->execute();

            header("Location: user_list.php");
            exit;
        } catch (PDOException $e) {
            echo "Gagal menambahkan user: " . $e->getMessage();
        }
    } else {
        echo "Semua field wajib diisi!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah User</title>
</head>
<body>
    <h2>Tambah User Baru</h2>
    <form method="post" action="">
        <label>Username</label><br>
        <input type="text" name="username" required><br><br>

        <label>Nama:</label><br>
        <input type="text" name="nama_lengkap" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <input type="submit" value="Simpan">
        <a href="user_list.php">Batal</a>
    </form>
</body>
</html>

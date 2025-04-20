<?php
require '../includes/db.php';

// Cek apakah user_id dikirim dan merupakan angka
if (!isset($_GET['user_id'])) {
    echo "❌ Parameter user_id tidak ditemukan.";
    exit();
}

if (!is_numeric($_GET['user_id'])) {
    echo "❌ ID user harus berupa angka.";
    exit();
}

$user_id = (int)$_GET['user_id']; // konversi ke integer untuk keamanan

try {
    // Cek apakah user dengan ID tersebut ada terlebih dahulu
    $checkStmt = $conn->prepare("SELECT * FROM user WHERE id_user = :user_id");
    $checkStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $checkStmt->execute();

    if ($checkStmt->rowCount() === 0) {
        echo "⚠️ User dengan ID $user_id tidak ditemukan di database.";
        exit();
    }

    // Jika ada, hapus user
    $stmt = $conn->prepare("DELETE FROM user WHERE id_user = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // Redirect kembali ke halaman daftar user
    header("Location: user_list.php");
    exit();

} catch (PDOException $e) {
    echo "❌ Gagal menghapus user: " . $e->getMessage();
}

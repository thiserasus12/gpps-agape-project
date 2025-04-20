<?php
include '../includes/db.php';

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $stmt = $conn->prepare("DELETE FROM jemaat WHERE id = ?");
        $stmt->execute([$id]);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

header("Location: jemaat_list.php");
exit;
?>

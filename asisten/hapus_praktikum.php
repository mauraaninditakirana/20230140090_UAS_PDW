<?php
require_once '../config.php';
// Pastikan sesi dimulai dan pengguna adalah asisten
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    header("Location: ../login.php"); 
    exit();
}

$id = $_GET['id'] ?? 0;

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM mata_praktikum WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: mata_praktikum.php?status=sukses");
        exit();
    } else {
        // Handle error, misalnya redirect dengan status error
        header("Location: mata_praktikum.php?status=gagal");
        exit();
    }
    $stmt->close();
} else {
    // Redirect jika ID tidak valid
    header("Location: mata_praktikum.php");
    exit();
}

$conn->close();
?>
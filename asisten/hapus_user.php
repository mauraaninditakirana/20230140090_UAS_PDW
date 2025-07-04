<?php
require_once '../config.php';
session_start();

// Keamanan: Pastikan pengguna adalah asisten yang login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    header("Location: ../login.php"); 
    exit();
}

$id_to_delete = $_GET['id'] ?? 0;

// Keamanan Tambahan: Cegah asisten menghapus akunnya sendiri
if ($id_to_delete == $_SESSION['user_id']) {
    header("Location: users.php?status=gagal_hapus_diri");
    exit();
}

if ($id_to_delete > 0) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id_to_delete);
    
    if ($stmt->execute()) {
        header("Location: users.php?status=sukses");
    } else {
        header("Location: users.php?status=gagal");
    }
    $stmt->close();
} else {
    header("Location: users.php");
}

$conn->close();
exit();
?>

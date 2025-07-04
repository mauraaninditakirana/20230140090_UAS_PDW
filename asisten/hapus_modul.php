<?php
require_once '../config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    header("Location: ../login.php"); 
    exit();
}

$id_modul = $_GET['id'] ?? 0;

if ($id_modul > 0) {
    $stmt_select = $conn->prepare("SELECT praktikum_id, file_materi FROM modul WHERE id = ?");
    $stmt_select->bind_param("i", $id_modul);
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    
    if ($result->num_rows === 1) {
        $modul = $result->fetch_assoc();
        $id_praktikum = $modul['praktikum_id'];
        $file_name = $modul['file_materi'];

        // PERUBAHAN: Path untuk hapus file
        $file_path = "../uploads/materi_modul/" . $file_name;
        if (!empty($file_name) && file_exists($file_path)) {
            unlink($file_path);
        }

        $stmt_delete = $conn->prepare("DELETE FROM modul WHERE id = ?");
        $stmt_delete->bind_param("i", $id_modul);
        
        if ($stmt_delete->execute()) {
            header("Location: detail_modul.php?id_praktikum=" . $id_praktikum . "&status=sukses");
            exit();
        }
    }
}

header("Location: modul.php?status=gagal");
exit();
?>

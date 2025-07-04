<?php
// Selalu mulai sesi di awal
session_start();

// Panggil file konfigurasi untuk koneksi database
require_once '../config.php';

// Keamanan: Pastikan pengguna sudah login dan perannya adalah mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

// Ambil ID praktikum dari URL dan ID mahasiswa dari sesi
$praktikum_id = $_GET['praktikum_id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Pastikan ID praktikum yang diterima valid
if ($praktikum_id > 0) {
    // Siapkan perintah DELETE untuk menghapus pendaftaran spesifik
    $stmt = $conn->prepare("DELETE FROM pendaftaran_praktikum WHERE user_id = ? AND praktikum_id = ?");
    $stmt->bind_param("ii", $user_id, $praktikum_id);
    
    if ($stmt->execute()) {
        // Jika berhasil, arahkan kembali ke "Praktikum Saya" dengan pesan sukses
        header("Location: my_courses.php?status=sukses_batal");
    } else {
        // Jika gagal, arahkan kembali dengan pesan error (opsional)
        header("Location: my_courses.php?status=gagal_batal");
    }
    $stmt->close();
} else {
    // Jika ID tidak valid, arahkan kembali ke halaman utama
    header("Location: my_courses.php");
}

$conn->close();
exit();
?>

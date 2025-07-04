<?php
// Selalu mulai sesi di awal
session_start();

// Panggil file konfigurasi untuk koneksi database
require_once '../config.php';

// Keamanan: Pastikan pengguna sudah login dan perannya adalah mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    // Jika tidak, alihkan ke halaman login
    header("Location: ../login.php");
    exit();
}

// Ambil ID praktikum dari URL dan ID mahasiswa dari sesi
$praktikum_id = $_GET['praktikum_id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Pastikan ID praktikum yang diterima adalah angka yang valid
if ($praktikum_id > 0) {
    
    // LANGKAH 1: Cek apakah mahasiswa sudah terdaftar pada praktikum ini sebelumnya
    $stmt_check = $conn->prepare("SELECT id FROM pendaftaran_praktikum WHERE user_id = ? AND praktikum_id = ?");
    $stmt_check->bind_param("ii", $user_id, $praktikum_id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        // JIKA SUDAH TERDAFTAR: Arahkan ke halaman "Praktikum Saya" dengan pesan informasi
        $stmt_check->close();
        header("Location: my_courses.php?status=sudah_terdaftar");
        exit();
    }
    
    $stmt_check->close();

    // LANGKAH 2: Jika belum terdaftar, lakukan proses pendaftaran
    $stmt_insert = $conn->prepare("INSERT INTO pendaftaran_praktikum (user_id, praktikum_id) VALUES (?, ?)");
    $stmt_insert->bind_param("ii", $user_id, $praktikum_id);
    
    if ($stmt_insert->execute()) {
        // JIKA PENDAFTARAN BERHASIL: Arahkan ke "Praktikum Saya" dengan pesan sukses
        header("Location: my_courses.php?status=sukses_daftar");
    } else {
        // JIKA GAGAL (misal: ada error database): Arahkan kembali ke katalog dengan pesan gagal
        header("Location: courses.php?status=gagal_daftar");
    }
    $stmt_insert->close();

} else {
    // Jika ID praktikum tidak valid atau tidak ada, arahkan kembali ke halaman katalog
    header("Location: courses.php");
}

// Tutup koneksi database dan hentikan skrip
$conn->close();
exit();
?>

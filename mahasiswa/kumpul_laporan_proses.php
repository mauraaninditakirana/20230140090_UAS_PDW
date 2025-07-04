<?php
session_start();
require_once '../config.php';

// Keamanan
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $modul_id = $_POST['modul_id'] ?? 0;
    $praktikum_id = $_POST['praktikum_id'] ?? 0; // Untuk redirect kembali

    // Validasi dasar
    if ($modul_id == 0 || $praktikum_id == 0) {
        header("Location: my_courses.php?status=gagal_kumpul");
        exit();
    }

    // Proses upload file
    if (isset($_FILES['file_laporan']) && $_FILES['file_laporan']['error'] == 0) {
        $target_dir = "../uploads/laporan_mahasiswa/";
        $file_extension = strtolower(pathinfo($_FILES["file_laporan"]["name"], PATHINFO_EXTENSION));
        // Buat nama file yang unik: user_id - modul_id - random_hash
        $file_name = "user" . $user_id . "_modul" . $modul_id . "_" . bin2hex(random_bytes(4)) . '.' . $file_extension;
        $target_file = $target_dir . $file_name;

        $allowed_types = ['pdf', 'doc', 'docx', 'zip', 'rar'];
        if (in_array($file_extension, $allowed_types)) {
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }

            if (move_uploaded_file($_FILES["file_laporan"]["tmp_name"], $target_file)) {
                // File berhasil diunggah, simpan ke database
                // Menggunakan INSERT ... ON DUPLICATE KEY UPDATE untuk handle pengumpulan ulang
                $sql = "INSERT INTO pengumpulan_laporan (modul_id, user_id, file_laporan, status) 
                        VALUES (?, ?, ?, 'dikumpulkan')
                        ON DUPLICATE KEY UPDATE file_laporan = VALUES(file_laporan), tanggal_kumpul = NOW(), status = 'dikumpulkan', nilai = NULL, feedback = NULL";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iis", $modul_id, $user_id, $file_name);

                if ($stmt->execute()) {
                    header("Location: detail_praktikum.php?praktikum_id=" . $praktikum_id . "&status=sukses_kumpul");
                } else {
                    header("Location: detail_praktikum.php?praktikum_id=" . $praktikum_id . "&status=gagal_kumpul");
                }
                $stmt->close();
            } else {
                // Gagal memindahkan file
                header("Location: detail_praktikum.php?praktikum_id=" . $praktikum_id . "&status=gagal_kumpul");
            }
        } else {
            // Format file tidak diizinkan
            header("Location: detail_praktikum.php?praktikum_id=" . $praktikum_id . "&status=gagal_kumpul");
        }
    } else {
        // Tidak ada file yang diunggah atau error
        header("Location: detail_praktikum.php?praktikum_id=" . $praktikum_id . "&status=gagal_kumpul");
    }
} else {
    header("Location: my_courses.php");
}

$conn->close();
exit();
?>

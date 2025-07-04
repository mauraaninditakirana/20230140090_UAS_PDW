<?php
// Selalu mulai sesi di awal untuk mengakses data login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = 'Dashboard';
$activePage = 'dashboard';

// Panggil file konfigurasi dan header
require_once '../config.php';
require_once 'templates/header_mahasiswa.php'; 

// Keamanan: Pastikan pengguna adalah mahasiswa yang login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- LOGIKA PENGAMBILAN DATA DINAMIS ---

// 1. Hitung jumlah Praktikum yang Diikuti
$stmt_praktikum = $conn->prepare("SELECT COUNT(id) AS total FROM pendaftaran_praktikum WHERE user_id = ?");
$stmt_praktikum->bind_param("i", $user_id);
$stmt_praktikum->execute();
$praktikum_diikuti = $stmt_praktikum->get_result()->fetch_assoc()['total'];
$stmt_praktikum->close();

// 2. Hitung jumlah Tugas yang Selesai (sudah dinilai)
$stmt_selesai = $conn->prepare("SELECT COUNT(id) AS total FROM pengumpulan_laporan WHERE user_id = ? AND status = 'dinilai'");
$stmt_selesai->bind_param("i", $user_id);
$stmt_selesai->execute();
$tugas_selesai = $stmt_selesai->get_result()->fetch_assoc()['total'];
$stmt_selesai->close();

// 3. Hitung jumlah Tugas yang Menunggu
// Pertama, hitung total modul dari semua praktikum yang diikuti
$sql_total_modul = "SELECT COUNT(m.id) AS total 
                    FROM modul m
                    JOIN pendaftaran_praktikum pp ON m.praktikum_id = pp.praktikum_id
                    WHERE pp.user_id = ?";
$stmt_total_modul = $conn->prepare($sql_total_modul);
$stmt_total_modul->bind_param("i", $user_id);
$stmt_total_modul->execute();
$total_modul_mahasiswa = $stmt_total_modul->get_result()->fetch_assoc()['total'];
$stmt_total_modul->close();

// Kedua, hitung total laporan yang sudah dikumpulkan (baik dinilai maupun belum)
$stmt_dikumpulkan = $conn->prepare("SELECT COUNT(id) AS total FROM pengumpulan_laporan WHERE user_id = ?");
$stmt_dikumpulkan->bind_param("i", $user_id);
$stmt_dikumpulkan->execute();
$total_laporan_dikumpulkan = $stmt_dikumpulkan->get_result()->fetch_assoc()['total'];
$stmt_dikumpulkan->close();

// Tugas menunggu adalah selisihnya
$tugas_menunggu = $total_modul_mahasiswa - $total_laporan_dikumpulkan;
// Pastikan tidak ada angka negatif
if ($tugas_menunggu < 0) {
    $tugas_menunggu = 0;
}

// --- AKHIR LOGIKA ---
?>


<div class="bg-gradient-to-r from-blue-500 to-cyan-400 text-white p-8 rounded-xl shadow-lg mb-8">
    <h1 class="text-3xl font-bold">Selamat Datang Kembali, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</h1>
    <p class="mt-2 opacity-90">Terus semangat dalam menyelesaikan semua modul praktikummu.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    
    <!-- Kartu Praktikum Diikuti -->
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-blue-600"><?php echo $praktikum_diikuti; ?></div>
        <div class="mt-2 text-lg text-gray-600">Praktikum Diikuti</div>
    </div>
    
    <!-- Kartu Tugas Selesai -->
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-green-500"><?php echo $tugas_selesai; ?></div>
        <div class="mt-2 text-lg text-gray-600">Tugas Selesai</div>
    </div>
    
    <!-- Kartu Tugas Menunggu -->
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-yellow-500"><?php echo $tugas_menunggu; ?></div>
        <div class="mt-2 text-lg text-gray-600">Tugas Menunggu</div>
    </div>
    
</div>

<!-- Bagian Notifikasi (saat ini masih statis, bisa dikembangkan lebih lanjut) -->
<div class="bg-white p-6 rounded-xl shadow-md">
    <h3 class="text-2xl font-bold text-gray-800 mb-4">Notifikasi Terbaru</h3>
    <ul class="space-y-4">
        
        <li class="flex items-start p-3 border-b border-gray-100 last:border-b-0">
            <span class="text-xl mr-4">🔔</span>
            <div>
                Nilai untuk <a href="#" class="font-semibold text-blue-600 hover:underline">Modul 1: HTML & CSS</a> telah diberikan.
            </div>
        </li>

        <li class="flex items-start p-3 border-b border-gray-100 last:border-b-0">
            <span class="text-xl mr-4">⏳</span>
            <div>
                Batas waktu pengumpulan laporan untuk <a href="#" class="font-semibold text-blue-600 hover:underline">Modul 2: PHP Native</a> adalah besok!
            </div>
        </li>

        <li class="flex items-start p-3">
            <span class="text-xl mr-4">✅</span>
            <div>
                Anda berhasil mendaftar pada praktikum <a href="#" class="font-semibold text-blue-600 hover:underline">Jaringan Komputer</a>.
            </div>
        </li>
    </ul>
</div>


<?php
$conn->close();
require_once 'templates/footer_mahasiswa.php';
?>

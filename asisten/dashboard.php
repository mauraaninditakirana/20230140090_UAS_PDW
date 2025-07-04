<?php
// 1. Definisi Variabel untuk Template
$pageTitle = 'Dashboard';
$activePage = 'dashboard';

// 2. Panggil Header dan Konfigurasi
require_once '../config.php';
require_once 'templates/header.php'; 

// 3. Logika untuk mengambil data dinamis
// =======================================

// Hitung Total Modul
$result_total_modul = $conn->query("SELECT COUNT(id) AS total FROM modul");
$total_modul = $result_total_modul->fetch_assoc()['total'];

// Hitung Total Laporan Masuk
$result_total_laporan = $conn->query("SELECT COUNT(id) AS total FROM pengumpulan_laporan");
$total_laporan_masuk = $result_total_laporan->fetch_assoc()['total'];

// Hitung Laporan Belum Dinilai
$result_laporan_belum_dinilai = $conn->query("SELECT COUNT(id) AS total FROM pengumpulan_laporan WHERE status = 'dikumpulkan'");
$laporan_belum_dinilai = $result_laporan_belum_dinilai->fetch_assoc()['total'];

// Ambil 5 Aktivitas Laporan Terbaru
$sql_aktivitas = "SELECT 
                    pl.tanggal_kumpul,
                    u.nama AS nama_mahasiswa,
                    m.judul_modul
                  FROM 
                    pengumpulan_laporan pl
                  JOIN 
                    users u ON pl.user_id = u.id
                  JOIN 
                    modul m ON pl.modul_id = m.id
                  ORDER BY 
                    pl.tanggal_kumpul DESC
                  LIMIT 5";
$aktivitas_terbaru = $conn->query($sql_aktivitas);

// =======================================
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    
    <!-- Kartu Total Modul -->
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-blue-100 p-3 rounded-full">
            <svg class="w-6 h-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Total Modul Diajarkan</p>
            <p class="text-2xl font-bold text-gray-800"><?php echo $total_modul; ?></p>
        </div>
    </div>

    <!-- Kartu Total Laporan Masuk -->
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-green-100 p-3 rounded-full">
            <svg class="w-6 h-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Total Laporan Masuk</p>
            <p class="text-2xl font-bold text-gray-800"><?php echo $total_laporan_masuk; ?></p>
        </div>
    </div>

    <!-- Kartu Laporan Belum Dinilai -->
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-yellow-100 p-3 rounded-full">
            <svg class="w-6 h-6 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Laporan Belum Dinilai</p>
            <p class="text-2xl font-bold text-gray-800"><?php echo $laporan_belum_dinilai; ?></p>
        </div>
    </div>
</div>

<!-- Kartu Aktivitas Terbaru -->
<div class="bg-white p-6 rounded-lg shadow-md mt-8">
    <h3 class="text-xl font-bold text-gray-800 mb-4">Aktivitas Laporan Terbaru</h3>
    <div class="space-y-4">
        <?php if ($aktivitas_terbaru->num_rows > 0): ?>
            <?php while($aktivitas = $aktivitas_terbaru->fetch_assoc()): ?>
            <div class="flex items-center border-b border-gray-100 pb-3 last:border-b-0">
                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center mr-4">
                    <!-- Ambil 2 huruf pertama dari nama untuk inisial -->
                    <span class="font-bold text-gray-500"><?php echo strtoupper(substr($aktivitas['nama_mahasiswa'], 0, 2)); ?></span>
                </div>
                <div>
                    <p class="text-gray-800">
                        <strong><?php echo htmlspecialchars($aktivitas['nama_mahasiswa']); ?></strong> 
                        mengumpulkan laporan untuk 
                        <strong><?php echo htmlspecialchars($aktivitas['judul_modul']); ?></strong>
                    </p>
                    <p class="text-sm text-gray-500"><?php echo date("d M Y, H:i", strtotime($aktivitas['tanggal_kumpul'])); ?></p>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-gray-500">Belum ada aktivitas laporan.</p>
        <?php endif; ?>
    </div>
</div>


<?php
// 4. Panggil Footer
$conn->close();
require_once 'templates/footer.php';
?>

<?php
// BAGIAN 1: PERSIAPAN LOGIKA DAN PENGAMBILAN DATA
// =======================================================
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$courses = [];

$sql = "SELECT mp.id, mp.nama_praktikum, mp.deskripsi, pp.tanggal_daftar 
        FROM pendaftaran_praktikum pp
        JOIN mata_praktikum mp ON pp.praktikum_id = mp.id
        WHERE pp.user_id = ?";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
    $stmt->close();
}
$conn->close();

// BAGIAN 2: PENAMPILAN HALAMAN (HTML)
// =======================================================
$pageTitle = 'Praktikum Saya';
$activePage = 'my_courses';
require_once 'templates/header_mahasiswa.php';
?>

<!-- Tampilkan notifikasi -->
<?php if (isset($_GET['status'])): ?>
    <?php if ($_GET['status'] == 'sukses_daftar'): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
            <strong class="font-bold">Pendaftaran Berhasil!</strong>
        </div>
    <?php elseif ($_GET['status'] == 'sudah_terdaftar'): ?>
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
            <strong class="font-bold">Informasi:</strong> Anda sudah terdaftar sebelumnya.
        </div>
    <?php elseif ($_GET['status'] == 'sukses_batal'): ?>
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
            <strong class="font-bold">Berhasil!</strong> Pendaftaran praktikum telah dibatalkan.
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Konten utama dengan tata letak kartu -->
<div class="container mx-auto p-4">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Praktikum yang Saya Ikuti</h2>
    
    <?php if (count($courses) > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach($courses as $course): ?>
                <!-- Awal dari satu kartu praktikum -->
                <div class="bg-white p-6 rounded-lg shadow-md flex flex-col justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($course['nama_praktikum']); ?></h3>
                        <p class="text-gray-600 text-sm mb-4 h-16 overflow-hidden"><?php echo htmlspecialchars($course['deskripsi']); ?></p>
                        <p class="text-xs text-gray-500 mb-4">
                            Terdaftar pada: <?php echo date("d M Y", strtotime($course['tanggal_daftar'])); ?>
                        </p>
                    </div>
                    <div class="mt-4 flex items-center space-x-2">
                        <!-- PERUBAHAN: Tombol Lihat Detail sekarang mengarah ke detail_praktikum.php -->
                        <a href="detail_praktikum.php?praktikum_id=<?php echo $course['id']; ?>" class="flex-1 text-center bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-3 rounded-lg text-sm transition-colors duration-300">
                            Lihat Detail
                        </a>
                        <!-- Tombol Batalkan -->
                        <a href="batal_praktikum.php?praktikum_id=<?php echo $course['id']; ?>" 
                           class="flex-1 text-center bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-3 rounded-lg text-sm transition-colors duration-300"
                           onclick="return confirm('Apakah Anda yakin ingin membatalkan pendaftaran untuk praktikum ini?');">
                            Batalkan
                        </a>
                    </div>
                </div>
                <!-- Akhir dari satu kartu praktikum -->
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-10 bg-white rounded-lg shadow-md">
            <p class="text-gray-500">Anda belum terdaftar pada mata praktikum manapun.</p>
            <a href="courses.php" class="mt-4 inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">
                Cari Praktikum Sekarang
            </a>
        </div>
    <?php endif; ?>
</div>

<?php
require_once 'templates/footer_mahasiswa.php';
?>

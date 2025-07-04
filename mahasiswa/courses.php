<?php
// Selalu mulai sesi di awal untuk mengakses data login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = 'Cari Praktikum';
$activePage = 'courses';

require_once '../config.php';
// Halaman ini untuk mahasiswa yang sudah login, jadi kita gunakan header yang aman
require_once 'templates/header_mahasiswa.php';

// --- LOGIKA BARU DIMULAI DI SINI ---

// 1. Ambil daftar ID praktikum yang sudah diikuti oleh mahasiswa
$registered_courses_ids = [];
$user_id = $_SESSION['user_id'];

$sql_registered = "SELECT praktikum_id FROM pendaftaran_praktikum WHERE user_id = ?";
if ($stmt_registered = $conn->prepare($sql_registered)) {
    $stmt_registered->bind_param("i", $user_id);
    $stmt_registered->execute();
    $result_registered = $stmt_registered->get_result();
    
    // Masukkan semua ID praktikum yang diikuti ke dalam sebuah array
    while ($row_registered = $result_registered->fetch_assoc()) {
        $registered_courses_ids[] = $row_registered['praktikum_id'];
    }
    $stmt_registered->close();
}

// --- LOGIKA BARU SELESAI ---

// Ambil semua data mata praktikum yang tersedia di sistem
$sql_all_courses = "SELECT id, nama_praktikum, deskripsi FROM mata_praktikum ORDER BY nama_praktikum ASC";
$result_all_courses = $conn->query($sql_all_courses);

?>

<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4">Daftar Mata Praktikum Tersedia</h2>

    <!-- Notifikasi jika pendaftaran gagal (opsional, untuk penanganan error) -->
    <?php if (isset($_GET['status']) && $_GET['status'] == 'gagal_daftar'): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
            <strong class="font-bold">Pendaftaran Gagal!</strong>
            <span class="block sm:inline"> Terjadi kesalahan saat mencoba mendaftar. Silakan coba lagi.</span>
        </div>
    <?php endif; ?>

    <?php if ($result_all_courses->num_rows > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while($row = $result_all_courses->fetch_assoc()): ?>
                <div class="bg-white p-6 rounded-lg shadow-md flex flex-col justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($row['nama_praktikum']); ?></h3>
                        <p class="text-gray-600 text-sm mb-4 h-20 overflow-hidden"><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                    </div>
                    
                    <!-- LOGIKA TOMBOL BARU -->
                    <div class="mt-4">
                        <?php if (in_array($row['id'], $registered_courses_ids)): ?>
                            <!-- Jika ID praktikum ini ada di dalam array yang sudah diikuti -->
                            <button disabled class="w-full bg-green-500 text-white font-bold py-2 px-4 rounded text-sm opacity-70 cursor-not-allowed">
                                ✓ Sudah Terdaftar
                            </button>
                        <?php else: ?>
                            <!-- Jika belum diikuti, tampilkan tombol daftar yang aktif -->
                            <a href="daftar_praktikum.php?praktikum_id=<?php echo $row['id']; ?>" class="w-full text-center block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm transition-colors duration-200">
                                Daftar Praktikum
                            </a>
                        <?php endif; ?>
                    </div>

                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="mt-4 text-gray-700">Belum ada mata praktikum yang tersedia saat ini.</p>
    <?php endif; ?>
</div>

<?php
require_once 'templates/footer_mahasiswa.php';
$conn->close();
?>
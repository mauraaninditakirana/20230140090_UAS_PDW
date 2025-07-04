<?php
session_start();
require_once '../config.php';

// Keamanan
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$praktikum_id = $_GET['praktikum_id'] ?? 0;

if ($praktikum_id == 0) {
    header("Location: my_courses.php");
    exit();
}

// Ambil detail praktikum
$stmt_praktikum = $conn->prepare("SELECT nama_praktikum FROM mata_praktikum WHERE id = ?");
$stmt_praktikum->bind_param("i", $praktikum_id);
$stmt_praktikum->execute();
$praktikum = $stmt_praktikum->get_result()->fetch_assoc();
$stmt_praktikum->close();

// Ambil semua modul untuk praktikum ini
$stmt_modul = $conn->prepare("SELECT * FROM modul WHERE praktikum_id = ? ORDER BY urutan ASC, created_at ASC");
$stmt_modul->bind_param("i", $praktikum_id);
$stmt_modul->execute();
$modules = $stmt_modul->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_modul->close();

// Ambil semua data pengumpulan laporan oleh mahasiswa ini untuk praktikum ini
$submissions = [];
if (!empty($modules)) {
    $module_ids = array_column($modules, 'id');
    $placeholders = implode(',', array_fill(0, count($module_ids), '?'));
    $types = str_repeat('i', count($module_ids));

    $sql_submissions = "SELECT * FROM pengumpulan_laporan WHERE user_id = ? AND modul_id IN ($placeholders)";
    $stmt_submissions = $conn->prepare($sql_submissions);
    $stmt_submissions->bind_param("i" . $types, $user_id, ...$module_ids);
    $stmt_submissions->execute();
    $result_submissions = $stmt_submissions->get_result();
    while ($row = $result_submissions->fetch_assoc()) {
        $submissions[$row['modul_id']] = $row;
    }
    $stmt_submissions->close();
}

$pageTitle = 'Detail Praktikum';
$activePage = 'my_courses';
require_once 'templates/header_mahasiswa.php';
?>

<!-- Notifikasi -->
<?php if (isset($_GET['status'])): ?>
    <?php if ($_GET['status'] == 'sukses_kumpul'): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
            <strong class="font-bold">Berhasil!</strong> Laporan Anda telah berhasil dikumpulkan.
        </div>
    <?php elseif ($_GET['status'] == 'gagal_kumpul'): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
            <strong class="font-bold">Gagal!</strong> Terjadi kesalahan saat mengumpulkan laporan.
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="container mx-auto p-4">
    <a href="my_courses.php" class="text-blue-500 hover:text-blue-700 font-semibold mb-4 inline-block">&larr; Kembali ke Praktikum Saya</a>
    <h2 class="text-3xl font-bold mb-6 text-gray-800"><?php echo htmlspecialchars($praktikum['nama_praktikum']); ?></h2>

    <div class="space-y-6">
        <?php if (count($modules) > 0): ?>
            <?php foreach ($modules as $module): ?>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($module['judul_modul']); ?></h3>
                    <p class="text-gray-600 text-sm mb-4"><?php echo htmlspecialchars($module['deskripsi']); ?></p>

                    <?php if (!empty($module['file_materi'])): ?>
                        <a href="../uploads/materi_modul/<?php echo htmlspecialchars($module['file_materi']); ?>" target="_blank" class="inline-block bg-blue-100 text-blue-700 font-semibold py-2 px-4 rounded-lg text-sm mb-4 hover:bg-blue-200">
                            Unduh Materi
                        </a>
                    <?php endif; ?>

                    <hr class="my-4">

                    <!-- Bagian Pengumpulan Laporan -->
                    <div>
                        <h4 class="font-bold text-gray-700 mb-2">Status Laporan Anda:</h4>
                        <?php if (isset($submissions[$module['id']])): 
                            $submission = $submissions[$module['id']];
                        ?>
                            <!-- Jika sudah mengumpulkan -->
                            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                <p class="font-semibold text-green-800">✓ Sudah Dikumpulkan</p>
                                <p class="text-xs text-gray-500 mt-1">Pada: <?php echo date("d M Y, H:i", strtotime($submission['tanggal_kumpul'])); ?></p>
                                <a href="../uploads/laporan_mahasiswa/<?php echo htmlspecialchars($submission['file_laporan']); ?>" target="_blank" class="text-blue-600 hover:underline text-sm mt-2 inline-block">Lihat File Laporan</a>
                                
                                <?php if ($submission['status'] == 'dinilai'): ?>
                                    <div class="mt-3 pt-3 border-t">
                                        <p class="font-semibold text-gray-800">Nilai: <span class="text-2xl font-bold text-blue-600"><?php echo htmlspecialchars($submission['nilai']); ?></span></p>
                                        <?php if (!empty($submission['feedback'])): ?>
                                            <p class="font-semibold text-gray-800 mt-2">Feedback Asisten:</p>
                                            <p class="text-sm text-gray-600 bg-gray-100 p-2 rounded"><?php echo nl2br(htmlspecialchars($submission['feedback'])); ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <!-- Jika belum mengumpulkan -->
                            <div class="bg-gray-50 p-4 rounded-lg border">
                                <p class="font-semibold text-gray-800 mb-2">Belum Mengumpulkan</p>
                                <form action="kumpul_laporan_proses.php" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="modul_id" value="<?php echo $module['id']; ?>">
                                    <input type="hidden" name="praktikum_id" value="<?php echo $praktikum_id; ?>">
                                    <input type="file" name="file_laporan" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300" required>
                                    <button type="submit" class="mt-2 bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg text-sm">
                                        Kumpulkan Laporan
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-10 bg-white rounded-lg shadow-md">
                <p class="text-gray-500">Asisten belum menambahkan modul untuk praktikum ini.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$conn->close();
require_once 'templates/footer_mahasiswa.php';
?>

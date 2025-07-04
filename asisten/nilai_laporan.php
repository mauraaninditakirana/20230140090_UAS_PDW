<?php
require_once '../config.php';

$id_laporan = $_GET['id'] ?? 0;
if ($id_laporan == 0) {
    header("Location: laporan.php");
    exit();
}

// Proses form jika ada POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nilai = $_POST['nilai'];
    $feedback = trim($_POST['feedback']);

    $stmt_update = $conn->prepare("UPDATE pengumpulan_laporan SET nilai = ?, feedback = ?, status = 'dinilai' WHERE id = ?");
    $stmt_update->bind_param("isi", $nilai, $feedback, $id_laporan);
    
    if ($stmt_update->execute()) {
        header("Location: laporan.php?status=nilai_sukses");
        exit();
    } else {
        $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">Gagal menyimpan nilai.</div>';
    }
    $stmt_update->close();
}

// Ambil detail laporan untuk ditampilkan
$sql = "SELECT 
            pl.*,
            u.nama AS nama_mahasiswa,
            m.judul_modul,
            mp.nama_praktikum
        FROM 
            pengumpulan_laporan pl
        JOIN 
            users u ON pl.user_id = u.id
        JOIN 
            modul m ON pl.modul_id = m.id
        JOIN 
            mata_praktikum mp ON m.praktikum_id = mp.id
        WHERE pl.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_laporan);
$stmt->execute();
$laporan = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$laporan) {
    header("Location: laporan.php");
    exit();
}

$pageTitle = 'Beri Nilai Laporan';
$activePage = 'laporan';
require_once 'templates/header.php';
?>

<a href="laporan.php" class="text-blue-500 hover:text-blue-700 font-semibold mb-4 inline-block">&larr; Kembali ke Daftar Laporan</a>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Kolom Informasi Laporan -->
    <div class="md:col-span-1 bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Detail Laporan</h3>
        <div class="space-y-2 text-sm">
            <p><strong>Mahasiswa:</strong><br><?php echo htmlspecialchars($laporan['nama_mahasiswa']); ?></p>
            <p><strong>Praktikum:</strong><br><?php echo htmlspecialchars($laporan['nama_praktikum']); ?></p>
            <p><strong>Modul:</strong><br><?php echo htmlspecialchars($laporan['judul_modul']); ?></p>
            <p><strong>Dikumpulkan:</strong><br><?php echo date("d M Y, H:i", strtotime($laporan['tanggal_kumpul'])); ?></p>
        </div>
        <hr class="my-4">
        <a href="../uploads/laporan_mahasiswa/<?php echo htmlspecialchars($laporan['file_laporan']); ?>" target="_blank" class="w-full text-center block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">
            Unduh Laporan Mahasiswa
        </a>
    </div>

    <!-- Kolom Form Penilaian -->
    <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Form Penilaian</h3>
        <?php if (isset($message)) echo $message; ?>
        <form action="nilai_laporan.php?id=<?php echo $id_laporan; ?>" method="POST">
            <div class="mb-4">
                <label for="nilai" class="block text-gray-700 text-sm font-bold mb-2">Nilai (0-100):</label>
                <input type="number" id="nilai" name="nilai" min="0" max="100" value="<?php echo htmlspecialchars($laporan['nilai'] ?? ''); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
            </div>
            <div class="mb-6">
                <label for="feedback" class="block text-gray-700 text-sm font-bold mb-2">Feedback (Opsional):</label>
                <textarea id="feedback" name="feedback" rows="5" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"><?php echo htmlspecialchars($laporan['feedback'] ?? ''); ?></textarea>
            </div>
            <div class="flex items-center">
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Simpan Nilai
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$conn->close();
require_once 'templates/footer.php';
?>

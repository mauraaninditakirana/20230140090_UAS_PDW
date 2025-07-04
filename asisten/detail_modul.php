<?php
$pageTitle = 'Detail Modul';
$activePage = 'modul';

require_once '../config.php';
require_once 'templates/header.php';

$id_praktikum = $_GET['id_praktikum'] ?? 0;
if ($id_praktikum == 0) {
    header("Location: modul.php");
    exit();
}

$stmt_praktikum = $conn->prepare("SELECT nama_praktikum FROM mata_praktikum WHERE id = ?");
$stmt_praktikum->bind_param("i", $id_praktikum);
$stmt_praktikum->execute();
$result_praktikum = $stmt_praktikum->get_result();
$praktikum = $result_praktikum->fetch_assoc();
$nama_praktikum = $praktikum['nama_praktikum'] ?? 'Tidak Ditemukan';
$stmt_praktikum->close();

$stmt_modul = $conn->prepare("SELECT * FROM modul WHERE praktikum_id = ? ORDER BY urutan ASC, created_at ASC");
$stmt_modul->bind_param("i", $id_praktikum);
$stmt_modul->execute();
$result_modul = $stmt_modul->get_result();
?>

<div class="mb-4 flex justify-between items-center">
    <a href="modul.php" class="text-blue-500 hover:text-blue-700 font-semibold">&larr; Kembali ke Daftar Praktikum</a>
    <a href="tambah_modul.php?id_praktikum=<?php echo $id_praktikum; ?>" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">
        + Tambah Modul Baru
    </a>
</div>

<?php if (isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
    <strong class="font-bold">Berhasil!</strong> Data modul telah berhasil diproses.
</div>
<?php endif; ?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-1 text-gray-800">Modul untuk: <?php echo htmlspecialchars($nama_praktikum); ?></h2>
    <p class="text-gray-600 mb-6">Berikut adalah daftar modul yang tersedia.</p>
    
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-200">
                <tr>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Judul Modul</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">File Materi</th>
                    <th class="text-center py-3 px-4 uppercase font-semibold text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php if ($result_modul->num_rows > 0): ?>
                    <?php while($row = $result_modul->fetch_assoc()): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4">
                            <div class="font-bold"><?php echo htmlspecialchars($row['judul_modul']); ?></div>
                            <div class="text-xs text-gray-500"><?php echo htmlspecialchars($row['deskripsi']); ?></div>
                        </td>
                        <td class="py-3 px-4">
                            <?php if (!empty($row['file_materi'])): ?>
                                <!-- PERUBAHAN: Path untuk link unduh -->
                                <a href="../uploads/materi_modul/<?php echo htmlspecialchars($row['file_materi']); ?>" target="_blank" class="text-blue-500 hover:underline">
                                    Unduh/Lihat Materi
                                </a>
                            <?php else: ?>
                                <span class="text-gray-400">Tidak ada file</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <a href="edit_modul.php?id=<?php echo $row['id']; ?>" class="text-blue-500 hover:text-blue-700 font-semibold mr-4">Edit</a>
                            <a href="hapus_modul.php?id=<?php echo $row['id']; ?>" class="text-red-500 hover:text-red-700 font-semibold" onclick="return confirm('Anda yakin ingin menghapus modul ini?');">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center py-4 text-gray-500">Belum ada modul untuk mata praktikum ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$stmt_modul->close();
$conn->close();
require_once 'templates/footer.php';
?>

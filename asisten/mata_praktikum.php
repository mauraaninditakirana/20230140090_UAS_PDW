<?php
// 1. Definisi Variabel untuk Template
$pageTitle = 'Manajemen Mata Praktikum';
$activePage = 'mata_praktikum'; // Sesuaikan dengan key di header.php

// 2. Panggil Header
require_once '../config.php';
require_once 'templates/header.php';

// 3. Ambil semua data praktikum dari database
$result = $conn->query("SELECT * FROM mata_praktikum ORDER BY created_at DESC");
?>

<div class="mb-4">
    <a href="tambah_praktikum.php" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-300">
        + Tambah Mata Praktikum
    </a>
</div>

<?php if (isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
    <strong class="font-bold">Berhasil!</strong>
    <span class="block sm:inline">Data mata praktikum telah berhasil diproses.</span>
</div>
<?php endif; ?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Daftar Mata Praktikum</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-200">
                <tr>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Nama Praktikum</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Deskripsi</th>
                    <th class="text-center py-3 px-4 uppercase font-semibold text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr class="border-b">
                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['nama_praktikum']); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['deskripsi']); ?></td>
                        <td class="py-3 px-4 text-center">
                            <a href="edit_praktikum.php?id=<?php echo $row['id']; ?>" class="text-blue-500 hover:text-blue-700 font-semibold mr-4">Edit</a>
                            <a href="hapus_praktikum.php?id=<?php echo $row['id']; ?>" class="text-red-500 hover:text-red-700 font-semibold" onclick="return confirm('Apakah Anda yakin ingin menghapus mata praktikum ini?');">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center py-4">Belum ada data mata praktikum.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// 4. Panggil Footer
require_once 'templates/footer.php';
?>
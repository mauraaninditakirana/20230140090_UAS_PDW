<?php
$pageTitle = 'Manajemen Modul';
$activePage = 'modul'; 

require_once '../config.php';
require_once 'templates/header.php';

// Ambil semua data praktikum untuk ditampilkan
$result = $conn->query("SELECT * FROM mata_praktikum ORDER BY nama_praktikum ASC");
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Pilih Modul Praktikum</h2>
    <p class="text-gray-600 mb-6">Pilih salah satu mata praktikum di bawah ini untuk mengelola modul/pertemuan yang ada di dalamnya.</p>
    
    <div class="space-y-4">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
            <div class="bg-gray-50 p-4 rounded-lg flex items-center justify-between shadow-sm hover:bg-gray-100 transition-colors duration-200">
                <div>
                    <h3 class="font-bold text-lg text-gray-800"><?php echo htmlspecialchars($row['nama_praktikum']); ?></h3>
                    <p class="text-sm text-gray-500"><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                </div>
                <a href="detail_modul.php?id_praktikum=<?php echo $row['id']; ?>" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-300">
                    Kelola Modul &rarr;
                </a>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center py-4 text-gray-500">Belum ada data mata praktikum. Silakan tambahkan terlebih dahulu di menu "Manajemen Praktikum".</p>
        <?php endif; ?>
    </div>
</div>

<?php
$conn->close();
require_once 'templates/footer.php';
?>

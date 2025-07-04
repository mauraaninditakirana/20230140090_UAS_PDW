<?php
$pageTitle = 'Manajemen Pengguna';
$activePage = 'users'; // Anda mungkin perlu menambahkan 'users' ke logika menu aktif di header

require_once '../config.php';
require_once 'templates/header.php';

// Ambil semua data pengguna
$result = $conn->query("SELECT id, nama, email, role, created_at FROM users ORDER BY created_at DESC");
?>

<!-- Tombol Tambah Pengguna -->
<div class="mb-4">
    <a href="tambah_user.php" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">
        + Tambah Pengguna Baru
    </a>
</div>

<!-- Notifikasi Sukses -->
<?php if (isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
    <strong class="font-bold">Berhasil!</strong> Data pengguna telah berhasil diproses.
</div>
<?php endif; ?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Daftar Akun Pengguna</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-200">
                <tr>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Nama</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Email</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Peran</th>
                    <th class="text-center py-3 px-4 uppercase font-semibold text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['nama']); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['email']); ?></td>
                        <td class="py-3 px-4">
                            <span class="capitalize px-2 py-1 text-xs font-semibold rounded-full <?php echo ($row['role'] == 'asisten') ? 'bg-blue-200 text-blue-800' : 'bg-yellow-200 text-yellow-800'; ?>">
                                <?php echo htmlspecialchars($row['role']); ?>
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="text-blue-500 hover:text-blue-700 font-semibold mr-4">Edit</a>
                            <?php if ($_SESSION['user_id'] != $row['id']): // Cegah admin menghapus diri sendiri ?>
                            <a href="hapus_user.php?id=<?php echo $row['id']; ?>" class="text-red-500 hover:text-red-700 font-semibold" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini? Semua data terkait (laporan, pendaftaran) akan ikut terhapus.');">Hapus</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-4">Tidak ada data pengguna.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$conn->close();
require_once 'templates/footer.php';
?>

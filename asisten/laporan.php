<?php
$pageTitle = 'Laporan Masuk';
$activePage = 'laporan';

require_once '../config.php';
require_once 'templates/header.php';

// --- LOGIKA FILTER ---
$filter_praktikum_id = $_GET['praktikum_id'] ?? '';
$filter_modul_id = $_GET['modul_id'] ?? '';
$filter_status = $_GET['status'] ?? '';

// Bangun query dasar
$sql = "SELECT 
            pl.id,
            pl.tanggal_kumpul,
            pl.status,
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
            mata_praktikum mp ON m.praktikum_id = mp.id";

$where_clauses = [];
$params = [];
$types = '';

if (!empty($filter_praktikum_id)) {
    $where_clauses[] = "mp.id = ?";
    $params[] = $filter_praktikum_id;
    $types .= 'i';
}
if (!empty($filter_modul_id)) {
    $where_clauses[] = "m.id = ?";
    $params[] = $filter_modul_id;
    $types .= 'i';
}
if (!empty($filter_status)) {
    $where_clauses[] = "pl.status = ?";
    $params[] = $filter_status;
    $types .= 's';
}

if (count($where_clauses) > 0) {
    $sql .= " WHERE " . implode(' AND ', $where_clauses);
}

$sql .= " ORDER BY pl.tanggal_kumpul DESC";

$stmt = $conn->prepare($sql);
if (count($params) > 0) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Ambil data untuk dropdown filter
$praktikums = $conn->query("SELECT id, nama_praktikum FROM mata_praktikum ORDER BY nama_praktikum");
$moduls = $conn->query("SELECT id, judul_modul FROM modul ORDER BY judul_modul");
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Laporan Masuk</h2>

    <!-- Form Filter -->
    <form action="laporan.php" method="GET" class="mb-6 bg-gray-50 p-4 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="praktikum_id" class="block text-sm font-medium text-gray-700">Mata Praktikum</label>
                <select name="praktikum_id" id="praktikum_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option value="">Semua</option>
                    <?php while($p = $praktikums->fetch_assoc()): ?>
                        <option value="<?php echo $p['id']; ?>" <?php echo ($filter_praktikum_id == $p['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($p['nama_praktikum']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label for="modul_id" class="block text-sm font-medium text-gray-700">Modul</label>
                <select name="modul_id" id="modul_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option value="">Semua</option>
                    <?php while($m = $moduls->fetch_assoc()): ?>
                        <option value="<?php echo $m['id']; ?>" <?php echo ($filter_modul_id == $m['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($m['judul_modul']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option value="">Semua</option>
                    <option value="dikumpulkan" <?php echo ($filter_status == 'dikumpulkan') ? 'selected' : ''; ?>>Dikumpulkan</option>
                    <option value="dinilai" <?php echo ($filter_status == 'dinilai') ? 'selected' : ''; ?>>Dinilai</option>
                </select>
            </div>
            <div class="self-end">
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">Filter</button>
            </div>
        </div>
    </form>

    <!-- Tabel Laporan -->
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-200">
                <tr>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Mahasiswa</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Praktikum & Modul</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Tanggal Kumpul</th>
                    <th class="text-center py-3 px-4 uppercase font-semibold text-sm">Status</th>
                    <th class="text-center py-3 px-4 uppercase font-semibold text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['nama_mahasiswa']); ?></td>
                        <td class="py-3 px-4">
                            <div class="font-semibold"><?php echo htmlspecialchars($row['nama_praktikum']); ?></div>
                            <div class="text-xs text-gray-500"><?php echo htmlspecialchars($row['judul_modul']); ?></div>
                        </td>
                        <td class="py-3 px-4"><?php echo date("d M Y, H:i", strtotime($row['tanggal_kumpul'])); ?></td>
                        <td class="py-3 px-4 text-center">
                            <?php if ($row['status'] == 'dinilai'): ?>
                                <span class="bg-green-200 text-green-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded-full">Dinilai</span>
                            <?php else: ?>
                                <span class="bg-yellow-200 text-yellow-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded-full">Dikumpulkan</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <a href="nilai_laporan.php?id=<?php echo $row['id']; ?>" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs">
                                <?php echo ($row['status'] == 'dinilai') ? 'Lihat/Edit Nilai' : 'Beri Nilai'; ?>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-gray-500">Tidak ada laporan yang ditemukan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$stmt->close();
$conn->close();
require_once 'templates/footer.php';
?>

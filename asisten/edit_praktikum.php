<?php
require_once '../config.php';

$message = '';
$id = $_GET['id'] ?? 0;

// Jika ID tidak valid, langsung redirect tanpa proses lebih lanjut
if ($id == 0) {
    header("Location: mata_praktikum.php");
    exit();
}

// Proses update data jika ada request POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_praktikum = trim($_POST['nama_praktikum']);
    $deskripsi = trim($_POST['deskripsi']);
    $current_id = $_POST['id'];

    if (empty($nama_praktikum)) {
        $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">Nama praktikum tidak boleh kosong.</div>';
    } else {
        // Cek duplikasi nama, kecuali untuk data itu sendiri
        $stmt_check = $conn->prepare("SELECT id FROM mata_praktikum WHERE nama_praktikum = ? AND id != ?");
        $stmt_check->bind_param("si", $nama_praktikum, $current_id);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">Nama praktikum sudah ada. Silakan gunakan nama lain.</div>';
        } else {
            $stmt = $conn->prepare("UPDATE mata_praktikum SET nama_praktikum = ?, deskripsi = ? WHERE id = ?");
            $stmt->bind_param("ssi", $nama_praktikum, $deskripsi, $current_id);

            if ($stmt->execute()) {
                // Jika sukses, redirect SEKARANG dan hentikan eksekusi script
                header("Location: mata_praktikum.php?status=sukses");
                exit();
            } else {
                $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">Terjadi kesalahan. Silakan coba lagi.</div>';
            }
            $stmt->close();
        }
        $stmt_check->close();
    }
}

// Ambil data yang akan diedit untuk ditampilkan di form
$stmt_select = $conn->prepare("SELECT nama_praktikum, deskripsi FROM mata_praktikum WHERE id = ?");
$stmt_select->bind_param("i", $id);
$stmt_select->execute();
$result = $stmt_select->get_result();
if ($result->num_rows === 1) {
    $praktikum = $result->fetch_assoc();
} else {
    // Jika ID tidak ditemukan, redirect
    header("Location: mata_praktikum.php");
    exit();
}
$stmt_select->close();


// =======================================================
// BAGIAN 2: TAMPILAN HTML (SETELAH LOGIKA SELESAI)
// =======================================================
$pageTitle = 'Edit Mata Praktikum';
$activePage = 'mata_praktikum';
require_once 'templates/header.php';
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Formulir Edit Mata Praktikum</h2>
    
    <?php echo $message; // Tampilkan pesan error jika ada ?>

    <form action="edit_praktikum.php?id=<?php echo $id; ?>" method="post">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="mb-4">
            <label for="nama_praktikum" class="block text-gray-700 text-sm font-bold mb-2">Nama Praktikum:</label>
            <input type="text" id="nama_praktikum" name="nama_praktikum" value="<?php echo htmlspecialchars($praktikum['nama_praktikum']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>
        <div class="mb-6">
            <label for="deskripsi" class="block text-gray-700 text-sm font-bold mb-2">Deskripsi:</label>
            <textarea id="deskripsi" name="deskripsi" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo htmlspecialchars($praktikum['deskripsi']); ?></textarea>
        </div>
        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Update
            </button>
            <a href="mata_praktikum.php" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                Batal
            </a>
        </div>
    </form>
</div>

<?php
require_once 'templates/footer.php';
?>
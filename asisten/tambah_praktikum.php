<?php
require_once '../config.php'; // Panggil config lebih dulu untuk koneksi DB

$message = '';
// Proses form HANYA jika ada request POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_praktikum = trim($_POST['nama_praktikum']);
    $deskripsi = trim($_POST['deskripsi']);

    if (empty($nama_praktikum)) {
        $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">Nama praktikum tidak boleh kosong.</div>';
    } else {
        // Cek duplikasi nama
        $stmt_check = $conn->prepare("SELECT id FROM mata_praktikum WHERE nama_praktikum = ?");
        $stmt_check->bind_param("s", $nama_praktikum);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">Nama praktikum sudah ada. Silakan gunakan nama lain.</div>';
        } else {
            $stmt = $conn->prepare("INSERT INTO mata_praktikum (nama_praktikum, deskripsi) VALUES (?, ?)");
            $stmt->bind_param("ss", $nama_praktikum, $deskripsi);

            if ($stmt->execute()) {
                // Jika sukses, redirect SEKARANG dan hentikan eksekusi script
                header("Location: mata_praktikum.php?status=sukses");
                exit(); // Wajib ada exit() setelah header location
            } else {
                $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">Terjadi kesalahan. Silakan coba lagi.</div>';
            }
            $stmt->close();
        }
        $stmt_check->close();
    }
}

$pageTitle = 'Tambah Mata Praktikum';
$activePage = 'mata_praktikum';
require_once 'templates/header.php'; // Header dipanggil setelah semua logika redirect selesai
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Formulir Tambah Mata Praktikum</h2>
    
    <?php echo $message; // Tampilkan pesan error jika ada ?>

    <form action="tambah_praktikum.php" method="post">
        <div class="mb-4">
            <label for="nama_praktikum" class="block text-gray-700 text-sm font-bold mb-2">Nama Praktikum:</label>
            <input type="text" id="nama_praktikum" name="nama_praktikum" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>
        <div class="mb-6">
            <label for="deskripsi" class="block text-gray-700 text-sm font-bold mb-2">Deskripsi:</label>
            <textarea id="deskripsi" name="deskripsi" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
        </div>
        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Simpan
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
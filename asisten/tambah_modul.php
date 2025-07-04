<?php
require_once '../config.php';

$message = '';
$id_praktikum = $_GET['id_praktikum'] ?? 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $praktikum_id = $_POST['praktikum_id'];
    $judul_modul = trim($_POST['judul_modul']);
    $deskripsi = trim($_POST['deskripsi']);
    $urutan = filter_input(INPUT_POST, 'urutan', FILTER_VALIDATE_INT);
    $file_materi_name = '';

    if (empty($judul_modul) || empty($praktikum_id)) {
        $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Judul modul dan ID praktikum wajib diisi.</div>';
    } else {
        if (isset($_FILES['file_materi']) && $_FILES['file_materi']['error'] == 0) {
            // PERUBAHAN: Path target diubah ke sub-folder
            $target_dir = "../uploads/materi_modul/";
            $file_extension = strtolower(pathinfo($_FILES["file_materi"]["name"], PATHINFO_EXTENSION));
            $file_materi_name = uniqid('modul_', true) . '.' . $file_extension;
            $target_file = $target_dir . $file_materi_name;
            
            $allowed_types = ['pdf', 'doc', 'docx', 'ppt', 'pptx'];
            if (in_array($file_extension, $allowed_types)) {
                // Pastikan direktori ada, jika tidak, coba buat
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                if (!move_uploaded_file($_FILES["file_materi"]["tmp_name"], $target_file)) {
                    $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Gagal memindahkan file.</div>';
                    $file_materi_name = '';
                }
            } else {
                $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Format file tidak diizinkan.</div>';
                $file_materi_name = '';
            }
        }

        if (empty($message)) {
            $stmt = $conn->prepare("INSERT INTO modul (praktikum_id, judul_modul, deskripsi, file_materi, urutan) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("isssi", $praktikum_id, $judul_modul, $deskripsi, $file_materi_name, $urutan);

            if ($stmt->execute()) {
                header("Location: detail_modul.php?id_praktikum=" . $praktikum_id . "&status=sukses");
                exit();
            } else {
                $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Gagal menyimpan data.</div>';
            }
            $stmt->close();
        }
    }
}

$pageTitle = 'Tambah Modul';
$activePage = 'modul';
require_once 'templates/header.php';
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Formulir Tambah Modul Baru</h2>
    
    <?php echo $message; ?>

    <form action="tambah_modul.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="praktikum_id" value="<?php echo $id_praktikum; ?>">
        
        <div class="mb-4">
            <label for="judul_modul" class="block text-gray-700 text-sm font-bold mb-2">Judul Modul:</label>
            <input type="text" id="judul_modul" name="judul_modul" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
        </div>
        <div class="mb-4">
            <label for="deskripsi" class="block text-gray-700 text-sm font-bold mb-2">Deskripsi Singkat:</label>
            <textarea id="deskripsi" name="deskripsi" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"></textarea>
        </div>
        <div class="mb-4">
            <label for="urutan" class="block text-gray-700 text-sm font-bold mb-2">Nomor Urut Modul:</label>
            <input type="number" id="urutan" name="urutan" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" placeholder="Contoh: 1">
        </div>
        <div class="mb-6">
            <label for="file_materi" class="block text-gray-700 text-sm font-bold mb-2">Unggah File Materi (Opsional):</label>
            <input type="file" id="file_materi" name="file_materi" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            <p class="text-xs text-gray-500 mt-1">Tipe file yang diizinkan: PDF, DOC, DOCX, PPT, PPTX.</p>
        </div>
        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Simpan Modul
            </button>
            <a href="detail_modul.php?id_praktikum=<?php echo $id_praktikum; ?>" class="font-bold text-sm text-blue-500 hover:text-blue-800">
                Batal
            </a>
        </div>
    </form>
</div>

<?php
require_once 'templates/footer.php';
?>

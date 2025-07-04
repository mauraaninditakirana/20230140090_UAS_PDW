<?php
require_once '../config.php';

$message = '';
$id_modul = $_GET['id'] ?? 0;

if ($id_modul == 0) {
    header("Location: modul.php");
    exit();
}

$stmt_current = $conn->prepare("SELECT * FROM modul WHERE id = ?");
$stmt_current->bind_param("i", $id_modul);
$stmt_current->execute();
$result_current = $stmt_current->get_result();
if ($result_current->num_rows === 0) {
    header("Location: modul.php");
    exit();
}
$modul = $result_current->fetch_assoc();
$old_file_name = $modul['file_materi'];
$id_praktikum = $modul['praktikum_id'];
$stmt_current->close();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul_modul = trim($_POST['judul_modul']);
    $deskripsi = trim($_POST['deskripsi']);
    $urutan = filter_input(INPUT_POST, 'urutan', FILTER_VALIDATE_INT);
    $file_materi_name = $old_file_name;

    if (isset($_FILES['file_materi']) && $_FILES['file_materi']['error'] == 0) {
        // PERUBAHAN: Path untuk hapus file lama
        $old_file_path = "../uploads/materi_modul/" . $old_file_name;
        if (!empty($old_file_name) && file_exists($old_file_path)) {
            unlink($old_file_path);
        }

        // PERUBAHAN: Path untuk simpan file baru
        $target_dir = "../uploads/materi_modul/";
        $file_extension = strtolower(pathinfo($_FILES["file_materi"]["name"], PATHINFO_EXTENSION));
        $file_materi_name = uniqid('modul_', true) . '.' . $file_extension;
        $target_file = $target_dir . $file_materi_name;
        
        $allowed_types = ['pdf', 'doc', 'docx', 'ppt', 'pptx'];
        if (in_array($file_extension, $allowed_types)) {
            if (!move_uploaded_file($_FILES["file_materi"]["tmp_name"], $target_file)) {
                $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Gagal memindahkan file.</div>';
                $file_materi_name = $old_file_name;
            }
        } else {
            $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Format file tidak diizinkan.</div>';
            $file_materi_name = $old_file_name;
        }
    }

    if (empty($message)) {
        $stmt_update = $conn->prepare("UPDATE modul SET judul_modul = ?, deskripsi = ?, file_materi = ?, urutan = ? WHERE id = ?");
        $stmt_update->bind_param("sssii", $judul_modul, $deskripsi, $file_materi_name, $urutan, $id_modul);

        if ($stmt_update->execute()) {
            header("Location: detail_modul.php?id_praktikum=" . $id_praktikum . "&status=sukses");
            exit();
        } else {
            $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Gagal memperbarui data.</div>';
        }
        $stmt_update->close();
    }
}

$pageTitle = 'Edit Modul';
$activePage = 'modul';
require_once 'templates/header.php';
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Formulir Edit Modul</h2>
    
    <?php echo $message; ?>

    <form action="edit_modul.php?id=<?php echo $id_modul; ?>" method="post" enctype="multipart/form-data">
        <div class="mb-4">
            <label for="judul_modul" class="block text-gray-700 text-sm font-bold mb-2">Judul Modul:</label>
            <input type="text" id="judul_modul" name="judul_modul" value="<?php echo htmlspecialchars($modul['judul_modul']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
        </div>
        <div class="mb-4">
            <label for="deskripsi" class="block text-gray-700 text-sm font-bold mb-2">Deskripsi Singkat:</label>
            <textarea id="deskripsi" name="deskripsi" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"><?php echo htmlspecialchars($modul['deskripsi']); ?></textarea>
        </div>
        <div class="mb-4">
            <label for="urutan" class="block text-gray-700 text-sm font-bold mb-2">Nomor Urut Modul:</label>
            <input type="number" id="urutan" name="urutan" value="<?php echo htmlspecialchars($modul['urutan']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
        </div>
        <div class="mb-6">
            <label for="file_materi" class="block text-gray-700 text-sm font-bold mb-2">Unggah File Materi Baru (Opsional):</label>
            <?php if (!empty($old_file_name)): ?>
                <!-- PERUBAHAN: Path untuk link file lama -->
                <p class="text-xs text-gray-600 mb-2">File saat ini: <a href="../uploads/materi_modul/<?php echo $old_file_name; ?>" target="_blank" class="text-blue-500"><?php echo $old_file_name; ?></a></p>
            <?php endif; ?>
            <input type="file" id="file_materi" name="file_materi" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            <p class="text-xs text-gray-500 mt-1">Jika Anda mengunggah file baru, file lama akan otomatis diganti.</p>
        </div>
        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Update Modul
            </button>
            <a href="detail_modul.php?id_praktikum=<?php echo $id_praktikum; ?>" class="font-bold text-sm text-blue-500 hover:text-blue-800">
                Batal
            </a>
        </div>
    </form>
</div>

<?php
$conn->close();
require_once 'templates/footer.php';
?>

<?php
require_once '../config.php';

$message = '';
$user_id = $_GET['id'] ?? 0;

if ($user_id == 0) {
    header("Location: users.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);
    $password = trim($_POST['password']);

    if (empty($nama) || empty($email) || empty($role)) {
        $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Nama, email, dan peran wajib diisi.</div>';
    } else {
        if (!empty($password)) {
            // Jika password diisi, update password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE users SET nama = ?, email = ?, role = ?, password = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $nama, $email, $role, $hashed_password, $user_id);
        } else {
            // Jika password kosong, jangan update password
            $stmt = $conn->prepare("UPDATE users SET nama = ?, email = ?, role = ? WHERE id = ?");
            $stmt->bind_param("sssi", $nama, $email, $role, $user_id);
        }

        if ($stmt->execute()) {
            header("Location: users.php?status=sukses");
            exit();
        } else {
            $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Gagal memperbarui data.</div>';
        }
        $stmt->close();
    }
}

// Ambil data pengguna saat ini untuk ditampilkan di form
$stmt_select = $conn->prepare("SELECT nama, email, role FROM users WHERE id = ?");
$stmt_select->bind_param("i", $user_id);
$stmt_select->execute();
$user = $stmt_select->get_result()->fetch_assoc();
$stmt_select->close();

if (!$user) {
    header("Location: users.php");
    exit();
}

$pageTitle = 'Edit Pengguna';
$activePage = 'users';
require_once 'templates/header.php';
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Formulir Edit Pengguna</h2>
    <?php if(!empty($message)) { echo "<div class='mb-4'>$message</div>"; } ?>
    <form action="edit_user.php?id=<?php echo $user_id; ?>" method="post">
        <div class="mb-4">
            <label for="nama" class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap:</label>
            <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($user['nama']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
        </div>
        <div class="mb-4">
            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
        </div>
        <div class="mb-4">
            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password Baru:</label>
            <input type="password" id="password" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
            <p class="text-xs text-gray-600 mt-1">Kosongkan jika tidak ingin mengubah password.</p>
        </div>
        <div class="mb-6">
            <label for="role" class="block text-gray-700 text-sm font-bold mb-2">Peran:</label>
            <select id="role" name="role" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                <option value="mahasiswa" <?php echo ($user['role'] == 'mahasiswa') ? 'selected' : ''; ?>>Mahasiswa</option>
                <option value="asisten" <?php echo ($user['role'] == 'asisten') ? 'selected' : ''; ?>>Asisten</option>
            </select>
        </div>
        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Update</button>
            <a href="users.php" class="font-bold text-sm text-blue-500 hover:text-blue-800">Batal</a>
        </div>
    </form>
</div>

<?php
require_once 'templates/footer.php';
?>

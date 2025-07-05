<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    header("Location: ../login.php"); 
    exit();
}

$currentPage = basename($_SERVER['SCRIPT_NAME']);
$dashboardPages = ['dashboard.php'];
$modulPages = ['modul.php', 'detail_modul.php', 'tambah_modul.php', 'edit_modul.php'];
$praktikumPages = ['mata_praktikum.php', 'tambah_praktikum.php', 'edit_praktikum.php'];
$laporanPages = ['laporan.php', 'nilai_laporan.php'];
$userPages = ['users.php', 'tambah_user.php', 'edit_user.php'];


?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panel Asisten - <?php echo $pageTitle ?? 'Dashboard'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100">

<div class="flex h-screen bg-slate-100">
    <aside class="w-64 bg-slate-800 text-white flex flex-col">
        <div class="p-6 text-center border-b border-slate-700">
            <h3 class="text-xl font-bold">Panel Asisten</h3>
            <p class="text-sm text-slate-400 mt-1"><?php echo htmlspecialchars($_SESSION['nama']); ?></p>
        </div>
        <nav class="flex-grow p-4">
            <ul class="space-y-2">
                <?php 
                    $activeClass = 'bg-indigo-600 text-white';
                    $inactiveClass = 'text-slate-300 hover:bg-slate-700 hover:text-white';
                ?>
                <li>
                    <a href="dashboard.php" class="<?php echo in_array($currentPage, $dashboardPages) ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-2.5 rounded-md transition-colors duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="modul.php" class="<?php echo in_array($currentPage, $modulPages) ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-2.5 rounded-md transition-colors duration-200">
                         <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v11.494m-5.747-6.248h11.494"></path></svg>
                        <span>Manajemen Modul</span>
                    </a>
                </li>
                <li>
                    <a href="mata_praktikum.php" class="<?php echo in_array($currentPage, $praktikumPages) ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-2.5 rounded-md transition-colors duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <span>Manajemen Praktikum</span>
                    </a>
                </li>
                <li>
                    <a href="laporan.php" class="<?php echo in_array($currentPage, $laporanPages) ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-2.5 rounded-md transition-colors duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Laporan Masuk</span>
                    </a>
                </li>
                <li>
                    <a href="users.php" class="<?php echo in_array($currentPage, $userPages) ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-2.5 rounded-md transition-colors duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-3-5.197M15 21a9 9 0 00-9-5.197"></path></svg>
                        <span>Kelola Pengguna</span>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="p-4 mt-auto">
            <a href="../logout.php" class="flex items-center justify-center bg-rose-500 hover:bg-rose-600 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 p-6 lg:p-10">
        <header class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-slate-800"><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
        </header>

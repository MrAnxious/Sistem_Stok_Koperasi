<?php
session_start();

// Proteksi halaman: pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}

require '../../config/koneksi.php';
?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    
    <?php if(!empty($_SETTINGS['favicon'])): ?>
        <link rel="icon" type="image/png" href="../../assets/logo_sistem/<?= htmlspecialchars($_SETTINGS['favicon']) ?>">
    <?php endif; ?>
    <title>Cetak Laporan - <?= htmlspecialchars($_SETTINGS['nama_sistem'] ?? 'Koperasi Merah Putih') ?></title>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <script id="tailwind-config">
        tailwind.config = {
            theme: {
                extend: { colors: { "primary": "#af101a", "surface": "#f8f9fa" }, fontFamily: { "headline": ["Work Sans", "sans-serif"], "body": ["Inter", "sans-serif"] } },
            },
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        h1, h2, h3 { font-family: 'Work Sans', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    </style>
</head>

<body class="text-gray-800 min-h-screen flex" x-data="{ isSidebarOpen: false }">
    
    <div x-show="isSidebarOpen" @click="isSidebarOpen = false" x-transition.opacity class="fixed inset-0 bg-black/50 z-40 lg:hidden" style="display: none;"></div>

    <aside class="fixed inset-y-0 left-0 z-50 w-72 bg-white flex flex-col h-screen transform transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-auto border-r border-gray-200" :class="{'translate-x-0': isSidebarOpen, '-translate-x-full': !isSidebarOpen}">
                <div class="px-8 py-8 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <?php if(!empty($_SETTINGS['logo'])): ?>
                    <img src="../../assets/logo_sistem/<?= htmlspecialchars($_SETTINGS['logo']) ?>" class="w-10 h-10 rounded-xl object-cover" alt="Logo">
                <?php else: ?>
                    <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-white">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">account_balance</span>
                    </div>
                <?php endif; ?>
                <div>
                    <h2 class="text-primary font-bold text-lg leading-none truncate w-40"><?= htmlspecialchars($_SETTINGS['nama_sistem'] ?? 'Merah Putih') ?></h2>
                    <p class="text-gray-500 text-xs mt-1">Sistem Koperasi</p>
                </div>
            </div>
            <button @click="isSidebarOpen = false" class="lg:hidden text-gray-500 hover:text-red-600"><span class="material-symbols-outlined">close</span></button>
        </div>
        
        <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="dashboard.php">
                <span class="material-symbols-outlined">dashboard</span><span>Dashboard</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="stok_barang.php">
                <span class="material-symbols-outlined">inventory_2</span><span>Stok Barang</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="barang_masuk.php">
                <span class="material-symbols-outlined">input</span><span>Barang Masuk</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="barang_keluar.php">
                <span class="material-symbols-outlined">output</span><span>Barang Keluar</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl bg-primary/10 text-primary font-semibold relative" href="cetak_laporan.php">
                <div class="absolute left-0 w-1 h-6 bg-primary rounded-r-full"></div>
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">print</span><span>Cetak Laporan</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="supplier.php">
                <span class="material-symbols-outlined">local_shipping</span><span>Supplier</span>
            </a>
                    <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="log_aktivitas.php">
                <span class="material-symbols-outlined">manage_search</span><span>Log Aktivitas</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="pengaturan.php">
                
                <span class="material-symbols-outlined" style="">settings</span>
                <span>Pengaturan Sistem</span>
            </a>
</nav>
        
                <div class="p-4 border-t border-gray-200 space-y-2">
            <a href="profil.php" class="w-full flex items-center gap-3 px-4 py-3 text-gray-700 font-medium hover:bg-gray-100 rounded-xl transition-colors">
                <span class="material-symbols-outlined">person</span><span>Profil Saya</span>
            </a>
            <a href="../../proses/auth_logout.php" class="w-full flex items-center gap-3 px-4 py-3 text-red-600 font-medium hover:bg-red-50 rounded-xl transition-colors">
                <span class="material-symbols-outlined">logout</span><span>Keluar</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col min-w-0 w-full overflow-hidden">
        <header class="h-20 bg-white flex items-center justify-between px-6 lg:px-12 border-b border-gray-200 z-10 sticky top-0">
            <button @click="isSidebarOpen = true" class="lg:hidden p-2 text-gray-600 rounded-md hover:bg-gray-100"><span class="material-symbols-outlined">menu</span></button>
            <div class="flex items-center gap-3 ml-auto">
                <?php include '../komponen/notifikasi.php'; ?>
                <div class="h-8 w-[1px] bg-gray-300 hidden sm:block"></div>
                
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold leading-none"><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username']); ?></p>
                        <p class="text-xs text-gray-500 mt-1">Admin Koperasi</p>
                    </div>
                    <?php if(!empty($_SESSION['foto'])): ?>
                        <img alt="Profil" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full object-cover border-2 border-gray-200" src="../../assets/admin/<?= htmlspecialchars($_SESSION['foto']) ?>"/>
                    <?php else: ?>
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-primary/20 flex items-center justify-center border-2 border-primary/30 text-primary">
                            <span class="material-symbols-outlined">person</span>
                        </div>
                    <?php endif; ?>
                </div>
        </header>

        <div class="flex-1 px-4 sm:px-6 lg:px-12 py-8 overflow-y-auto w-full flex justify-center">
            <div class="w-full max-w-2xl bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden self-start">
                <div class="p-8 border-b border-gray-50 bg-gradient-to-r from-red-50 to-transparent">
                    <h2 class="font-bold text-2xl text-gray-900">Filter Laporan Transaksi</h2>
                    <p class="text-gray-500 mt-1">Pilih kriteria data yang ingin Anda cetak ke dalam PDF.</p>
                </div>
                
                <form action="print_view.php" method="GET" target="_blank" class="p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">calendar_today</span> Tanggal Mulai
                            </label>
                            <input type="date" name="tgl_mulai" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">event</span> Tanggal Selesai
                            </label>
                            <input type="date" name="tgl_selesai" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">filter_list</span> Jenis Laporan
                        </label>
                        <select name="jenis" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                            <option value="semua">Semua Transaksi (Masuk & Keluar)</option>
                            <option value="masuk">Hanya Barang Masuk</option>
                            <option value="keluar">Hanya Barang Keluar</option>
                        </select>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full bg-primary hover:bg-red-800 text-white font-bold py-4 rounded-2xl shadow-lg shadow-red-900/20 flex items-center justify-center gap-3 transition-all active:scale-[0.98]">
                            <span class="material-symbols-outlined">picture_as_pdf</span>
                            Buka Laporan & Cetak PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
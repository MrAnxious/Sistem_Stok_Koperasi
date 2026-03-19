<?php
session_start();

// Proteksi halaman: Cek apakah user sudah login dan apakah rolenya kepala
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kepala') {
    header("Location: ../../index.php");
    exit();
}

require '../../config/koneksi.php';

// Ambil semua data stok
$query = "SELECT * FROM stok ORDER BY id_barang DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    
    <?php if(!empty($_SETTINGS['favicon'])): ?>
        <link rel="icon" type="image/png" href="../../assets/logo_sistem/<?= htmlspecialchars($_SETTINGS['favicon']) ?>">
    <?php endif; ?>
    <title>Laporan Stok Barang - <?= htmlspecialchars($_SETTINGS['nama_sistem'] ?? 'Koperasi Merah Putih') ?></title>
    
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
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>

<body class="text-gray-800 min-h-screen flex" x-data="{ isSidebarOpen: false, showPreview: false, previewUrl: '' }">
    
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
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl bg-primary/10 text-primary font-semibold relative" href="laporan_stok.php">
                <div class="absolute left-0 w-1 h-6 bg-primary rounded-r-full"></div>
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">inventory_2</span><span>Laporan Stok</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="riwayat_transaksi.php">
                <span class="material-symbols-outlined">history</span><span>Riwayat Transaksi</span>
            </a>
                    <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="log_aktivitas.php">
                <span class="material-symbols-outlined">manage_search</span><span>Log Aktivitas</span>
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
            <div class="flex items-center justify-end flex-1 gap-3 sm:gap-6">
                <?php include '../komponen/notifikasi.php'; ?>
                <div class="h-8 w-[1px] bg-gray-300 hidden sm:block"></div>
                
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold leading-none"><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username']); ?></p>
                        <p class="text-xs text-gray-500 mt-1">Kepala Koperasi</p>
                    </div>
                    <?php if(!empty($_SESSION['foto'])): ?>
                        <img alt="Profil" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full object-cover border-2 border-gray-200" src="../../assets/kepala/<?= htmlspecialchars($_SESSION['foto']) ?>"/>
                    <?php else: ?>
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-primary/20 flex items-center justify-center border-2 border-primary/30 text-primary">
                            <span class="material-symbols-outlined">person</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <div class="flex-1 px-4 sm:px-6 lg:px-12 py-8 overflow-y-auto w-full">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900">Laporan Stok Barang (Read-Only)</h1>
                    <p class="text-gray-500 mt-1 text-sm">Lihat daftar persediaan barang secara komprehensif.</p>
                </div>
                <a href="../admin/print_stok.php" target="_blank" class="bg-gray-900 hover:bg-black text-white px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center justify-center gap-2 transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-lg">print</span>
                    Cetak Laporan PDF
                </a>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm w-full overflow-hidden">
                <div class="w-full overflow-x-auto hide-scrollbar">
                    <table class="w-full text-left min-w-[800px]">
                        <thead>
                            <tr class="text-xs font-bold text-gray-500 border-b border-gray-200 uppercase tracking-wider bg-gray-50">
                                <th class="p-4 w-16 text-center">ID</th>
                                <th class="p-4 w-32 text-center">Foto</th> 
                                <th class="p-4">Nama Barang</th>
                                <th class="p-4">Kategori</th>
                                <th class="p-4 text-center">Stok Tersedia</th>
                                <th class="p-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100">
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 text-center text-gray-500 align-middle"><?= $row['id_barang']; ?></td>
                                    
                                    <td class="p-4 text-center align-middle">
                                        <?php if(!empty($row['foto'])): ?>
                                            <img src="../../uploads/barang/<?= $row['foto']; ?>" 
                                                 alt="<?= htmlspecialchars($row['nama_barang']); ?>" 
                                                 class="w-16 h-16 object-cover rounded-lg border border-gray-200 mx-auto shadow-sm cursor-zoom-in hover:scale-105 transition-transform"
                                                 @click="previewUrl = '../../uploads/barang/<?= $row['foto']; ?>'; showPreview = true;"
                                                 onerror="this.onerror=null; this.src='https://placehold.co/100x100?text=No+Img';">
                                        <?php else: ?>
                                            <div class="w-16 h-16 bg-gray-100 flex items-center justify-center rounded-lg border border-gray-200 mx-auto text-gray-400 shadow-sm">
                                                <span class="material-symbols-outlined text-[28px]">image_not_supported</span>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td class="p-4 font-semibold text-gray-800 align-middle"><?= $row['nama_barang']; ?></td>
                                    <td class="p-4 align-middle"><span class="px-3 py-1 bg-gray-100 rounded-md text-xs font-medium text-gray-600"><?= $row['kategori']; ?></span></td>
                                    <td class="p-4 text-center align-middle">
                                        <span class="px-3 py-1 font-bold rounded-md text-sm <?= ($row['jumlah_stok'] == 0) ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'; ?>">
                                            <?= $row['jumlah_stok']; ?>
                                        </span>
                                    </td>
                                    
                                    <td class="p-4 text-center align-middle">
                                        <?php if ($row['jumlah_stok'] > 10): ?>
                                            <span class="text-green-600 font-semibold text-xs flex items-center justify-center gap-1"><span class="material-symbols-outlined" style="font-size: 16px;">check_circle</span> Aman</span>
                                        <?php elseif ($row['jumlah_stok'] > 0): ?>
                                            <span class="text-orange-500 font-semibold text-xs flex items-center justify-center gap-1"><span class="material-symbols-outlined" style="font-size: 16px;">warning</span> Menipis</span>
                                        <?php else: ?>
                                            <span class="text-red-600 font-semibold text-xs flex items-center justify-center gap-1"><span class="material-symbols-outlined" style="font-size: 16px;">error</span> Habis</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-gray-500">
                                        <span class="material-symbols-outlined text-4xl mb-2 text-gray-300">inventory_2</span>
                                        <p>Belum ada data barang.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div x-show="showPreview" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100">
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm" @click="showPreview = false"></div>
        <div class="relative max-w-3xl w-full flex flex-col items-center">
            <button @click="showPreview = false" class="absolute -top-12 right-0 text-white hover:text-red-500 transition-colors">
                <span class="material-symbols-outlined text-4xl">close</span>
            </button>
            <img :src="previewUrl" class="max-w-full max-h-[80vh] rounded-2xl shadow-2xl border-4 border-white/10" alt="Preview">
        </div>
    </div>
</body>
</html>

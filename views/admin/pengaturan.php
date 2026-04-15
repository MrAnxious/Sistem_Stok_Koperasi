<?php
session_start();

// Proteksi halaman: Cek apakah user sudah login dan apakah rolenya admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}

require '../../config/koneksi.php';

// Data pengaturan global sudah diambil di koneksi.php ke variabel $_SETTINGS
// Jika belum ada karena error atau sesuatu hal, siapkan default
if (!isset($_SETTINGS)) {
    $_SETTINGS = [
        'nama_sistem' => 'Koperasi Merah Putih',
        'logo' => '',
        'favicon' => ''
    ];
}
?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Pengaturan Sistem - <?= htmlspecialchars($_SETTINGS['nama_sistem']) ?></title>
    
    <?php if(!empty($_SETTINGS['favicon'])): ?>
        <link rel="icon" type="image/png" href="../../assets/logo_sistem/<?= htmlspecialchars($_SETTINGS['favicon']) ?>">
    <?php endif; ?>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#af101a",
                        "surface": "#f8f9fa",
                    },
                    fontFamily: {
                        "headline": ["Work Sans", "sans-serif"],
                        "body": ["Inter", "sans-serif"],
                    },
                },
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

    <aside class="fixed inset-y-0 left-0 z-50 w-72 bg-white flex flex-col h-screen transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto shadow-xl lg:shadow-none border-r border-gray-200" :class="{'translate-x-0': isSidebarOpen, '-translate-x-full': !isSidebarOpen}">
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
                    <h2 class="text-primary font-bold text-lg leading-none truncate w-40"><?= htmlspecialchars($_SETTINGS['nama_sistem']) ?></h2>
                    <p class="text-gray-500 text-xs mt-1">Sistem Koperasi</p>
                </div>
            </div>
            <button @click="isSidebarOpen = false" class="lg:hidden text-gray-500 hover:text-red-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="dashboard.php">
                <span class="material-symbols-outlined">dashboard</span><span>Dashboard</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="stok_barang.php">
                <span class="material-symbols-outlined">inventory_2</span><span>Stok Barang</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="kategori.php">
                <span class="material-symbols-outlined">category</span><span>Kategori Barang</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="barang_masuk.php">
                <span class="material-symbols-outlined">input</span><span>Barang Masuk</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="barang_keluar.php">
                <span class="material-symbols-outlined">output</span><span>Barang Keluar</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="cetak_laporan.php">
                <span class="material-symbols-outlined">print</span><span>Cetak Laporan</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="supplier.php">
                <span class="material-symbols-outlined">local_shipping</span><span>Supplier</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="log_aktivitas.php">
                <span class="material-symbols-outlined">manage_search</span><span>Log Aktivitas</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 bg-primary/10 text-primary font-semibold rounded-xl transition-colors relative" href="pengaturan.php">
                <div class="absolute left-0 w-1 h-6 bg-primary rounded-r-full"></div>
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">settings</span><span>Pengaturan Sistem</span>
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
            <div class="flex items-center gap-4 flex-1">
                <button @click="isSidebarOpen = true" class="lg:hidden p-2 text-gray-600 rounded-md hover:bg-gray-100">
                    <span class="material-symbols-outlined">menu</span>
                </button>
            </div>
            
            <div class="flex items-center justify-end flex-1 gap-3 sm:gap-6">
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
            </div>
        </header>

        <div class="flex-1 px-4 sm:px-6 lg:px-12 py-8 overflow-y-auto w-full flex justify-center">
            
            <div class="w-full max-w-2xl bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden self-start">
                <div class="p-8 border-b border-gray-50 flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-3xl">settings_applications</span>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Pengaturan Sistem</h2>
                        <p class="text-gray-500 mt-1">Sesuaikan identitas dan tampilan sistem koperasi.</p>
                    </div>
                </div>

                <form action="../../proses/update_pengaturan.php" method="POST" enctype="multipart/form-data" class="p-8">
                    
                    <?php if (isset($_SESSION['pesan_pengaturan'])): ?>
                        <?php 
                            $is_error = strpos($_SESSION['pesan_pengaturan'], 'Gagal') !== false;
                            $bg_class = $is_error ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200';
                            $icon = $is_error ? 'error' : 'check_circle';
                        ?>
                        <div class="mb-6 p-4 rounded-xl <?= $bg_class ?> flex items-center gap-3">
                            <span class="material-symbols-outlined"><?= $icon ?></span>
                            <span class="text-sm font-medium">
                                <?= $_SESSION['pesan_pengaturan'] ?>
                            </span>
                        </div>
                        <?php unset($_SESSION['pesan_pengaturan']); ?>
                    <?php endif; ?>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">badge</span> Nama Sistem Identitas
                            </label>
                            <input type="text" name="nama_sistem" value="<?= htmlspecialchars($_SETTINGS['nama_sistem']) ?>" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                        </div>
                        
                        <div class="space-y-4 pt-4 border-t border-gray-100">
                            <div class="flex flex-col sm:flex-row gap-6 items-start">
                                <div class="shrink-0 flex flex-col items-center gap-2">
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Logo Saat Ini</span>
                                    <?php if(!empty($_SETTINGS['logo'])): ?>
                                        <div class="w-24 h-24 rounded-2xl border-4 border-gray-100 shadow-sm overflow-hidden bg-white flex items-center justify-center">
                                            <img alt="Logo Sistem" class="w-full h-full object-contain" src="../../assets/logo_sistem/<?= htmlspecialchars($_SETTINGS['logo']); ?>"/>
                                        </div>
                                    <?php else: ?>
                                        <div class="w-24 h-24 rounded-2xl bg-primary/20 flex items-center justify-center border-4 border-gray-100 text-primary shadow-sm">
                                            <span class="material-symbols-outlined text-3xl">account_balance</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="space-y-2 w-full mt-4 sm:mt-6">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-sm">image</span> Ganti Logo Sistem (Opsional)
                                    </label>
                                    <input type="file" name="logo" accept="image/*" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-red-800 cursor-pointer text-sm">
                                    <p class="text-xs text-gray-500">Maks. ukuran 2MB. Format: JPG, PNG, GIF</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 pt-4 border-t border-gray-100">
                            <div class="flex flex-col sm:flex-row gap-6 items-start">
                                <div class="shrink-0 flex flex-col items-center gap-2">
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Favicon Saat Ini</span>
                                    <?php if(!empty($_SETTINGS['favicon'])): ?>
                                        <div class="w-16 h-16 rounded-xl border-4 border-gray-100 shadow-sm overflow-hidden bg-white flex items-center justify-center">
                                            <img alt="Favicon" class="w-full h-full object-contain" src="../../assets/logo_sistem/<?= htmlspecialchars($_SETTINGS['favicon']); ?>"/>
                                        </div>
                                    <?php else: ?>
                                        <div class="w-16 h-16 rounded-xl bg-gray-100 flex items-center justify-center border-4 border-gray-200 text-gray-400 shadow-sm">
                                            <span class="material-symbols-outlined text-2xl">tab</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="space-y-2 w-full mt-2 sm:mt-4">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-sm">tab</span> Ganti Favicon (Opsional)
                                    </label>
                                    <input type="file" name="favicon" accept="image/x-icon,image/png,image/jpeg" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-700 file:text-white hover:file:bg-gray-900 cursor-pointer text-sm">
                                    <p class="text-xs text-gray-500">Ikon di tab browser. Disarankan ukuran 32x32px. Format: ICO, PNG</p>
                                </div>
                            </div>
                        </div>
                        
                    </div>

                    <div class="pt-8 mt-8 border-t border-gray-100">
                        <button type="submit" class="w-full bg-primary hover:bg-red-800 text-white font-bold py-4 rounded-2xl shadow-lg shadow-red-900/20 flex items-center justify-center gap-3 transition-all active:scale-[0.98]">
                            <span class="material-symbols-outlined">save</span> Simpan Perubahan Sistem
                        </button>
                    </div>
                </form>
            </div>
            
        </div>
    </main>
</body>
</html>

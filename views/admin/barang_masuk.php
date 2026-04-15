<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}

require '../../config/koneksi.php';

// Ambil riwayat barang masuk beserta nama barang, supplier, dan admin yang input
$query_masuk = "SELECT bm.*, s.nama_barang, sup.nama_supplier, u.username 
                FROM barang_masuk bm 
                JOIN stok s ON bm.id_barang = s.id_barang 
                LEFT JOIN supplier sup ON bm.id_supplier = sup.id_supplier 
                JOIN user u ON bm.id_user = u.id_user 
                ORDER BY bm.tanggal_masuk DESC";
$result_masuk = mysqli_query($koneksi, $query_masuk);

// Ambil data stok untuk dropdown form
$query_stok = mysqli_query($koneksi, "SELECT * FROM stok ORDER BY nama_barang ASC");
// Ambil data supplier untuk dropdown form
$query_supplier = mysqli_query($koneksi, "SELECT * FROM supplier ORDER BY nama_supplier ASC");
?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    
    <?php if(!empty($_SETTINGS['favicon'])): ?>
        <link rel="icon" type="image/png" href="../../assets/logo_sistem/<?= htmlspecialchars($_SETTINGS['favicon']) ?>">
    <?php endif; ?>
    <title>Barang Masuk - <?= htmlspecialchars($_SETTINGS['nama_sistem'] ?? 'Koperasi Merah Putih') ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { "primary": "#af101a", "surface": "#f8f9fa" }, fontFamily: { "headline": ["Work Sans", "sans-serif"], "body": ["Inter", "sans-serif"] } } }
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

<body class="text-gray-800 min-h-screen flex" x-data="{ isSidebarOpen: false, showAddModal: false }">
    
    <div x-show="isSidebarOpen" @click="isSidebarOpen = false" x-transition.opacity class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>

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
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="kategori.php">
                <span class="material-symbols-outlined">category</span><span>Kategori Barang</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl bg-primary/10 text-primary font-semibold relative" href="barang_masuk.php">
                <div class="absolute left-0 w-1 h-6 bg-primary rounded-r-full"></div>
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">input</span><span>Barang Masuk</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="barang_keluar.php">
                <span class="material-symbols-outlined">output</span><span>Barang Keluar</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="../admin/cetak_laporan.php">
                <span class="material-symbols-outlined">print</span><span>Cetak Laporan</span>
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
            <button @click="isSidebarOpen = true" class="lg:hidden p-2 text-gray-600"><span class="material-symbols-outlined">menu</span></button>
            <div class="flex items-center gap-3 sm:gap-6 ml-auto">
                <?php include '../komponen/notifikasi.php'; ?>
                <div class="h-8 w-[1px] bg-gray-300 hidden sm:block"></div>
                
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold leading-none"><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username']); ?></p>
                        <p class="text-xs text-gray-500 mt-1">Admin Koperasi</p>
                    </div>
                    <?php if(!empty($_SESSION['foto'])): ?>
                        <img alt="Profil" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full object-cover border-2 border-gray-200" src="../../assets/<?php echo $_SESSION['role']; ?>/<?= htmlspecialchars($_SESSION['foto']) ?>"/>
                    <?php else: ?>
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-primary/20 flex items-center justify-center border-2 border-primary/30 text-primary">
                            <span class="material-symbols-outlined">person</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <div class="flex-1 px-4 sm:px-6 lg:px-12 py-8 overflow-y-auto w-full">
            <?php if (isset($_SESSION['pesan'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined">check_circle</span>
                    <span><?= $_SESSION['pesan']; ?></span>
                </div>
                <?php unset($_SESSION['pesan']); ?>
            <?php endif; ?>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900">Barang Masuk</h1>
                    <p class="text-gray-500 mt-1 text-sm">Catat penambahan stok dari supplier ke gudang.</p>
                </div>
                <button @click="showAddModal = true" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center justify-center gap-2 transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-lg">add_box</span>
                    Input Barang Masuk
                </button>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm w-full overflow-hidden">
                <div class="w-full overflow-x-auto hide-scrollbar">
                    <table class="w-full text-left min-w-[800px]">
                        <thead>
                            <tr class="text-xs font-bold text-gray-500 border-b border-gray-200 uppercase tracking-wider bg-gray-50">
                                <th class="p-4 rounded-tl-lg">No TRX</th>
                                <th class="p-4">Tanggal & Waktu</th>
                                <th class="p-4">Nama Barang</th>
                                <th class="p-4">Supplier</th>
                                <th class="p-4 text-center">Jumlah Masuk</th>
                                <th class="p-4 text-right">Admin Input</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100">
                            <?php if (mysqli_num_rows($result_masuk) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result_masuk)): ?>
                                <?php $no_trx = 'TM' . str_pad($row['id_masuk'], 3, '0', STR_PAD_LEFT); ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 font-semibold text-gray-800"><?= $no_trx; ?></td>
                                    <td class="p-4 text-gray-600"><?= date('d M Y, H:i', strtotime($row['tanggal_masuk'])); ?></td>
                                    <td class="p-4 font-semibold text-gray-800"><?= $row['nama_barang']; ?></td>
                                    <td class="p-4 text-gray-600"><?= $row['nama_supplier'] ?? '-'; ?></td>
                                    <td class="p-4 text-center">
                                        <span class="px-3 py-1 bg-green-100 text-green-700 font-bold rounded-md text-sm">+<?= $row['jumlah_masuk']; ?></span>
                                    </td>
                                    <td class="p-4 text-right text-gray-500"><?= $row['username']; ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-gray-500">
                                        <span class="material-symbols-outlined text-4xl mb-2 text-gray-300">history</span>
                                        <p>Belum ada riwayat barang masuk.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div x-show="showAddModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-0" style="display: none;">
        <div x-show="showAddModal" x-transition.opacity @click="showAddModal = false" class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
        <div x-show="showAddModal" x-transition.scale class="relative bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-lg text-gray-900">Form Barang Masuk</h3>
                <button @click="showAddModal = false" class="text-gray-400 hover:text-red-500"><span class="material-symbols-outlined">close</span></button>
            </div>
            <form action="../../proses/input_masuk.php" method="POST" class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Barang</label>
                        <select name="id_barang" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                            <option value="">-- Pilih Barang --</option>
                            <?php while($stok = mysqli_fetch_assoc($query_stok)): ?>
                                <option value="<?= $stok['id_barang']; ?>"><?= $stok['nama_barang']; ?> (Sisa: <?= $stok['jumlah_stok']; ?>)</option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Supplier</label>
                        <select name="id_supplier" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                            <option value="">-- Pilih Supplier --</option>
                            <?php while($sup = mysqli_fetch_assoc($query_supplier)): ?>
                                <option value="<?= $sup['id_supplier']; ?>"><?= $sup['nama_supplier']; ?></option>
                            <?php endwhile; ?>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Isi dulu data Supplier di menu Supplier jika kosong.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Masuk</label>
                        <input type="number" name="jumlah_masuk" min="1" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                    </div>
                </div>
                <div class="mt-8 flex gap-3">
                    <button type="button" @click="showAddModal = false" class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 font-medium">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-green-600 text-white rounded-xl hover:bg-green-700 font-medium shadow-sm">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
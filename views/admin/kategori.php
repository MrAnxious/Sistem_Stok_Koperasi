<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}

require '../../config/koneksi.php';

// Ambil semua data kategori beserta jumlah barang yang menggunakannya
$query = "SELECT k.*, COUNT(s.id_barang) as jumlah_barang 
          FROM kategori k 
          LEFT JOIN stok s ON s.kategori = k.nama_kategori 
          GROUP BY k.id_kategori 
          ORDER BY k.nama_kategori ASC";
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
    <title>Kategori Barang - <?= htmlspecialchars($_SETTINGS['nama_sistem'] ?? 'Koperasi Merah Putih') ?></title>
    
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

<body class="text-gray-800 min-h-screen flex" x-data="{ isSidebarOpen: false, showAddModal: false, showEditModal: false, editData: {} }">
    
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
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl bg-primary/10 text-primary font-semibold relative" href="kategori.php">
                <div class="absolute left-0 w-1 h-6 bg-primary rounded-r-full"></div>
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">category</span><span>Kategori Barang</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="barang_masuk.php">
                <span class="material-symbols-outlined">input</span><span>Barang Masuk</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="barang_keluar.php">
                <span class="material-symbols-outlined">output</span><span>Barang Keluar</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="#">
                <span class="material-symbols-outlined">print</span><span>Cetak Laporan</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="supplier.php">
                <span class="material-symbols-outlined">local_shipping</span><span>Supplier</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="log_aktivitas.php">
                <span class="material-symbols-outlined">manage_search</span><span>Log Aktivitas</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="pengaturan.php">
                <span class="material-symbols-outlined">settings</span>
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
            </div>
        </header>

        <div class="flex-1 px-4 sm:px-6 lg:px-12 py-8 overflow-y-auto w-full">
            
            <?php if (isset($_SESSION['pesan'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined">check_circle</span>
                    <span><?= htmlspecialchars($_SESSION['pesan']); ?></span>
                </div>
                <?php unset($_SESSION['pesan']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined">error</span>
                    <span><?= htmlspecialchars($_SESSION['error']); ?></span>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900">Manajemen Kategori</h1>
                    <p class="text-gray-500 mt-1 text-sm">Kelola daftar kategori barang yang tersedia di koperasi.</p>
                </div>
                <button @click="showAddModal = true" class="bg-primary hover:bg-red-800 text-white px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center justify-center gap-2 transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-lg">add</span>
                    Tambah Kategori
                </button>
            </div>

            <!-- Stats Cards -->
            <?php
            $total_kategori = mysqli_num_rows($result);
            mysqli_data_seek($result, 0);
            $total_barang_q = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM stok");
            $total_barang = mysqli_fetch_assoc($total_barang_q)['total'];
            ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">category</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Kategori</p>
                        <p class="text-2xl font-bold text-gray-900"><?= $total_kategori ?></p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 flex-shrink-0">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">inventory_2</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Barang</p>
                        <p class="text-2xl font-bold text-gray-900"><?= $total_barang ?></p>
                    </div>
                </div>
            </div>

            <!-- Tabel Kategori -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm w-full overflow-hidden">
                <div class="p-5 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">list</span>
                        Daftar Kategori Barang
                    </h2>
                </div>
                
                <div class="w-full overflow-x-auto hide-scrollbar">
                    <table class="w-full text-left min-w-[600px]">
                        <thead>
                            <tr class="text-xs font-bold text-gray-500 border-b border-gray-200 uppercase tracking-wider bg-gray-50">
                                <th class="p-4 w-16 text-center">No</th>
                                <th class="p-4">Nama Kategori</th>
                                <th class="p-4">Deskripsi</th>
                                <th class="p-4 text-center">Jumlah Barang</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100">
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php $no = 1; while($row = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 text-center text-gray-500 align-middle"><?= $no++; ?></td>
                                    <td class="p-4 align-middle">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                                <span class="material-symbols-outlined text-primary text-[16px]" style="font-variation-settings: 'FILL' 1;">category</span>
                                            </div>
                                            <span class="font-semibold text-gray-800"><?= htmlspecialchars($row['nama_kategori']); ?></span>
                                        </div>
                                    </td>
                                    <td class="p-4 align-middle text-gray-500"><?= !empty($row['deskripsi']) ? htmlspecialchars($row['deskripsi']) : '<span class="italic text-gray-400">-</span>'; ?></td>
                                    <td class="p-4 text-center align-middle">
                                        <span class="px-3 py-1 font-bold rounded-full text-sm <?= ($row['jumlah_barang'] > 0) ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'; ?>">
                                            <?= $row['jumlah_barang']; ?> barang
                                        </span>
                                    </td>
                                    <td class="p-4 align-middle">
                                        <div class="flex justify-center gap-2 items-center">
                                            <button @click="showEditModal = true; editData = { id: '<?= $row['id_kategori'] ?>', nama: '<?= addslashes($row['nama_kategori']) ?>', deskripsi: '<?= addslashes($row['deskripsi'] ?? '') ?>' }" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                                <span class="material-symbols-outlined text-[20px]">edit</span>
                                            </button>
                                            <?php if ($row['jumlah_barang'] == 0): ?>
                                            <a href="../../proses/crud_kategori.php?aksi=hapus&id=<?= $row['id_kategori']; ?>" onclick="return confirm('Yakin ingin menghapus kategori \'<?= addslashes($row['nama_kategori']) ?>\'?');" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </a>
                                            <?php else: ?>
                                            <button class="p-2 text-gray-300 cursor-not-allowed rounded-lg" title="Tidak bisa dihapus, masih ada barang">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="p-10 text-center text-gray-500">
                                        <span class="material-symbols-outlined text-5xl mb-3 text-gray-300 block">category</span>
                                        <p class="font-medium">Belum ada kategori.</p>
                                        <p class="text-sm text-gray-400 mt-1">Klik "Tambah Kategori" untuk mulai menambahkan kategori baru.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Tambah Kategori -->
    <div x-show="showAddModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4" style="display: none;">
        <div x-show="showAddModal" x-transition.opacity @click="showAddModal = false" class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
        <div x-show="showAddModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="relative bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-[18px]" style="font-variation-settings: 'FILL' 1;">add_circle</span>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900">Tambah Kategori Baru</h3>
                </div>
                <button @click="showAddModal = false" class="text-gray-400 hover:text-red-500 transition-colors"><span class="material-symbols-outlined">close</span></button>
            </div>
            <form action="../../proses/crud_kategori.php?aksi=tambah" method="POST" class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Kategori <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_kategori" required placeholder="Contoh: Sembako, Alat Tulis, Minuman..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi <span class="text-gray-400 text-xs font-normal">(opsional)</span></label>
                        <textarea name="deskripsi" rows="3" placeholder="Tambahkan deskripsi singkat tentang kategori ini..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all resize-none"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex gap-3">
                    <button type="button" @click="showAddModal = false" class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 font-medium transition-colors">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-primary text-white rounded-xl hover:bg-red-800 font-medium shadow-sm transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Kategori -->
    <div x-show="showEditModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4" style="display: none;">
        <div x-show="showEditModal" x-transition.opacity @click="showEditModal = false" class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
        <div x-show="showEditModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="relative bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                        <span class="material-symbols-outlined text-blue-600 text-[18px]" style="font-variation-settings: 'FILL' 1;">edit</span>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900">Edit Kategori</h3>
                </div>
                <button @click="showEditModal = false" class="text-gray-400 hover:text-red-500 transition-colors"><span class="material-symbols-outlined">close</span></button>
            </div>
            <form action="../../proses/crud_kategori.php?aksi=edit" method="POST" class="p-6">
                <input type="hidden" name="id_kategori" x-model="editData.id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Kategori <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_kategori" x-model="editData.nama" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi <span class="text-gray-400 text-xs font-normal">(opsional)</span></label>
                        <textarea name="deskripsi" rows="3" x-model="editData.deskripsi" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all resize-none"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex gap-3">
                    <button type="button" @click="showEditModal = false" class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 font-medium transition-colors">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-medium shadow-sm transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>

<?php
session_start();

// Proteksi halaman: pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}

require '../../config/koneksi.php';

// Ambil semua data stok
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
if (!empty($search)) {
    $query = "SELECT * FROM stok WHERE nama_barang LIKE '%$search%' ORDER BY id_barang DESC";
} else {
    $query = "SELECT * FROM stok ORDER BY id_barang DESC";
}
$result = mysqli_query($koneksi, $query);
// Ambil daftar kategori untuk dropdown
$kategori_list = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    
    <?php if(!empty($_SETTINGS['favicon'])): ?>
        <link rel="icon" type="image/png" href="../../assets/logo_sistem/<?= htmlspecialchars($_SETTINGS['favicon']) ?>">
    <?php endif; ?>
    <title>Stok Barang - <?= htmlspecialchars($_SETTINGS['nama_sistem'] ?? 'Koperasi Merah Putih') ?></title>
    
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

<body class="text-gray-800 min-h-screen flex" x-data="{ isSidebarOpen: false, showAddModal: false, showEditModal: false, showPreview: false, previewUrl: '', editData: {} }">
    
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
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl bg-primary/10 text-primary font-semibold relative" href="stok_barang.php">
                <div class="absolute left-0 w-1 h-6 bg-primary rounded-r-full"></div>
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">inventory_2</span><span>Stok Barang</span>
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
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900">Master Data Barang</h1>
                    <p class="text-gray-500 mt-1 text-sm">Kelola daftar barang yang tersedia di koperasi beserta fotonya.</p>
                </div>
                <button @click="showAddModal = true" class="bg-primary hover:bg-red-800 text-white px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center justify-center gap-2 transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-lg">add</span>
                    Tambah Barang Baru
                </button>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm w-full overflow-hidden">
                <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                    <form method="GET" action="" class="relative w-full max-w-sm">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">search</span>
                        <input name="search" value="<?= htmlspecialchars($search); ?>" class="w-full bg-gray-50 border border-gray-200 rounded-lg pl-10 pr-4 py-2 text-sm focus:ring-1 focus:ring-primary focus:border-primary transition-all" placeholder="Cari nama barang..." type="text"/>
                    </form>
                </div>
                
                <div class="w-full overflow-x-auto hide-scrollbar">
                    <table class="w-full text-left min-w-[800px]">
                        <thead>
                            <tr class="text-xs font-bold text-gray-500 border-b border-gray-200 uppercase tracking-wider bg-gray-50">
                                <th class="p-4 w-16 text-center">ID</th>
                                <th class="p-4 w-32 text-center">Foto</th> 
                                <th class="p-4">Nama Barang</th>
                                <th class="p-4">Kategori</th>
                                <th class="p-4 text-center">Stok Tersedia</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100">
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php $no = 1; while($row = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 text-center text-gray-500 align-middle"><?= $no++; ?></td>
                                    
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
                                    
                                    <td class="p-4 align-middle">
                                        <div class="flex justify-center gap-2 items-center">
                                            <button @click="showEditModal = true; editData = { id: '<?= $row['id_barang'] ?>', nama: '<?= addslashes($row['nama_barang']) ?>', kategori: '<?= $row['kategori'] ?>', foto: '<?= $row['foto'] ?>' }" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                                <span class="material-symbols-outlined text-[20px]">edit</span>
                                            </button>
                                            <a href="../../proses/crud_stok.php?aksi=hapus&id=<?= $row['id_barang']; ?>" onclick="return confirm('Yakin ingin menghapus barang ini beserta fotonya?');" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </a>
                                        </div>
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

    <div x-show="showAddModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4" style="display: none;">
        <div x-show="showAddModal" x-transition.opacity @click="showAddModal = false" class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
        <div x-show="showAddModal" x-transition.scale class="relative bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-lg text-gray-900">Tambah Barang Baru</h3>
                <button @click="showAddModal = false" class="text-gray-400 hover:text-red-500"><span class="material-symbols-outlined">close</span></button>
            </div>
            <form action="../../proses/crud_stok.php?aksi=tambah" method="POST" enctype="multipart/form-data" class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang</label>
                        <input type="text" name="nama_barang" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <select name="kategori" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                            <option value="" disabled selected>-- Pilih Kategori --</option>
                            <?php
                            // Reset pointer kategori_list
                            mysqli_data_seek($kategori_list, 0);
                            while($kat = mysqli_fetch_assoc($kategori_list)):
                            ?>
                            <option value="<?= htmlspecialchars($kat['nama_kategori']) ?>"><?= htmlspecialchars($kat['nama_kategori']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Belum ada kategori? <a href="kategori.php" class="text-primary underline hover:text-red-800">Tambahkan dulu di sini</a>.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Upload Foto Barang</label>
                        <input type="file" name="foto" accept="image/*" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-red-800 transition-all cursor-pointer">
                    </div>
                </div>
                <div class="mt-8 flex gap-3">
                    <button type="button" @click="showAddModal = false" class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 font-medium">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-primary text-white rounded-xl hover:bg-red-800 font-medium shadow-sm">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showEditModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4" style="display: none;">
        <div x-show="showEditModal" x-transition.opacity @click="showEditModal = false" class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
        <div x-show="showEditModal" x-transition.scale class="relative bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-lg text-gray-900">Edit Data Barang</h3>
                <button @click="showEditModal = false" class="text-gray-400 hover:text-red-500"><span class="material-symbols-outlined">close</span></button>
            </div>
            <form action="../../proses/crud_stok.php?aksi=edit" method="POST" enctype="multipart/form-data" class="p-6">
                <input type="hidden" name="id_barang" x-model="editData.id">
                <input type="hidden" name="foto_lama" x-model="editData.foto">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang</label>
                        <input type="text" name="nama_barang" x-model="editData.nama" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <select id="edit-kategori-select" name="kategori" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                            <option value="" disabled>-- Pilih Kategori --</option>
                            <?php
                            mysqli_data_seek($kategori_list, 0);
                            while($kat = mysqli_fetch_assoc($kategori_list)):
                                $kat_nama = htmlspecialchars($kat['nama_kategori']);
                            ?>
                            <option value="<?= $kat_nama ?>" <?php // selected via JS below ?>><?= $kat_nama ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ganti Foto (Opsional)</label>
                        <input type="file" name="foto" accept="image/*" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 transition-all cursor-pointer">
                    </div>
                </div>
                <div class="mt-8 flex gap-3">
                    <button type="button" @click="showEditModal = false" class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 font-medium">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-medium shadow-sm">Update Data</button>
                </div>
            </form>
        </div>
    </div>

<script>
// Auto-set dropdown kategori saat modal edit dibuka
document.addEventListener('alpine:initialized', function() {
    var selectEl = document.getElementById('edit-kategori-select');
    if (!selectEl) return;
    var rootEl = document.querySelector('[x-data]');
    if (!rootEl) return;
    Alpine.effect(function() {
        var editData = Alpine.$data(rootEl).editData;
        if (editData && editData.kategori) {
            selectEl.value = editData.kategori;
        }
    });
});
</script>

</body>
</html>

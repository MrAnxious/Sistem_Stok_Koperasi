<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}

require '../../config/koneksi.php';

// Proses Hapus Supplier
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Cek apakah supplier ini sudah dipakai di transaksi barang masuk
    $cek_trx = mysqli_query($koneksi, "SELECT * FROM barang_masuk WHERE id_supplier='$id'");
    
    if (mysqli_num_rows($cek_trx) > 0) {
        $_SESSION['error'] = 'Gagal menghapus! Supplier ini memiliki riwayat transaksi.';
    } else {
        if (mysqli_query($koneksi, $query_delete)) {
            $nama_supplier = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_supplier FROM supplier WHERE id_supplier='$id'"))['nama_supplier'] ?? "ID $id";
            catat_log($koneksi, $_SESSION['id_user'], "Hapus Supplier", "Menghapus supplier: $nama_supplier");
            $_SESSION['pesan'] = 'Data supplier berhasil dihapus!';
        } else {
            $_SESSION['error'] = 'Terjadi kesalahan sistem saat menghapus data.';
        }
    }
    header("Location: supplier.php");
    exit();
}

// Proses Tambah / Edit Supplier
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_supplier = mysqli_real_escape_string($koneksi, $_POST['nama_supplier']);
    $kontak = mysqli_real_escape_string($koneksi, $_POST['kontak']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    
    if (isset($_POST['id_supplier']) && $_POST['id_supplier'] != '') {
        // Mode Edit
        $id = $_POST['id_supplier'];
        $query = "UPDATE supplier SET nama_supplier='$nama_supplier', no_telepon='$kontak', alamat='$alamat' WHERE id_supplier='$id'";
        $pesan = 'Data supplier berhasil diupdate!';
        $aksi_log = "Edit Supplier";
        $ket_log = "Memperbarui data supplier: $nama_supplier";
    } else {
        // Mode Tambah
        $query = "INSERT INTO supplier (nama_supplier, no_telepon, alamat) VALUES ('$nama_supplier', '$kontak', '$alamat')";
        $pesan = 'Data supplier berhasil ditambahkan!';
        $aksi_log = "Tambah Supplier";
        $ket_log = "Menambahkan supplier baru: $nama_supplier";
    }
    
    if (mysqli_query($koneksi, $query)) {
        catat_log($koneksi, $_SESSION['id_user'], $aksi_log, $ket_log);
        $_SESSION['pesan'] = $pesan;
    } else {
        $_SESSION['error'] = 'Terjadi kesalahan sistem saat menyimpan data.';
    }
    header("Location: supplier.php");
    exit();
}

// Ambil semua data supplier
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
    <title>Manajemen Supplier - <?= htmlspecialchars($_SETTINGS['nama_sistem'] ?? 'Koperasi Merah Putih') ?></title>
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

<body class="text-gray-800 min-h-screen flex" x-data="{ 
    isSidebarOpen: false, 
    showModal: false,
    modalMode: 'add',
    formData: { id: '', nama: '', kontak: '', alamat: '' },
    openAddModal() {
        this.modalMode = 'add';
        this.formData = { id: '', nama: '', kontak: '', alamat: '' };
        this.showModal = true;
    },
    openEditModal(id, nama, kontak, alamat) {
        this.modalMode = 'edit';
        this.formData = { id: id, nama: nama, kontak: kontak, alamat: alamat };
        this.showModal = true;
    }
}">
    
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
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="barang_masuk.php">
                <span class="material-symbols-outlined">input</span><span>Barang Masuk</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="barang_keluar.php">
                <span class="material-symbols-outlined">output</span><span>Barang Keluar</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="cetak_laporan.php">
                <span class="material-symbols-outlined">print</span><span>Cetak Laporan</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl bg-primary/10 text-primary font-semibold relative" href="supplier.php">
                <div class="absolute left-0 w-1 h-6 bg-primary rounded-r-full"></div>
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">local_shipping</span><span>Supplier</span>
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
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined">error</span>
                    <span><?= $_SESSION['error']; ?></span>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900">Manajemen Supplier</h1>
                    <p class="text-gray-500 mt-1 text-sm">Kelola daftar pihak penyedia barang untuk koperasi.</p>
                </div>
                <button @click="openAddModal()" class="bg-primary hover:bg-red-800 text-white px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center justify-center gap-2 transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-lg">add</span>
                    Tambah Supplier
                </button>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm w-full overflow-hidden">
                <div class="w-full overflow-x-auto hide-scrollbar">
                    <table class="w-full text-left min-w-[800px]">
                        <thead>
                            <tr class="text-xs font-bold text-gray-500 border-b border-gray-200 uppercase tracking-wider bg-gray-50">
                                <th class="p-4 w-12 text-center">No</th>
                                <th class="p-4">Nama Supplier</th>
                                <th class="p-4">Kontak</th>
                                <th class="p-4">Alamat</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100">
                            <?php 
                            $no = 1;
                            if (mysqli_num_rows($query_supplier) > 0): 
                                while($row = mysqli_fetch_assoc($query_supplier)): 
                            ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-4 text-center text-gray-500 font-medium"><?= $no++; ?></td>
                                <td class="p-4 font-semibold text-gray-800"><?= htmlspecialchars($row['nama_supplier']); ?></td>
                                <td class="p-4 text-gray-600"><?= htmlspecialchars($row['no_telepon']); ?></td>
                                <td class="p-4 text-gray-600">
                                    <p class="truncate max-w-xs" title="<?= htmlspecialchars($row['alamat']); ?>"><?= htmlspecialchars($row['alamat']); ?></p>
                                </td>
                                <td class="p-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="openEditModal('<?= $row['id_supplier'] ?>', '<?= addslashes($row['nama_supplier']) ?>', '<?= addslashes($row['no_telepon']) ?>', '<?= addslashes($row['alamat']) ?>')" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit Data">
                                            <span class="material-symbols-outlined text-[20px]">edit</span>
                                        </button>
                                        <a href="supplier.php?delete=<?= $row['id_supplier']; ?>" onclick="return confirm('Yakin ingin menghapus supplier ini?');" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Data">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php 
                                endwhile; 
                            else: 
                            ?>
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-gray-500">
                                        <span class="material-symbols-outlined text-4xl mb-2 text-gray-300">local_shipping</span>
                                        <p>Belum ada data supplier yang ditambahkan.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Form (Tambah/Edit) -->
    <div x-show="showModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-0" style="display: none;">
        <div x-show="showModal" x-transition.opacity @click="showModal = false" class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
        <div x-show="showModal" x-transition.scale class="relative bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-lg text-gray-900" x-text="modalMode === 'add' ? 'Tambah Supplier Baru' : 'Edit Data Supplier'"></h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-red-500"><span class="material-symbols-outlined">close</span></button>
            </div>
            
            <form action="" method="POST" class="p-6">
                <!-- Input Hidden ID untuk mode edit -->
                <input type="hidden" name="id_supplier" x-model="formData.id">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Supplier</label>
                        <input type="text" name="nama_supplier" x-model="formData.nama" required placeholder="Contoh: PT. Sembako Makmur" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kontak (No HP / Telepon)</label>
                        <input type="text" name="kontak" x-model="formData.kontak" required placeholder="Contoh: 08123456789" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                        <textarea name="alamat" x-model="formData.alamat" required rows="3" placeholder="Masukkan alamat lengkap..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"></textarea>
                    </div>
                </div>
                
                <div class="mt-8 flex gap-3">
                    <button type="button" @click="showModal = false" class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 font-medium transition-colors">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-primary text-white rounded-xl hover:bg-red-800 font-medium shadow-sm transition-colors">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

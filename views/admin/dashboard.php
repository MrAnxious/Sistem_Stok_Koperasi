<?php
session_start();

// Proteksi halaman: Cek apakah user sudah login dan apakah rolenya admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    
    <?php if(!empty($_SETTINGS['favicon'])): ?>
        <link rel="icon" type="image/png" href="../../assets/logo_sistem/<?= htmlspecialchars($_SETTINGS['favicon']) ?>">
    <?php endif; ?>
    <title><?= htmlspecialchars($_SETTINGS['nama_sistem'] ?? 'Koperasi Merah Putih') ?> - Dashboard Admin</title>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#af101a",
                        "tertiary": "#005f7b",
                        "error": "#ba1a1a",
                        // Warna lainnya disederhanakan untuk contoh, Anda bisa menambahkan config warna sebelumnya jika perlu
                        "surface": "#f8f9fa",
                        "surface-container-low": "#f3f4f5",
                        "surface-container-lowest": "#ffffff",
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
        
        /* Custom scrollbar untuk tabel di mobile */
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>

<body class="text-gray-800 min-h-screen flex" x-data="{ isSidebarOpen: false }">
    
    <div 
        x-show="isSidebarOpen" 
        @click="isSidebarOpen = false"
        x-transition.opacity
        class="fixed inset-0 bg-black/50 z-40 lg:hidden"
    ></div>

    <aside 
        class="fixed inset-y-0 left-0 z-50 w-72 bg-white flex flex-col h-screen transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto shadow-xl lg:shadow-none border-r border-gray-200"
        :class="{'translate-x-0': isSidebarOpen, '-translate-x-full': !isSidebarOpen}"
    >
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
            <button @click="isSidebarOpen = false" class="lg:hidden text-gray-500 hover:text-red-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl bg-primary/10 text-primary font-semibold relative" href="#">
                <div class="absolute left-0 w-1 h-6 bg-primary rounded-r-full"></div>
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">dashboard</span>
                <span>Dashboard</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="../admin/stok_barang.php">
                <span class="material-symbols-outlined">inventory_2</span>
                <span>Stok Barang</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="../admin/barang_masuk.php">
                <span class="material-symbols-outlined">input</span>
                <span>Barang Masuk</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="../admin/barang_keluar.php">
                <span class="material-symbols-outlined">output</span>
                <span>Barang Keluar</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="../admin/cetak_laporan.php">
                <span class="material-symbols-outlined">print</span>
                <span>Cetak Laporan</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors" href="../admin/supplier.php">
                <span class="material-symbols-outlined">local_shipping</span>
                <span>Supplier</span>
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
            <div class="flex items-center gap-4 flex-1">
                <button @click="isSidebarOpen = true" class="lg:hidden p-2 text-gray-600 rounded-md hover:bg-gray-100">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                
            </div>
            
            <div class="flex items-center gap-3 sm:gap-6">
                
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
            
            <div class="mb-8">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-gray-900">Ringkasan Operasional</h1>
                <p class="text-gray-500 mt-2 text-sm sm:text-base">Pantau aktivitas pergudangan <?= htmlspecialchars($_SETTINGS['nama_sistem'] ?? 'Koperasi Merah Putih') ?> secara real-time.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8 lg:mb-12">
                
                <div class="bg-gradient-to-br from-primary to-red-800 p-5 sm:p-6 rounded-2xl text-white flex flex-col justify-between h-36 sm:h-40 lg:h-48 shadow-lg shadow-red-900/20">
                    <div class="flex justify-between items-start">
                        <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">inventory_2</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-white/80 text-xs sm:text-sm font-medium">Total Barang</p>
                        <?php
                            require '../../config/koneksi.php';
                            $query_total = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM stok");
                            $total_barang = mysqli_fetch_assoc($query_total)['total'];
                        ?>
                        <h3 class="text-3xl sm:text-4xl font-bold mt-1"><?= $total_barang ?></h3>
                    </div>
                </div>

                <div class="bg-white p-5 sm:p-6 rounded-2xl border border-gray-100 flex flex-col justify-between h-36 sm:h-40 lg:h-48 shadow-sm">
                    <div class="flex justify-between items-start">
                        <div class="p-2 bg-green-100 rounded-lg text-green-700">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">input</span>
                        </div>
                        <span class="text-[10px] sm:text-xs font-medium px-2 py-1 bg-green-50 rounded-full text-green-700">Hari Ini</span>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs sm:text-sm font-medium">Barang Masuk</p>
                        <?php
                            $tgl_hari_ini = date('Y-m-d');
                            $query_masuk = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM barang_masuk WHERE DATE(tanggal_masuk) = '$tgl_hari_ini'");
                            $masuk_hari_ini = mysqli_fetch_assoc($query_masuk)['total'];
                        ?>
                        <h3 class="text-3xl sm:text-4xl font-bold text-gray-800 mt-1"><?= $masuk_hari_ini ?></h3>
                    </div>
                </div>

                <div class="bg-white p-5 sm:p-6 rounded-2xl border border-gray-100 flex flex-col justify-between h-36 sm:h-40 lg:h-48 shadow-sm">
                    <div class="flex justify-between items-start">
                        <div class="p-2 bg-orange-100 rounded-lg text-orange-700">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">output</span>
                        </div>
                        <span class="text-[10px] sm:text-xs font-medium px-2 py-1 bg-orange-50 rounded-full text-orange-700">Hari Ini</span>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs sm:text-sm font-medium">Barang Keluar</p>
                        <?php
                            $query_keluar = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM barang_keluar WHERE DATE(tanggal_keluar) = '$tgl_hari_ini'");
                            $keluar_hari_ini = mysqli_fetch_assoc($query_keluar)['total'];
                        ?>
                        <h3 class="text-3xl sm:text-4xl font-bold text-gray-800 mt-1"><?= $keluar_hari_ini ?></h3>
                    </div>
                </div>

                <div class="bg-gray-900 p-5 sm:p-6 rounded-2xl flex flex-col justify-between h-36 sm:h-40 lg:h-48 shadow-lg shadow-gray-900/20">
                    <div class="flex justify-between items-start">
                        <div class="p-2 bg-white/10 rounded-lg text-white">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">local_shipping</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs sm:text-sm font-medium">Jumlah Supplier</p>
                        <?php
                            $query_sup = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM supplier");
                            $total_supplier = mysqli_fetch_assoc($query_sup)['total'];
                        ?>
                        <h3 class="text-3xl sm:text-4xl font-bold text-white mt-1"><?= $total_supplier ?></h3>
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 w-full">
                
                <div class="w-full lg:w-2/3 bg-white p-5 sm:p-8 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="mb-6">
                        <h2 class="text-lg sm:text-xl font-bold text-gray-800">Tren Stok Mingguan</h2>
                        <p class="text-gray-500 text-xs sm:text-sm mt-1">Perbandingan jumlah barang masuk dan keluar.</p>
                    </div>
                    
                    <div class="relative w-full h-64 sm:h-72 lg:h-80">
                        <canvas id="stokChart"></canvas>
                    </div>
                </div>

                <div class="w-full lg:w-1/3 flex flex-col gap-6">
                    
                    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-gray-100 shadow-sm flex-1">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-bold text-gray-800">Stok Menipis</h3>
                        </div>
                        <div class="space-y-4">
                            <?php 
                            $query_menipis = mysqli_query($koneksi, "SELECT * FROM stok WHERE jumlah_stok < 10 ORDER BY jumlah_stok ASC LIMIT 2");
                            if (mysqli_num_rows($query_menipis) > 0): 
                                while($stok = mysqli_fetch_assoc($query_menipis)): 
                            ?>
                            <div class="flex items-center gap-3 sm:gap-4 p-3 bg-red-50/50 rounded-xl border border-red-100">
                                <div class="w-10 h-10 rounded-lg bg-red-100 flex-shrink-0 flex items-center justify-center text-red-600">
                                    <span class="material-symbols-outlined">warning</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-800 truncate"><?= htmlspecialchars($stok['nama_barang']) ?></p>
                                    <p class="text-xs text-red-600 font-medium">Sisa: <?= $stok['jumlah_stok'] ?> unit</p>
                                </div>
                            </div>
                            <?php 
                                endwhile; 
                            else: 
                            ?>
                                <p class="text-sm text-gray-500">Tidak ada stok menipis.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="bg-blue-50 p-5 sm:p-6 rounded-2xl border border-blue-100">
                        <h3 class="font-bold text-blue-900">Aksi Cepat</h3>
                        <p class="text-xs text-blue-700 mt-1 mb-4">Input data transaksi baru dengan cepat.</p>
                        <a href="barang_masuk.php" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl text-sm font-semibold flex items-center justify-center gap-2 transition-colors shadow-sm mb-2">
                            <span class="material-symbols-outlined text-lg">add_circle</span>
                            Input Barang Masuk
                        </a>
                        <a href="barang_keluar.php" class="w-full bg-orange-600 hover:bg-orange-700 text-white py-3 rounded-xl text-sm font-semibold flex items-center justify-center gap-2 transition-colors shadow-sm">
                            <span class="material-symbols-outlined text-lg">indeterminate_check_box</span>
                            Input Barang Keluar
                        </a>
                    </div>

                </div>
            </div>

            <div class="mt-8 lg:mt-12 bg-white rounded-2xl p-5 sm:p-8 border border-gray-100 shadow-sm w-full overflow-hidden">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800">Transaksi Terakhir</h2>
                    <form method="GET" action="" class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-gray-400 text-lg">filter_list</span>
                        <select name="filter" onchange="this.form.submit()" class="text-sm text-gray-600 bg-gray-50 border border-gray-200 px-3 py-2 rounded-lg cursor-pointer focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all">
                            <option value="semua" <?= (isset($_GET['filter']) && $_GET['filter'] == 'semua') ? 'selected' : ''; ?>>Semua Transaksi</option>
                            <option value="masuk" <?= (isset($_GET['filter']) && $_GET['filter'] == 'masuk') ? 'selected' : ''; ?>>Barang Masuk</option>
                            <option value="keluar" <?= (isset($_GET['filter']) && $_GET['filter'] == 'keluar') ? 'selected' : ''; ?>>Barang Keluar</option>
                        </select>
                    </form>
                </div>
                
                <div class="w-full overflow-x-auto hide-scrollbar rounded-lg">
                    <table class="w-full text-left min-w-[700px]">
                        <thead>
                            <tr class="text-xs font-bold text-gray-500 border-b border-gray-200 uppercase tracking-wider bg-gray-50">
                                <th class="p-4 rounded-tl-lg">No TRX</th>
                                <th class="p-4">Nama Barang</th>
                                <th class="p-4">Kategori</th>
                                <th class="p-4">Tipe</th>
                                <th class="p-4">Jumlah</th>
                                <th class="p-4 text-right rounded-tr-lg">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100">
                            <?php 
                            $filter = isset($_GET['filter']) ? $_GET['filter'] : 'semua';
                            $where_clause = "";
                            if ($filter == 'masuk') {
                                $where_clause = "WHERE tipe = 'MASUK'";
                            } elseif ($filter == 'keluar') {
                                $where_clause = "WHERE tipe = 'KELUAR'";
                            }
                            
                            $query_trx_terakhir = "
                                SELECT * FROM (
                                    (SELECT 'MASUK' as tipe, id_masuk as id_trx, bm.tanggal_masuk as waktu, s.nama_barang, s.kategori, bm.jumlah_masuk as jumlah 
                                     FROM barang_masuk bm JOIN stok s ON bm.id_barang = s.id_barang)
                                    UNION ALL
                                    (SELECT 'KELUAR' as tipe, id_keluar as id_trx, bk.tanggal_keluar as waktu, s.nama_barang, s.kategori, bk.jumlah_keluar as jumlah 
                                     FROM barang_keluar bk JOIN stok s ON bk.id_barang = s.id_barang)
                                ) as trx_all
                                $where_clause
                                ORDER BY waktu DESC LIMIT 5
                            ";
                            $result_trx = mysqli_query($koneksi, $query_trx_terakhir);
                            
                            if (mysqli_num_rows($result_trx) > 0): 
                                while($row = mysqli_fetch_assoc($result_trx)):
                                    $prefix = ($row['tipe'] == 'MASUK') ? 'TM' : 'TK';
                                    $no_trx = $prefix . str_pad($row['id_trx'], 3, '0', STR_PAD_LEFT);
                            ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-4 font-semibold text-gray-800"><?= $no_trx ?></td>
                                <td class="p-4"><?= htmlspecialchars($row['nama_barang']) ?></td>
                                <td class="p-4"><span class="px-3 py-1 bg-gray-100 rounded-md text-xs font-medium text-gray-600"><?= htmlspecialchars($row['kategori']) ?></span></td>
                                <td class="p-4">
                                    <?php if ($row['tipe'] == 'MASUK'): ?>
                                        <span class="px-3 py-1 bg-green-100 text-green-700 font-bold rounded-md text-xs">MASUK</span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 bg-orange-100 text-orange-700 font-bold rounded-md text-xs">KELUAR</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4 font-bold text-gray-800"><?= $row['jumlah'] ?></td>
                                <td class="p-4 text-right text-gray-500"><?= date('d M, H:i', strtotime($row['waktu'])) ?></td>
                            </tr>
                            <?php 
                                endwhile;
                            else:
                            ?>
                                <tr>
                                    <td colspan="6" class="p-4 text-center text-gray-500">Belum ada transaksi</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <?php
    // Query Data Chart Mingguan
    // Inisialisasi array 7 hari (Senin - Minggu) dengan nilai 0
    $data_masuk_mingguan = array_fill(1, 7, 0); 
    $data_keluar_mingguan = array_fill(1, 7, 0);

    // Ambil data barang masuk 7 hari terakhir
    $query_chart_masuk = mysqli_query($koneksi, "
        SELECT DAYOFWEEK(tanggal_masuk) as hari, SUM(jumlah_masuk) as total 
        FROM barang_masuk 
        WHERE tanggal_masuk >= DATE(NOW() - INTERVAL 6 DAY) 
        GROUP BY hari
    ");
    while ($row = mysqli_fetch_assoc($query_chart_masuk)) {
        // MySQL DAYOFWEEK: 1=Minggu, 2=Senin, ..., 7=Sabtu
        // Kita convert ke index 1=Senin, ..., 7=Minggu
        $hari_idx = $row['hari'] - 1;
        if ($hari_idx == 0) $hari_idx = 7;
        $data_masuk_mingguan[$hari_idx] += (int)$row['total'];
    }

    // Ambil data barang keluar 7 hari terakhir
    $query_chart_keluar = mysqli_query($koneksi, "
        SELECT DAYOFWEEK(tanggal_keluar) as hari, SUM(jumlah_keluar) as total 
        FROM barang_keluar 
        WHERE tanggal_keluar >= DATE(NOW() - INTERVAL 6 DAY) 
        GROUP BY hari
    ");
    while ($row = mysqli_fetch_assoc($query_chart_keluar)) {
        $hari_idx = $row['hari'] - 1;
        if ($hari_idx == 0) $hari_idx = 7;
        $data_keluar_mingguan[$hari_idx] += (int)$row['total'];
    }

    // Format data untuk Javascript
    $chart_categories = "['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']";
    $chart_data_masuk = "[" . implode(",", $data_masuk_mingguan) . "]";
    $chart_data_keluar = "[" . implode(",", $data_keluar_mingguan) . "]";
    ?>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('stokChart').getContext('2d');
            
            // Konfigurasi Chart
            new Chart(ctx, {
                type: 'bar', // Menggunakan grafik batang
                data: {
                    labels: <?= $chart_categories ?>, // Label Sumbu X
                    datasets: [
                        {
                            label: 'Barang Masuk',
                            data: <?= $chart_data_masuk ?>, // Data Dinamis
                            backgroundColor: '#af101a', // Warna Merah Utama Koperasi
                            borderRadius: 6,
                            barPercentage: 0.6,
                            categoryPercentage: 0.8
                        },
                        {
                            label: 'Barang Keluar',
                            data: <?= $chart_data_keluar ?>, // Data Dinamis
                            backgroundColor: '#3b82f6', // Warna Biru Tailwind (blue-500)
                            borderRadius: 6,
                            barPercentage: 0.6,
                            categoryPercentage: 0.8
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // Penting agar tinggi chart mengikuti container CSS
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 8,
                                font: { family: "'Inter', sans-serif", size: 12 }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                            titleFont: { family: "'Inter', sans-serif", size: 13 },
                            bodyFont: { family: "'Inter', sans-serif", size: 13 },
                            padding: 12,
                            cornerRadius: 8,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f3f4f6', // Warna garis tipis
                                drawBorder: false,
                            },
                            ticks: {
                                font: { family: "'Inter', sans-serif", size: 11 },
                                color: '#6b7280'
                            }
                        },
                        x: {
                            grid: {
                                display: false, // Hilangkan garis vertikal
                                drawBorder: false,
                            },
                            ticks: {
                                font: { family: "'Inter', sans-serif", size: 11 },
                                color: '#6b7280'
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
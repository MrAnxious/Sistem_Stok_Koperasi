<?php
session_start();
require '../config/koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_user'])) {
    echo json_echo(['sukses' => false, 'pesan' => 'Belum login']);
    exit();
}

try {
    $notifikasi = [];
    $unread_count = 0;
    
    // 1. Cek Stok Menipis (< 10)
    $query_stok = mysqli_query($koneksi, "SELECT * FROM stok WHERE jumlah_stok < 10 ORDER BY jumlah_stok ASC");
    while ($row = mysqli_fetch_assoc($query_stok)) {
        $notifikasi[] = [
            'tipe' => 'stok_low',
            'icon' => 'warning',
            'warna' => 'text-red-600',
            'bg' => 'bg-red-100',
            'judul' => 'Stok Menipis',
            'pesan' => "Sisa {$row['jumlah_stok']} unit untuk barang: {$row['nama_barang']}",
            'waktu' => 'Saat ini',
            'link' => '../admin/stok_barang.php'
        ];
        $unread_count++;
    }

    // 2. Cek Transaksi Masuk Hari Ini
    $tgl_hari_ini = date('Y-m-d');
    $query_masuk = mysqli_query($koneksi, "
        SELECT bm.jumlah_masuk, s.nama_barang, bm.tanggal_masuk 
        FROM barang_masuk bm 
        JOIN stok s ON bm.id_barang = s.id_barang 
        WHERE DATE(bm.tanggal_masuk) = '$tgl_hari_ini' 
        ORDER BY bm.tanggal_masuk DESC
    ");
    while ($row = mysqli_fetch_assoc($query_masuk)) {
        $notifikasi[] = [
            'tipe' => 'barang_masuk',
            'icon' => 'input',
            'warna' => 'text-green-600',
            'bg' => 'bg-green-100',
            'judul' => 'Barang Masuk',
            'pesan' => "Masuk {$row['jumlah_masuk']} unit {$row['nama_barang']}",
            'waktu' => date('H:i', strtotime($row['tanggal_masuk'])),
            'link' => '../admin/barang_masuk.php'
        ];
        $unread_count++;
    }

    // 3. Cek Transaksi Keluar Hari Ini
    $query_keluar = mysqli_query($koneksi, "
        SELECT bk.jumlah_keluar, s.nama_barang, bk.tanggal_keluar 
        FROM barang_keluar bk 
        JOIN stok s ON bk.id_barang = s.id_barang 
        WHERE DATE(bk.tanggal_keluar) = '$tgl_hari_ini' 
        ORDER BY bk.tanggal_keluar DESC
    ");
    while ($row = mysqli_fetch_assoc($query_keluar)) {
        $notifikasi[] = [
            'tipe' => 'barang_keluar',
            'icon' => 'output',
            'warna' => 'text-orange-600',
            'bg' => 'bg-orange-100',
            'judul' => 'Barang Keluar',
            'pesan' => "Keluar {$row['jumlah_keluar']} unit {$row['nama_barang']}",
            'waktu' => date('H:i', strtotime($row['tanggal_keluar'])),
            'link' => '../admin/barang_keluar.php'
        ];
        $unread_count++;
    }

    // Urutkan notifikasi (meskipun dicampur, kita tampilkan semua yang relevan)
    // Jika terlalu banyak, potong jadi 10 max
    $notifikasi = array_slice($notifikasi, 0, 10);

    echo json_encode([
        'sukses' => true,
        'count' => $unread_count,
        'data' => $notifikasi
    ]);

} catch (Exception $e) {
    echo json_encode(['sukses' => false, 'pesan' => $e->getMessage()]);
}
?>

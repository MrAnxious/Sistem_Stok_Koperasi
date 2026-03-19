<?php
session_start();
require '../config/koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_barang = mysqli_real_escape_string($koneksi, $_POST['id_barang']);
    $jumlah_keluar = mysqli_real_escape_string($koneksi, $_POST['jumlah_keluar']);
    $keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    $id_user = $_SESSION['id_user'];
    $tanggal_keluar = date('Y-m-d H:i:s'); // Menggunakan waktu sekarang

    // Validasi input
    if (empty($id_barang) || empty($jumlah_keluar) || empty($keterangan)) {
        $_SESSION['error'] = "Semua field harus diisi.";
        header("Location: ../views/admin/barang_keluar.php");
        exit();
    }
    
    // Mulai transaction untuk memastikan kedua query berhasil atau gagal bersamaan
    mysqli_begin_transaction($koneksi);
    
    try {
        // Cek dulu stok yang tersedia
        $query_cek = "SELECT jumlah_stok, nama_barang FROM stok WHERE id_barang = '$id_barang'";
        $result_cek = mysqli_query($koneksi, $query_cek);
        $data_stok = mysqli_fetch_assoc($result_cek);
        
        if (!$data_stok) {
            throw new Exception("Barang tidak ditemukan.");
        }
        
        $stok_tersedia = (int) $data_stok['jumlah_stok'];
        $jumlah_diminta = (int) $jumlah_keluar;
        
        if ($stok_tersedia < $jumlah_diminta) {
            throw new Exception("Stok tidak mencukupi untuk " . $data_stok['nama_barang'] . ". Sisa stok: " . $stok_tersedia);
        }

        // 1. Insert ke tabel barang_keluar
        $query_insert = "INSERT INTO barang_keluar (id_barang, jumlah_keluar, tanggal_keluar, keterangan, id_user) 
                         VALUES ('$id_barang', '$jumlah_keluar', '$tanggal_keluar', '$keterangan', '$id_user')";
        
        if (!mysqli_query($koneksi, $query_insert)) {
            throw new Exception("Gagal menyimpan data barang keluar: " . mysqli_error($koneksi));
        }
        
        // 2. Update jumlah_stok di tabel stok
        $query_update = "UPDATE stok SET jumlah_stok = jumlah_stok - $jumlah_keluar WHERE id_barang = '$id_barang'";
        
        if (!mysqli_query($koneksi, $query_update)) {
            throw new Exception("Gagal mengurangi stok barang: " . mysqli_error($koneksi));
        }
        
        // Jika semua berhasil, commit
        mysqli_commit($koneksi);
        
        // Catat Log
        $nama_barang = $data_stok['nama_barang'];
        catat_log($koneksi, $id_user, "Input Barang Keluar", "Mengeluarkan $jumlah_keluar unit $nama_barang");
        
        $_SESSION['pesan'] = "Barang keluar berhasil dicatat dan stok telah dikurangi.";
        header("Location: ../views/admin/barang_keluar.php");
        exit();
        
    } catch (Exception $e) {
        // Jika ada yang gagal, rollback
        mysqli_rollback($koneksi);
        
        $_SESSION['error'] = $e->getMessage();
        header("Location: ../views/admin/barang_keluar.php");
        exit();
    }
} else {
    header("Location: ../views/admin/barang_keluar.php");
    exit();
}
?>

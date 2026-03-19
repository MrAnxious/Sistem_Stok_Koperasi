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
    $id_supplier = mysqli_real_escape_string($koneksi, $_POST['id_supplier']);
    $jumlah_masuk = mysqli_real_escape_string($koneksi, $_POST['jumlah_masuk']);
    $id_user = $_SESSION['id_user'];
    $tanggal_masuk = date('Y-m-d H:i:s'); // Menggunakan waktu sekarang

    // Validasi input
    if (empty($id_barang) || empty($id_supplier) || empty($jumlah_masuk)) {
        $_SESSION['error'] = "Semua field harus diisi.";
        header("Location: ../views/admin/barang_masuk.php");
        exit();
    }
    
    // Mulai transaction untuk memastikan kedua query berhasil atau gagal bersamaan
    mysqli_begin_transaction($koneksi);
    
    try {
        // 1. Insert ke tabel barang_masuk
        $query_insert = "INSERT INTO barang_masuk (id_barang, id_supplier, jumlah_masuk, tanggal_masuk, id_user) 
                         VALUES ('$id_barang', '$id_supplier', '$jumlah_masuk', '$tanggal_masuk', '$id_user')";
        
        if (!mysqli_query($koneksi, $query_insert)) {
            throw new Exception("Gagal menyimpan data barang masuk: " . mysqli_error($koneksi));
        }
        
        // 2. Update jumlah_stok di tabel stok
        $query_update = "UPDATE stok SET jumlah_stok = jumlah_stok + $jumlah_masuk WHERE id_barang = '$id_barang'";
        
        if (!mysqli_query($koneksi, $query_update)) {
            throw new Exception("Gagal mengupdate stok barang: " . mysqli_error($koneksi));
        }
        
        // Jika semua berhasil, commit
        mysqli_commit($koneksi);
        
        // Catat Log
        $nama_barang = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_barang FROM stok WHERE id_barang='$id_barang'"))['nama_barang'];
        catat_log($koneksi, $id_user, "Input Barang Masuk", "Memasukkan $jumlah_masuk unit $nama_barang");
        
        $_SESSION['pesan'] = "Barang masuk berhasil dicatat dan stok telah ditambahkan.";
        header("Location: ../views/admin/barang_masuk.php");
        exit();
        
    } catch (Exception $e) {
        // Jika ada yang gagal, rollback
        mysqli_rollback($koneksi);
        
        $_SESSION['error'] = $e->getMessage();
        header("Location: ../views/admin/barang_masuk.php");
        exit();
    }
} else {
    header("Location: ../views/admin/barang_masuk.php");
    exit();
}
?>

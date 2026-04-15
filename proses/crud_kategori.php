<?php
session_start();
require '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';

// 1. PROSES TAMBAH KATEGORI
if ($aksi == 'tambah' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kategori = mysqli_real_escape_string($koneksi, trim($_POST['nama_kategori']));
    $deskripsi     = mysqli_real_escape_string($koneksi, trim($_POST['deskripsi'] ?? ''));

    if (empty($nama_kategori)) {
        $_SESSION['error'] = "Nama kategori tidak boleh kosong.";
        header("Location: ../views/admin/kategori.php");
        exit();
    }

    // Cek duplikat
    $cek = mysqli_query($koneksi, "SELECT id_kategori FROM kategori WHERE nama_kategori = '$nama_kategori'");
    if (mysqli_num_rows($cek) > 0) {
        $_SESSION['error'] = "Kategori '$nama_kategori' sudah ada!";
        header("Location: ../views/admin/kategori.php");
        exit();
    }

    $query = "INSERT INTO kategori (nama_kategori, deskripsi) VALUES ('$nama_kategori', '$deskripsi')";
    if (mysqli_query($koneksi, $query)) {
        catat_log($koneksi, $_SESSION['id_user'], "Tambah Kategori", "Menambahkan kategori: $nama_kategori");
        $_SESSION['pesan'] = "Kategori '$nama_kategori' berhasil ditambahkan!";
    } else {
        $_SESSION['error'] = "Gagal menambahkan kategori.";
    }
    header("Location: ../views/admin/kategori.php");
    exit();
}

// 2. PROSES EDIT KATEGORI
elseif ($aksi == 'edit' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_kategori   = mysqli_real_escape_string($koneksi, $_POST['id_kategori']);
    $nama_kategori = mysqli_real_escape_string($koneksi, trim($_POST['nama_kategori']));
    $deskripsi     = mysqli_real_escape_string($koneksi, trim($_POST['deskripsi'] ?? ''));

    if (empty($nama_kategori)) {
        $_SESSION['error'] = "Nama kategori tidak boleh kosong.";
        header("Location: ../views/admin/kategori.php");
        exit();
    }

    // Cek duplikat (kecuali diri sendiri)
    $cek = mysqli_query($koneksi, "SELECT id_kategori FROM kategori WHERE nama_kategori = '$nama_kategori' AND id_kategori != '$id_kategori'");
    if (mysqli_num_rows($cek) > 0) {
        $_SESSION['error'] = "Kategori '$nama_kategori' sudah ada!";
        header("Location: ../views/admin/kategori.php");
        exit();
    }

    $query = "UPDATE kategori SET nama_kategori = '$nama_kategori', deskripsi = '$deskripsi' WHERE id_kategori = '$id_kategori'";
    if (mysqli_query($koneksi, $query)) {
        catat_log($koneksi, $_SESSION['id_user'], "Edit Kategori", "Memperbarui kategori ID $id_kategori menjadi: $nama_kategori");
        $_SESSION['pesan'] = "Kategori berhasil diperbarui!";
    } else {
        $_SESSION['error'] = "Gagal memperbarui kategori.";
    }
    header("Location: ../views/admin/kategori.php");
    exit();
}

// 3. PROSES HAPUS KATEGORI
elseif ($aksi == 'hapus' && isset($_GET['id'])) {
    $id_kategori = mysqli_real_escape_string($koneksi, $_GET['id']);

    // Ambil nama kategori
    $q = mysqli_query($koneksi, "SELECT nama_kategori FROM kategori WHERE id_kategori = '$id_kategori'");
    $data = mysqli_fetch_assoc($q);
    $nama_kategori = $data['nama_kategori'] ?? "ID $id_kategori";

    // Cek apakah kategori masih dipakai di tabel stok
    $cek_stok = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM stok WHERE kategori = '$nama_kategori'");
    $jml = mysqli_fetch_assoc($cek_stok)['total'];
    if ($jml > 0) {
        $_SESSION['error'] = "Kategori '$nama_kategori' tidak bisa dihapus karena masih digunakan oleh $jml barang.";
        header("Location: ../views/admin/kategori.php");
        exit();
    }

    $query = "DELETE FROM kategori WHERE id_kategori = '$id_kategori'";
    if (mysqli_query($koneksi, $query)) {
        catat_log($koneksi, $_SESSION['id_user'], "Hapus Kategori", "Menghapus kategori: $nama_kategori");
        $_SESSION['pesan'] = "Kategori '$nama_kategori' berhasil dihapus.";
    } else {
        $_SESSION['error'] = "Gagal menghapus kategori.";
    }
    header("Location: ../views/admin/kategori.php");
    exit();
} else {
    header("Location: ../views/admin/kategori.php");
    exit();
}
?>

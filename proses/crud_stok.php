<?php
session_start();
require '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';
$target_dir = "../uploads/barang/"; // Folder tempat menyimpan gambar

// 1. PROSES TAMBAH BARANG
if ($aksi == 'tambah' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_barang = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
    $kategori    = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $nama_file_baru = "";

    // Cek apakah ada file foto yang diupload
    if(isset($_FILES["foto"]) && $_FILES["foto"]["error"] == 0) {
        $ekstensi = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
        // Buat nama file unik agar tidak bentrok jika nama fotonya sama
        $nama_file_baru = time() . "_" . uniqid() . "." . $ekstensi; 
        move_uploaded_file($_FILES["foto"]["tmp_name"], $target_dir . $nama_file_baru);
    }

    $query = "INSERT INTO stok (nama_barang, kategori, foto, jumlah_stok) VALUES ('$nama_barang', '$kategori', '$nama_file_baru', 0)";
    
    if (mysqli_query($koneksi, $query)) {
        catat_log($koneksi, $_SESSION['id_user'], "Tambah Barang Baru", "Menambahkan barang baru: $nama_barang");
        $_SESSION['pesan'] = "Barang '$nama_barang' berhasil ditambahkan!";
    }
    header("Location: ../views/admin/stok_barang.php");
    exit();
}

// 2. PROSES EDIT BARANG
elseif ($aksi == 'edit' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_barang   = mysqli_real_escape_string($koneksi, $_POST['id_barang']);
    $nama_barang = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
    $kategori    = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $foto_lama   = mysqli_real_escape_string($koneksi, $_POST['foto_lama']);

    $nama_file_baru = $foto_lama; // Default gunakan foto lama

    // Cek apakah admin mengupload foto baru saat diedit
    if(isset($_FILES["foto"]) && $_FILES["foto"]["error"] == 0) {
        $ekstensi = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
        $nama_file_baru = time() . "_" . uniqid() . "." . $ekstensi; 
        
        // Pindahkan foto baru
        if(move_uploaded_file($_FILES["foto"]["tmp_name"], $target_dir . $nama_file_baru)) {
            // Hapus foto lama dari server jika ada dan bukan file kosong
            if(!empty($foto_lama) && file_exists($target_dir . $foto_lama)) {
                unlink($target_dir . $foto_lama);
            }
        }
    }

    $query = "UPDATE stok SET nama_barang = '$nama_barang', kategori = '$kategori', foto = '$nama_file_baru' WHERE id_barang = '$id_barang'";
    
    if (mysqli_query($koneksi, $query)) {
        catat_log($koneksi, $_SESSION['id_user'], "Edit Barang", "Memperbarui data barang: $nama_barang");
        $_SESSION['pesan'] = "Data barang berhasil diperbarui!";
    }
    header("Location: ../views/admin/stok_barang.php");
    exit();
}

// 3. PROSES HAPUS BARANG
elseif ($aksi == 'hapus' && isset($_GET['id'])) {
    $id_barang = mysqli_real_escape_string($koneksi, $_GET['id']);
    
    // Ambil nama foto sebelum dihapus untuk dihapus juga dari server
    $query_foto = mysqli_query($koneksi, "SELECT foto FROM stok WHERE id_barang = '$id_barang'");
    $data_foto = mysqli_fetch_assoc($query_foto);
    $foto_yang_dihapus = $data_foto['foto'];

    $query = "DELETE FROM stok WHERE id_barang = '$id_barang'";
    
    if (mysqli_query($koneksi, $query)) {
        // Hapus file fisiknya
        if(!empty($foto_yang_dihapus) && file_exists($target_dir . $foto_yang_dihapus)) {
            unlink($target_dir . $foto_yang_dihapus);
        }
        $nama_barang = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_barang FROM stok WHERE id_barang = '$id_barang'"))['nama_barang'] ?? "ID $id_barang";
        catat_log($koneksi, $_SESSION['id_user'], "Hapus Barang", "Menghapus barang: $nama_barang");
        $_SESSION['pesan'] = "Data barang dan fotonya berhasil dihapus.";
    }
    header("Location: ../views/admin/stok_barang.php");
    exit();
} else {
    header("Location: ../views/admin/stok_barang.php");
    exit();
}
?>
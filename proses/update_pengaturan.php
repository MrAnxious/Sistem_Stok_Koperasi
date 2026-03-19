<?php
session_start();

// Proteksi halaman: pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_sistem = mysqli_real_escape_string($koneksi, $_POST['nama_sistem']);
    $id_user = $_SESSION['id_user'];
    
    // Ambil data pengaturan saat ini
    $query_current = mysqli_query($koneksi, "SELECT * FROM pengaturan LIMIT 1");
    $current_settings = mysqli_fetch_assoc($query_current);
    
    $logo_lama = $current_settings ? $current_settings['logo'] : '';
    $favicon_lama = $current_settings ? $current_settings['favicon'] : '';
    
    $logo_baru = $logo_lama;
    $favicon_baru = $favicon_lama;
    
    $upload_dir = '../assets/logo_sistem/';
    
    // Pastikan direktori ada
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $pesan_error = '';

    // Proses upload Logo
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $logo_tmp = $_FILES['logo']['tmp_name'];
        $logo_name = $_FILES['logo']['name'];
        $logo_size = $_FILES['logo']['size'];
        
        $ext = strtolower(pathinfo($logo_name, PATHINFO_EXTENSION));
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        
        if (!in_array($ext, $allowed)) {
            $pesan_error .= "Format logo tidak valid. ";
        } elseif ($logo_size > 2097152) { // 2MB
            $pesan_error .= "Ukuran logo terlalu besar (Max 2MB). ";
        } else {
            $logo_baru = 'logo_' . time() . '.' . $ext;
            if (move_uploaded_file($logo_tmp, $upload_dir . $logo_baru)) {
                // Hapus logo lama jika ada
                if (!empty($logo_lama) && file_exists($upload_dir . $logo_lama)) {
                    unlink($upload_dir . $logo_lama);
                }
            } else {
                $pesan_error .= "Gagal mengunggah logo. ";
            }
        }
    }

    // Proses upload Favicon
    if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
        $fav_tmp = $_FILES['favicon']['tmp_name'];
        $fav_name = $_FILES['favicon']['name'];
        $fav_size = $_FILES['favicon']['size'];
        
        $ext = strtolower(pathinfo($fav_name, PATHINFO_EXTENSION));
        $allowed = array('jpg', 'jpeg', 'png', 'ico');
        
        if (!in_array($ext, $allowed)) {
            $pesan_error .= "Format favicon tidak valid. ";
        } elseif ($fav_size > 1048576) { // 1MB
            $pesan_error .= "Ukuran favicon terlalu besar (Max 1MB). ";
        } else {
            $favicon_baru = 'fav_' . time() . '.' . $ext;
            if (move_uploaded_file($fav_tmp, $upload_dir . $favicon_baru)) {
                // Hapus favicon lama jika ada
                if (!empty($favicon_lama) && file_exists($upload_dir . $favicon_lama)) {
                    unlink($upload_dir . $favicon_lama);
                }
            } else {
                $pesan_error .= "Gagal mengunggah favicon. ";
            }
        }
    }

    if (!empty($pesan_error)) {
        $_SESSION['pesan_pengaturan'] = "Gagal: " . $pesan_error;
        header("Location: ../views/admin/pengaturan.php");
        exit();
    }

    // Update pengaturan ke database
    $query = "UPDATE pengaturan SET nama_sistem = '$nama_sistem', logo = '$logo_baru', favicon = '$favicon_baru' WHERE id = 1";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        catat_log($koneksi, $id_user, 'UPDATE_PENGATURAN', 'Mengubah pengaturan sistem');
        $_SESSION['pesan_pengaturan'] = "Pengaturan sistem berhasil diperbarui!";
    } else {
        $_SESSION['pesan_pengaturan'] = "Gagal memperbarui pengaturan sistem di database.";
    }

    header("Location: ../views/admin/pengaturan.php");
    exit();
} else {
    header("Location: ../views/admin/pengaturan.php");
    exit();
}
?>

<?php
session_start();

if (!isset($_SESSION['role'])) {
    header("Location: ../index.php");
    exit();
}

require '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = $_POST['id_user'];
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];
    $k_password = $_POST['k_password'];
    $foto_lama = $_POST['foto_lama'];
    
    // Proses Upload Foto Baru (jika ada)
    $nama_foto = $foto_lama;
    if (isset($_FILES['foto']['name']) && $_FILES['foto']['name'] != '') {
        $nama_file = $_FILES['foto']['name'];
        $tmp_file = $_FILES['foto']['tmp_name'];
        $ext = pathinfo($nama_file, PATHINFO_EXTENSION);
        $nama_foto_baru = "profil_" . $id_user . "_" . time() . "." . $ext;
        
        $folder_tujuan = ($_SESSION['role'] === 'admin') ? '../assets/admin/' : '../assets/kepala/';
        
        // Hapus foto lama jika ada dan bukan kosong
        if (!empty($foto_lama) && file_exists($folder_tujuan . $foto_lama)) {
            unlink($folder_tujuan . $foto_lama);
        }
        
        // Pindahkan foto baru
        move_uploaded_file($tmp_file, $folder_tujuan . $nama_foto_baru);
        $nama_foto = $nama_foto_baru;
    }

    // Cek apakah username sudah dipakai oleh user lain
    $cek_username = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username' AND id_user != '$id_user'");
    if (mysqli_num_rows($cek_username) > 0) {
        $_SESSION['pesan_profil'] = "Gagal: Username sudah digunakan oleh pengguna lain.";
    } else {
        if (!empty($password)) {
            if ($password === $k_password) {
                // Update dengan password
                $password_hash = md5($password);
                $query = "UPDATE user SET username='$username', password='$password_hash', foto='$nama_foto' WHERE id_user='$id_user'";
                if (mysqli_query($koneksi, $query)) {
                    catat_log($koneksi, $id_user, "Edit Profil", "Memperbarui profil, foto dan kata sandi");
                    $_SESSION['username'] = $username;
                    $_SESSION['foto'] = $nama_foto;
                    $_SESSION['pesan_profil'] = "Profil dan kata sandi berhasil diperbarui.";
                } else {
                    $_SESSION['pesan_profil'] = "Gagal memperbarui profil: " . mysqli_error($koneksi);
                }
            } else {
                $_SESSION['pesan_profil'] = "Gagal: Konfirmasi password tidak cocok.";
            }
        } else {
            // Update tanpa password
            $query = "UPDATE user SET username='$username', foto='$nama_foto' WHERE id_user='$id_user'";
            if (mysqli_query($koneksi, $query)) {
                catat_log($koneksi, $id_user, "Edit Profil", "Memperbarui data profil dan foto");
                $_SESSION['username'] = $username;
                $_SESSION['foto'] = $nama_foto;
                $_SESSION['pesan_profil'] = "Profil berhasil diperbarui.";
            } else {
                $_SESSION['pesan_profil'] = "Gagal memperbarui profil: " . mysqli_error($koneksi);
            }
        }
    }

    if ($_SESSION['role'] === 'admin') {
        header("Location: ../views/admin/profil.php");
    } else {
        header("Location: ../views/kepala/profil.php");
    }
    exit();
}
?>

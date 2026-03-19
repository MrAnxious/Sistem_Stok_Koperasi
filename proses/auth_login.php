<?php
// 1. Mulai session untuk menyimpan data user yang berhasil login
session_start();

// 2. Hubungkan ke database
require '../config/koneksi.php';

// 3. Pastikan data dikirim melalui metode POST dari form login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil inputan user dan bersihkan untuk mencegah serangan SQL Injection
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    // Karena di database menggunakan MD5, kita enkripsi juga password inputan ini
    $password_md5 = md5($password);

    // 4. Query untuk mencari user di database
    $query = "SELECT * FROM user WHERE username = '$username' AND password = '$password_md5'";
    $result = mysqli_query($koneksi, $query);

    // 5. Cek apakah ada kecocokan data (jika baris yang ditemukan lebih dari 0)
    if (mysqli_num_rows($result) > 0) {
        
        // Ambil data user tersebut
        $data_user = mysqli_fetch_assoc($result);

        // Simpan data penting ke dalam Session
        $_SESSION['id_user']  = $data_user['id_user'];
        $_SESSION['username'] = $data_user['username'];
        $_SESSION['role']     = $data_user['role'];
        $_SESSION['foto']     = $data_user['foto'];

        // 6. Arahkan ke dashboard masing-masing sesuai role
        if ($data_user['role'] == 'admin') {
            header("Location: ../views/admin/dashboard.php");
            exit();
        } elseif ($data_user['role'] == 'kepala') {
            header("Location: ../views/kepala/dashboard.php");
            exit();
        }

    } else {
        // Jika salah, buat pesan error dan kembalikan ke halaman login (index.php)
        // Pesan ini akan ditangkap oleh desain Tailwind warna merah yang sudah kita buat
        $_SESSION['error'] = "Username atau kata sandi salah!";
        header("Location: ../index.php");
        exit();
    }
} else {
    // Jika seseorang iseng membuka file ini langsung dari URL tanpa mengisi form
    header("Location: ../index.php");
    exit();
}
?>
<?php
// Memulai session untuk mengecek status login
session_start();

// Cek apakah user sudah login dengan mengecek adanya session 'role'
if (isset($_SESSION['role'])) {
    // Jika sudah login, arahkan sesuai rolenya
    if ($_SESSION['role'] == 'admin') {
        header("Location: views/admin/dashboard.php");
        exit();
    } elseif ($_SESSION['role'] == 'kepala') {
        header("Location: views/kepala/dashboard.php");
        exit();
    }
} else {
    // Jika belum login, arahkan ke halaman login
    header("Location: login.php");
    exit();
}
?>
<?php
// 1. Mulai session untuk mengenali session yang sedang aktif
session_start();

// 2. Hapus semua variabel session yang ada
session_unset();

// 3. Hancurkan session sepenuhnya
session_destroy();

// 4. Arahkan kembali pengguna ke halaman utama (index.php / halaman login)
header("Location: ../index.php");
exit();
?>
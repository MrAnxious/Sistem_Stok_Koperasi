<?php
// Pengaturan Database
$host     = "localhost";      // Biasanya 'localhost' jika berjalan di server lokal atau Project IDX
$username = "root";           // Username default MySQL (sesuaikan jika di IDX berbeda)
$password = "";               // Password default MySQL (biasanya kosong di XAMPP, sesuaikan jika di IDX ada passwordnya)
$database = "sistem_koperasi_stok";    // Nama database yang kita buat sebelumnya

// Membuat koneksi menggunakan MySQLi
$koneksi = mysqli_connect($host, $username, $password, $database);

// Cek apakah koneksi berhasil atau gagal
if (!$koneksi) {
    // Jika gagal, sistem akan berhenti dan menampilkan pesan error
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Ambil data pengaturan sistem secara global
$query_pengaturan = mysqli_query($koneksi, "SELECT * FROM pengaturan LIMIT 1");
$_SETTINGS = mysqli_fetch_assoc($query_pengaturan);

// Fungsi global untuk mencatat log aktivitas
function catat_log($koneksi, $id_user, $aksi, $keterangan) {
    if (!$id_user) return false;
    $aksi = mysqli_real_escape_string($koneksi, $aksi);
    $keterangan = mysqli_real_escape_string($koneksi, $keterangan);
    $query_log = "INSERT INTO log_aktivitas (id_user, aksi, keterangan) VALUES ('$id_user', '$aksi', '$keterangan')";
    return mysqli_query($koneksi, $query_log);
}
?>
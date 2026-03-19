<?php
session_start();

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'kepala'])) {
    die("Akses ditolak.");
}

require '../../config/koneksi.php';

// Ambil semua data stok
$query = "SELECT * FROM stok ORDER BY id_barang ASC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <?php if(!empty($_SETTINGS['favicon'])): ?>
        <link rel="icon" type="image/png" href="../../assets/logo_sistem/<?= htmlspecialchars($_SETTINGS['favicon']) ?>">
    <?php endif; ?>
    <title>Cetak Laporan - Stok Barang Master</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.5; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; color: #af101a; }
        .header p { margin: 5px 0 0 0; font-size: 14px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 14px; }
        th { background-color: #f4f4f4; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .footer { margin-top: 40px; text-align: right; font-size: 14px; }
        .footer p { margin-bottom: 60px; }
        @media print { body { padding: 0; } .no-print { display: none; } }
    </style>
</head>
<body>
    
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #af101a; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; font-weight: bold;">🖨️ Cetak / Simpan PDF</button>
        <button onclick="window.close()" style="padding: 10px 20px; background-color: #6c757d; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; font-weight: bold; margin-left: 10px;">Tutup</button>
    </div>

    <div class="header">
        <h1>KOPERASI MERAH PUTIH</h1>
        <p>Laporan Master Stok Barang</p>
        <p><strong>Dicetak pada:</strong> <?= date('d/m/Y H:i') ?></p>
        <p><strong>Oleh:</strong> <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username']) ?> (<?= ucfirst($_SESSION['role']) ?>)</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 15%; text-align: center;">ID Barang</th>
                <th style="width: 40%;">Nama Barang</th>
                <th style="width: 25%;">Kategori</th>
                <th style="width: 15%; text-align: center;">Stok Tersedia</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
            ?>
                <tr>
                    <td style="text-align: center;"><?= $no++ ?></td>
                    <td style="text-align: center;">BRG-<?= str_pad($row['id_barang'], 3, '0', STR_PAD_LEFT) ?></td>
                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                    <td><?= htmlspecialchars($row['kategori']) ?></td>
                    <td style="text-align: center;"><strong><?= $row['jumlah_stok'] ?></strong></td>
                </tr>
            <?php 
                }
            } else {
            ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;">Belum ada data barang.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Mengetahui,</p>
        <br>
        <strong>( <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username']) ?> )</strong>
    </div>

</body>
</html>

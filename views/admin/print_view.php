<?php
session_start();

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'kepala'])) {
    die("Akses ditolak.");
}

require '../../config/koneksi.php';

// Validasi parameter GET
if (!isset($_GET['tgl_mulai']) || !isset($_GET['tgl_selesai'])) {
    die("Pilih rentang tanggal terlebih dahulu.");
}

$tgl_mulai = $_GET['tgl_mulai'];
$tgl_selesai = $_GET['tgl_selesai'];
$jenis = isset($_GET['jenis']) ? $_GET['jenis'] : 'semua';

// Query dasar
$query_masuk = "SELECT 'Barang Masuk' as tipe, bm.tanggal_masuk as waktu, s.nama_barang, sup.nama_supplier as keterangan, bm.jumlah_masuk as jumlah, u.username as admin 
                FROM barang_masuk bm 
                JOIN stok s ON bm.id_barang = s.id_barang 
                LEFT JOIN supplier sup ON bm.id_supplier = sup.id_supplier 
                JOIN user u ON bm.id_user = u.id_user 
                WHERE DATE(bm.tanggal_masuk) BETWEEN '$tgl_mulai' AND '$tgl_selesai'";

$query_keluar = "SELECT 'Barang Keluar' as tipe, bk.tanggal_keluar as waktu, s.nama_barang, bk.keterangan, bk.jumlah_keluar as jumlah, u.username as admin 
                 FROM barang_keluar bk 
                 JOIN stok s ON bk.id_barang = s.id_barang 
                 JOIN user u ON bk.id_user = u.id_user 
                 WHERE DATE(bk.tanggal_keluar) BETWEEN '$tgl_mulai' AND '$tgl_selesai'";

$final_query = "";

if ($jenis == 'masuk') {
    $final_query = $query_masuk . " ORDER BY waktu ASC";
} elseif ($jenis == 'keluar') {
    $final_query = $query_keluar . " ORDER BY waktu ASC";
} else {
    $final_query = "($query_masuk) UNION ALL ($query_keluar) ORDER BY waktu ASC";
}

$result = mysqli_query($koneksi, $final_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <?php if(!empty($_SETTINGS['favicon'])): ?>
        <link rel="icon" type="image/png" href="../../assets/logo_sistem/<?= htmlspecialchars($_SETTINGS['favicon']) ?>">
    <?php endif; ?>
    <title>Cetak Laporan - <?= date('d M Y', strtotime($tgl_mulai)) ?> s/d <?= date('d M Y', strtotime($tgl_selesai)) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #af101a;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 14px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .masuk {
            color: #166534;
            font-weight: bold;
        }
        .keluar {
            color: #9a3412;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            text-align: right;
            font-size: 14px;
        }
        .footer p {
            margin-bottom: 60px;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #af101a; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; font-weight: bold;">🖨️ Cetak / Simpan PDF</button>
    </div>

    <div class="header">
        <h1>KOPERASI MERAH PUTIH</h1>
        <p>Laporan Transaksi Barang</p>
        <p><strong>Periode:</strong> <?= date('d/m/Y', strtotime($tgl_mulai)) ?> - <?= date('d/m/Y', strtotime($tgl_selesai)) ?></p>
        <p><strong>Tipe Laporan:</strong> <?= strtoupper($jenis) ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 15%;">Jenis Transaksi</th>
                <th style="width: 25%;">Nama Barang</th>
                <th style="width: 10%; text-align: center;">Jumlah</th>
                <th style="width: 20%;">Keterangan / Supplier</th>
                <th style="width: 10%;">Admin</th>
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
                    <td><?= date('d/m/Y H:i', strtotime($row['waktu'])) ?></td>
                    <td class="<?= $row['tipe'] == 'Barang Masuk' ? 'masuk' : 'keluar' ?>"><?= $row['tipe'] ?></td>
                    <td><?= $row['nama_barang'] ?></td>
                    <td style="text-align: center;"><strong><?= $row['jumlah'] ?></strong></td>
                    <td><?= $row['keterangan'] ? $row['keterangan'] : '-' ?></td>
                    <td><?= $row['admin'] ?></td>
                </tr>
            <?php 
                }
            } else {
            ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">Tidak ada transaksi pada periode ini.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: <?= date('d/m/Y H:i') ?></p>
        <p>Mengetahui,</p>
        <br>
        <strong>( <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username']) ?> - <?= ucfirst($_SESSION['role']) ?> )</strong>
    </div>

</body>
</html>
<?php
require 'config/koneksi.php';

$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');
$filter = "$tahun-$bulan";

// Set header Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Keuangan_{$bulan}_{$tahun}.xls");

// Query data gabungan
$pemasukan = mysqli_query($conn, "SELECT tanggal, 'Pemasukan' AS tipe, kategori, deskripsi, jumlah, metode_pembayaran 
                                  FROM pemasukan 
                                  WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$filter'");

$pengeluaran = mysqli_query($conn, "SELECT tanggal, 'Pengeluaran' AS tipe, k.nama_kategori AS kategori, p.deskripsi, p.jumlah, p.metode_pembayaran 
                                    FROM pengeluaran p
                                    JOIN kategori_pengeluaran k ON p.kategori_id = k.id
                                    WHERE DATE_FORMAT(p.tanggal, '%Y-%m') = '$filter'");

// Gabungkan
$data = [];
while ($row = mysqli_fetch_assoc($pemasukan)) $data[] = $row;
while ($row = mysqli_fetch_assoc($pengeluaran)) $data[] = $row;

// Urutkan berdasarkan tanggal
usort($data, function($a, $b) {
    return strtotime($a['tanggal']) - strtotime($b['tanggal']);
});

// Mulai output tabel
echo "<h3>Laporan Keuangan Gabungan - " . date('F Y', strtotime("$tahun-$bulan-01")) . "</h3>";
echo "<table border='1' cellpadding='5'>
<tr>
    <th>No</th>
    <th>Tanggal</th>
    <th>Jenis</th>
    <th>Kategori</th>
    <th>Deskripsi</th>
    <th>Jumlah (Rp)</th>
    <th>Metode</th>
</tr>";

$no = 1;
$totalMasuk = 0;
$totalKeluar = 0;

foreach ($data as $row) {
    echo "<tr>
        <td>{$no}</td>
        <td>".date('d-m-Y', strtotime($row['tanggal']))."</td>
        <td>{$row['tipe']}</td>
        <td>{$row['kategori']}</td>
        <td>{$row['deskripsi']}</td>
        <td>Rp ".number_format($row['jumlah'], 0, ',', '.')."</td>
        <td>{$row['metode_pembayaran']}</td>
    </tr>";

    if ($row['tipe'] == 'Pemasukan') $totalMasuk += $row['jumlah'];
    else $totalKeluar += $row['jumlah'];

    $no++;
}

$selisih = $totalMasuk - $totalKeluar;

// Tambahan total dan selisih
echo "<tr>
    <td colspan='5' align='right'><strong>Total Pemasukan</strong></td>
    <td colspan='2'>Rp ".number_format($totalMasuk, 0, ',', '.')."</td>
</tr>";
echo "<tr>
    <td colspan='5' align='right'><strong>Total Pengeluaran</strong></td>
    <td colspan='2'>Rp ".number_format($totalKeluar, 0, ',', '.')."</td>
</tr>";
echo "<tr>
    <td colspan='5' align='right'><strong>Selisih</strong></td>
    <td colspan='2'>Rp ".number_format($selisih, 0, ',', '.')."</td>
</tr>";
echo "</table>";
exit;

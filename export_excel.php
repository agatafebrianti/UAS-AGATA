<?php
require 'config/koneksi.php';

$bulan = $_GET['bulan'];
$tahun = $_GET['tahun'];
$filter = "$tahun-$bulan";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Keuangan_$bulan-$tahun.xls");

echo "<table border='1'>
<tr style='background-color:#dff0ff;'>
    <th>No</th>
    <th>Tanggal</th>
    <th>Jenis</th>
    <th>Kategori</th>
    <th>Deskripsi</th>
    <th>Jumlah (Rp)</th>
    <th>Metode</th>
</tr>";

$query = mysqli_query($conn, "
    SELECT tanggal, 'Pemasukan' AS tipe, kategori, deskripsi, jumlah, metode_pembayaran 
    FROM pemasukan 
    WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$filter'
    UNION
    SELECT tanggal, 'Pengeluaran' AS tipe, kp.nama_kategori AS kategori, deskripsi, jumlah, metode_pembayaran 
    FROM pengeluaran p 
    JOIN kategori_pengeluaran kp ON p.kategori_id = kp.id 
    WHERE DATE_FORMAT(p.tanggal, '%Y-%m') = '$filter'
    ORDER BY tanggal DESC
");

$no = 1;
$totalPemasukan = 0;
$totalPengeluaran = 0;

while ($row = mysqli_fetch_assoc($query)) {
    echo "<tr>";
    echo "<td>" . $no++ . "</td>";
    echo "<td>" . date('d-m-Y', strtotime($row['tanggal'])) . "</td>";
    echo "<td>" . $row['tipe'] . "</td>";
    echo "<td>" . $row['kategori'] . "</td>";
    echo "<td>" . $row['deskripsi'] . "</td>";
    echo "<td align='right'>" . number_format($row['jumlah'], 0, ',', '.') . "</td>";
    echo "<td>" . $row['metode_pembayaran'] . "</td>";
    echo "</tr>";

    if ($row['tipe'] == 'Pemasukan') {
        $totalPemasukan += $row['jumlah'];
    } else {
        $totalPengeluaran += $row['jumlah'];
    }
}

// Footer Total
echo "<tr style='font-weight: bold; background-color:#f0f8ff;'>
    <td colspan='5' align='right'>Total Pemasukan</td>
    <td colspan='2' align='right'>Rp " . number_format($totalPemasukan, 0, ',', '.') . "</td>
</tr>";
echo "<tr style='font-weight: bold; background-color:#f0f8ff;'>
    <td colspan='5' align='right'>Total Pengeluaran</td>
    <td colspan='2' align='right'>Rp " . number_format($totalPengeluaran, 0, ',', '.') . "</td>
</tr>";
echo "<tr style='font-weight: bold; background-color:#f0f8ff;'>
    <td colspan='5' align='right'>Selisih</td>
    <td colspan='2' align='right'>Rp " . number_format($totalPemasukan - $totalPengeluaran, 0, ',', '.') . "</td>
</tr>";

echo "</table>";
?>

<?php
require 'config/koneksi.php';
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Ambil bulan dan tahun dari GET atau default ke bulan & tahun sekarang
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Ambil data pemasukan & pengeluaran sesuai bulan dan tahun
$query = mysqli_query($conn, "
    SELECT tanggal, 'Pemasukan' AS tipe, 
           IFNULL(kp.nama_kategori, kategori) AS kategori, 
           deskripsi, jumlah, metode_pembayaran 
    FROM pemasukan p
    LEFT JOIN kategori_pemasukan kp ON p.kategori = kp.id
    WHERE MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun'

    UNION

    SELECT tanggal, 'Pengeluaran' AS tipe, 
           IFNULL(kg.nama_kategori, kategori) AS kategori, 
           deskripsi, jumlah, metode_pembayaran 
    FROM pengeluaran p
    LEFT JOIN kategori_pengeluaran kg ON p.kategori = kg.id
    WHERE MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun'

    ORDER BY tanggal DESC
");

// Siapkan isi laporan
$html = "
<h2 style='text-align:center;'>Laporan Keuangan Bulan " . date('F', mktime(0, 0, 0, $bulan, 10)) . " $tahun</h2>
<table border='1' cellspacing='0' cellpadding='6' width='100%'>
    <thead style='background-color:#e0eaff;'>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Jenis</th>
            <th>Kategori</th>
            <th>Deskripsi</th>
            <th>Jumlah (Rp)</th>
            <th>Metode</th>
        </tr>
    </thead>
    <tbody>
";

$no = 1;
$totalPemasukan = 0;
$totalPengeluaran = 0;

while ($row = mysqli_fetch_assoc($query)) {
    $html .= "<tr>
        <td>$no</td>
        <td>" . date('d-m-Y', strtotime($row['tanggal'])) . "</td>
        <td>{$row['tipe']}</td>
        <td>{$row['kategori']}</td>
        <td>{$row['deskripsi']}</td>
        <td style='text-align:right;'>Rp " . number_format($row['jumlah'], 0, ',', '.') . "</td>
        <td>{$row['metode_pembayaran']}</td>
    </tr>";

    if ($row['tipe'] == 'Pemasukan') {
        $totalPemasukan += $row['jumlah'];
    } else {
        $totalPengeluaran += $row['jumlah'];
    }

    $no++;
}

$selisih = $totalPemasukan - $totalPengeluaran;

$html .= "
    </tbody>
    <tfoot>
        <tr><td colspan='5' align='right'><strong>Total Pemasukan</strong></td><td colspan='2'>Rp " . number_format($totalPemasukan, 0, ',', '.') . "</td></tr>
        <tr><td colspan='5' align='right'><strong>Total Pengeluaran</strong></td><td colspan='2'>Rp " . number_format($totalPengeluaran, 0, ',', '.') . "</td></tr>
        <tr><td colspan='5' align='right'><strong>Selisih</strong></td><td colspan='2'>Rp " . number_format($selisih, 0, ',', '.') . "</td></tr>
    </tfoot>
</table>
";

// Generate PDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// Buat file downloadable
$filename = "Laporan_Keuangan_" . date('F_Y', mktime(0, 0, 0, $bulan, 10));
$dompdf->stream($filename . ".pdf", ["Attachment" => true]); // true = langsung download
exit;

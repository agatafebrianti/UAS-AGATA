<?php
require 'vendor/autoload.php';
require 'config/koneksi.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Ambil data filter bulan & tahun dari GET
$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');
$filter = "$tahun-$bulan";

// Ambil data pemasukan
$pemasukan = mysqli_query($conn, "SELECT tanggal, 'Pemasukan' AS tipe, kategori AS kategori, deskripsi, jumlah, metode_pembayaran 
                                  FROM pemasukan 
                                  WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$filter'");

// Ambil data pengeluaran
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

// Siapkan HTML
$html = "<h2 style='text-align:center;'>Laporan Keuangan - ".date('F Y', strtotime("$tahun-$bulan-01"))."</h2>";
$html .= "<table border='1' cellspacing='0' cellpadding='6' width='100%'>
<thead style='background:#e0eaff;'>
    <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Tipe</th>
        <th>Kategori</th>
        <th>Deskripsi</th>
        <th>Jumlah (Rp)</th>
        <th>Metode</th>
    </tr>
</thead>
<tbody>";

$no = 1;
$totalMasuk = 0;
$totalKeluar = 0;

foreach ($data as $row) {
    $tanggal = date('d-m-Y', strtotime($row['tanggal']));
    $jumlah = number_format($row['jumlah'], 0, ',', '.');
    $html .= "<tr>
        <td>{$no}</td>
        <td>{$tanggal}</td>
        <td>{$row['tipe']}</td>
        <td>{$row['kategori']}</td>
        <td>{$row['deskripsi']}</td>
        <td align='right'>Rp {$jumlah}</td>
        <td>{$row['metode_pembayaran']}</td>
    </tr>";
    if ($row['tipe'] == 'Pemasukan') {
        $totalMasuk += $row['jumlah'];
    } else {
        $totalKeluar += $row['jumlah'];
    }
    $no++;
}

$selisih = $totalMasuk - $totalKeluar;

$html .= "</tbody>
<tfoot>
    <tr>
        <td colspan='5' align='right'><strong>Total Pemasukan</strong></td>
        <td colspan='2'><strong>Rp ".number_format($totalMasuk, 0, ',', '.')."</strong></td>
    </tr>
    <tr>
        <td colspan='5' align='right'><strong>Total Pengeluaran</strong></td>
        <td colspan='2'><strong>Rp ".number_format($totalKeluar, 0, ',', '.')."</strong></td>
    </tr>
    <tr>
        <td colspan='5' align='right'><strong>Selisih</strong></td>
        <td colspan='2'><strong>Rp ".number_format($selisih, 0, ',', '.')."</strong></td>
    </tr>
</tfoot>
</table>";

// Export ke PDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("Laporan_Keuangan_{$bulan}_{$tahun}.pdf", ["Attachment" => false]);
exit;

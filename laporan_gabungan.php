<?php
require 'config/koneksi.php';
$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');
$filter = "$tahun-$bulan";

// Pemasukan
$pemasukan = mysqli_query($conn, "SELECT tanggal, 'Pemasukan' AS tipe, kategori, deskripsi, jumlah, metode_pembayaran 
                                  FROM pemasukan 
                                  WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$filter'");

// Pengeluaran
$pengeluaran = mysqli_query($conn, "SELECT tanggal, 'Pengeluaran' AS tipe, k.nama_kategori AS kategori, p.deskripsi, p.jumlah, p.metode_pembayaran 
                                    FROM pengeluaran p
                                    JOIN kategori_pengeluaran k ON p.kategori_id = k.id
                                    WHERE DATE_FORMAT(p.tanggal, '%Y-%m') = '$filter'");

// Gabungkan
$data = [];
while ($row = mysqli_fetch_assoc($pemasukan)) $data[] = $row;
while ($row = mysqli_fetch_assoc($pengeluaran)) $data[] = $row;

usort($data, function($a, $b) {
    return strtotime($a['tanggal']) - strtotime($b['tanggal']);
});
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Gabungan - FinTrack</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body style="background-color: #f0f8ff;">
<div class="container mt-4">
    <h2 class="text-primary mb-4">📑 Laporan Keuangan Gabungan</h2>

    <!-- Filter Form -->
    <form class="row mb-4" method="GET" action="">
        <div class="col-md-3">
            <select name="bulan" class="form-select" required>
                <option value="">Pilih Bulan</option>
                <?php
                for ($m = 1; $m <= 12; $m++) {
                    $bln = str_pad($m, 2, '0', STR_PAD_LEFT);
                    $selected = ($bulan == $bln) ? "selected" : "";
                    echo "<option value='$bln' $selected>".date('F', mktime(0, 0, 0, $m, 10))."</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="tahun" class="form-select" required>
                <?php
                for ($y = 2023; $y <= date('Y'); $y++) {
                    $selected = ($tahun == $y) ? "selected" : "";
                    echo "<option value='$y' $selected>$y</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
        </div>
        <div class="col-md-2">
            <a href="laporan_pdf.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn btn-danger w-100" target="_blank">Export PDF</a>
        </div>
        <div class="col-md-2">
    <a href="laporan_excel.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn btn-success w-100" target="_blank">Export Excel</a>
</div>

    </form>

    <!-- Table -->
    <table class="table table-bordered bg-white">
        <thead class="table-info text-center">
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
            <?php
            if (count($data) > 0) {
                $no = 1;
                $totalMasuk = 0;
                $totalKeluar = 0;
                foreach ($data as $row) {
                    echo "<tr>
                        <td class='text-center'>{$no}</td>
                        <td>".date('d-m-Y', strtotime($row['tanggal']))."</td>
                        <td class='text-center'>{$row['tipe']}</td>
                        <td>{$row['kategori']}</td>
                        <td>{$row['deskripsi']}</td>
                        <td>Rp ".number_format($row['jumlah'], 0, ',', '.')."</td>
                        <td>{$row['metode_pembayaran']}</td>
                    </tr>";
                    if ($row['tipe'] == 'Pemasukan') $totalMasuk += $row['jumlah'];
                    else $totalKeluar += $row['jumlah'];
                    $no++;
                }
            ?>
            <tr class="table-warning fw-bold">
                <td colspan="5" class="text-end">Total Pemasukan</td>
                <td colspan="2">Rp <?= number_format($totalMasuk, 0, ',', '.') ?></td>
            </tr>
            <tr class="table-warning fw-bold">
                <td colspan="5" class="text-end">Total Pengeluaran</td>
                <td colspan="2">Rp <?= number_format($totalKeluar, 0, ',', '.') ?></td>
            </tr>
            <tr class="table-warning fw-bold">
                <td colspan="5" class="text-end">Selisih</td>
                <td colspan="2">Rp <?= number_format($totalMasuk - $totalKeluar, 0, ',', '.') ?></td>
            </tr>
            <?php } else { ?>
                <tr><td colspan="7" class="text-center">Tidak ada data untuk bulan ini.</td></tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php
require 'config/koneksi.php';

// Ambil bulan & tahun dari form (default ke bulan sekarang)
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Format untuk MySQL
$filter = "$tahun-$bulan";

// Query total pemasukan
$qPemasukan = mysqli_query($conn, "SELECT SUM(jumlah) as total FROM pemasukan WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$filter'");
$pemasukan = mysqli_fetch_assoc($qPemasukan)['total'] ?? 0;

// Query total pengeluaran
$qPengeluaran = mysqli_query($conn, "SELECT SUM(jumlah) as total FROM pengeluaran WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$filter'");
$pengeluaran = mysqli_fetch_assoc($qPengeluaran)['total'] ?? 0;

// Kategori pengeluaran terbanyak
$qKategori = mysqli_query($conn, "SELECT kp.nama_kategori, SUM(p.jumlah) as total 
    FROM pengeluaran p 
    JOIN kategori_pengeluaran kp ON p.kategori_id = kp.id 
    WHERE DATE_FORMAT(p.tanggal, '%Y-%m') = '$filter'
    GROUP BY kp.nama_kategori 
    ORDER BY total DESC 
    LIMIT 1");

$kategoriTerboros = mysqli_fetch_assoc($qKategori)['nama_kategori'] ?? 'Belum Ada';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Bulanan - FinTrack</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body style="background-color: #f0f8ff;">
<div class="container mt-5">
    <h2 class="text-primary mb-4">Laporan Keuangan Bulanan</h2>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="bulan" class="form-label">Bulan</label>
            <select name="bulan" class="form-select" id="bulan">
                <?php for ($i = 1; $i <= 12; $i++): 
                    $val = str_pad($i, 2, '0', STR_PAD_LEFT); ?>
                    <option value="<?= $val ?>" <?= $val == $bulan ? 'selected' : '' ?>>
                        <?= date('F', mktime(0, 0, 0, $i, 10)) ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="tahun" class="form-label">Tahun</label>
            <select name="tahun" class="form-select" id="tahun">
                <?php for ($i = date('Y') - 5; $i <= date('Y'); $i++): ?>
                    <option value="<?= $i ?>" <?= $i == $tahun ? 'selected' : '' ?>><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-primary" type="submit">Tampilkan</button>
        </div>
    </form>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="p-3 bg-white shadow rounded text-center">
                <h5>Total Pemasukan</h5>
                <h4 class="text-success">Rp <?= number_format($pemasukan, 0, ',', '.') ?></h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 bg-white shadow rounded text-center">
                <h5>Total Pengeluaran</h5>
                <h4 class="text-danger">Rp <?= number_format($pengeluaran, 0, ',', '.') ?></h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 bg-white shadow rounded text-center">
                <h5>Kategori Terboros</h5>
                <h4 class="text-warning"><?= $kategoriTerboros ?></h4>
            </div>
        </div>
    </div>

    <div class="card p-4 mb-4">
        <canvas id="grafikKeuangan" height="100"></canvas>
    </div>

    <div class="alert alert-info">
        Bulan <?= date('F Y', strtotime($filter . '-01')) ?>, pengeluaranmu paling banyak untuk <strong><?= $kategoriTerboros ?></strong>. Yuk kontrol pengeluaranmu!
    </div>

    <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
    <a href="export_pdf.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn btn-danger">Ekspor PDF</a>
    <a href="export_excel.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn btn-success">Ekspor Excel</a>
</div>

<script>
    const ctx = document.getElementById('grafikKeuangan').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Pemasukan', 'Pengeluaran'],
            datasets: [{
                label: 'Jumlah (Rp)',
                data: [<?= $pemasukan ?>, <?= $pengeluaran ?>],
                backgroundColor: ['#198754', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });
</script>
</body>
</html>

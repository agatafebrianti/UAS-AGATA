<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'config/koneksi.php';

$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');

// Ambil data pemasukan
$pemasukanQuery = mysqli_query($conn, "
    SELECT tanggal, jumlah 
    FROM pemasukan 
    WHERE MONTH(tanggal) = $bulan AND YEAR(tanggal) = $tahun
");

$pemasukanData = [];
while ($row = mysqli_fetch_assoc($pemasukanQuery)) {
    $tanggal = date('d', strtotime($row['tanggal']));
    $pemasukanData[$tanggal] = $row['jumlah'];
}

// Ambil data pengeluaran
$pengeluaranQuery = mysqli_query($conn, "
    SELECT tanggal, jumlah 
    FROM pengeluaran 
    WHERE MONTH(tanggal) = $bulan AND YEAR(tanggal) = $tahun
");

$pengeluaranData = [];
while ($row = mysqli_fetch_assoc($pengeluaranQuery)) {
    $tanggal = date('d', strtotime($row['tanggal']));
    $pengeluaranData[$tanggal] = $row['jumlah'];
}

// Siapkan data grafik
$labels = [];
$pemasukanGrafik = [];
$pengeluaranGrafik = [];

for ($i = 1; $i <= 31; $i++) {
    $hari = str_pad($i, 2, '0', STR_PAD_LEFT);
    $labels[] = $hari;
    $pemasukanGrafik[] = $pemasukanData[$hari] ?? 0;
    $pengeluaranGrafik[] = $pengeluaranData[$hari] ?? 0;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Grafik Keuangan Bulanan</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f8ff; }
        .container { margin-top: 30px; }
        canvas { background-color: white; border-radius: 15px; padding: 10px; }
    </style>
</head>
<body>

<div class="container">
    <h3 class="text-center text-primary mb-4">📊 Grafik Keuangan Bulan <?= $bulan ?>/<?= $tahun ?></h3>

    <!-- Filter Bulan dan Tahun -->
    <form class="row mb-4 justify-content-center" method="GET">
        <div class="col-md-2">
            <select name="bulan" class="form-select">
                <?php
                for ($b = 1; $b <= 12; $b++) {
                    $pad = str_pad($b, 2, '0', STR_PAD_LEFT);
                    $selected = ($pad == $bulan) ? 'selected' : '';
                    echo "<option value='$pad' $selected>Bulan $pad</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="tahun" class="form-select">
                <?php
                for ($y = date('Y'); $y >= 2022; $y--) {
                    $selected = ($y == $tahun) ? 'selected' : '';
                    echo "<option value='$y' $selected>$y</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Tampilkan</button>
        </div>
    </form>

    <!-- Grafik -->
    <canvas id="grafikKeuangan" height="100"></canvas>
</div>

<script>
const ctx = document.getElementById('grafikKeuangan').getContext('2d');
const grafik = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [
            {
                label: 'Pemasukan',
                data: <?= json_encode($pemasukanGrafik) ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.7)'
            },
            {
                label: 'Pengeluaran',
                data: <?= json_encode($pengeluaranGrafik) ?>,
                backgroundColor: 'rgba(255, 99, 132, 0.7)'
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top'
            },
            title: {
                display: true,
                text: 'Grafik Keuangan Harian'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
</body>
</html>

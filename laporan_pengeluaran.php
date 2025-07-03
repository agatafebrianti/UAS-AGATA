<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'config/koneksi.php';

date_default_timezone_set('Asia/Jakarta');
$hour = date('H');
if ($hour < 12) {
    $greet = "Selamat pagi";
} elseif ($hour < 15) {
    $greet = "Selamat siang";
} elseif ($hour < 18) {
    $greet = "Selamat sore";
} else {
    $greet = "Selamat malam";
}

$pemasukan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) AS total FROM pemasukan"))['total'] ?? 0;
$pengeluaran = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) AS total FROM pengeluaran"))['total'] ?? 0;
$selisih = $pemasukan - $pengeluaran;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - FinTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(to right, #f0f8ff, #e0f2ff);
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: scale(1.02);
        }
        .navbar {
            background-color: #007bff;
        }
        .navbar-brand {
            color: white;
            font-weight: bold;
        }
        .logout-btn {
            color: white;
            text-decoration: none;
        }
        .btn-custom {
            border-radius: 20px;
        }
        footer {
            margin-top: 50px;
            padding: 15px;
            text-align: center;
            font-size: 14px;
            color: #777;
        }
    </style>

    <script>
        function toggleJumlah(id) {
            const elem = document.getElementById(id);
            if (elem.style.display === "none") {
                elem.style.display = "block";
            } else {
                elem.style.display = "none";
            }
        }
    </script>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark px-4">
    <a class="navbar-brand" href="#"><i class="fas fa-wallet me-2"></i>FinTrack</a>
    <div class="ms-auto">
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</nav>

<!-- Konten Utama -->
<div class="container mt-5">
    <h4 class="text-primary mb-3"><?= $greet ?> 👋</h4>
    <p>Semoga harimu menyenangkan! Yuk, cek kondisi keuanganmu hari ini ✨</p>

    <!-- Ringkasan -->
    <div class="row g-4 mt-4">
        <div class="col-md-4">
            <div class="card bg-white text-dark p-4 text-center" onclick="toggleJumlah('pemasukan')">
                <h5><i class="fas fa-money-bill-wave text-success me-2"></i>Pemasukan</h5>
                <div id="pemasukan" style="display: none;">
                    <h4 class="text-success">Rp <?= number_format($pemasukan, 0, ',', '.') ?></h4>
                </div>
                <small class="text-muted">Klik untuk lihat</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-white text-dark p-4 text-center" onclick="toggleJumlah('pengeluaran')">
                <h5><i class="fas fa-receipt text-danger me-2"></i>Pengeluaran</h5>
                <div id="pengeluaran" style="display: none;">
                    <h4 class="text-danger">Rp <?= number_format($pengeluaran, 0, ',', '.') ?></h4>
                </div>
                <small class="text-muted">Klik untuk lihat</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-white text-dark p-4 text-center" onclick="toggleJumlah('selisih')">
                <h5><i class="fas fa-balance-scale text-primary me-2"></i>Selisih</h5>
                <div id="selisih" style="display: none;">
                    <h4 class="text-primary">Rp <?= number_format($selisih, 0, ',', '.') ?></h4>
                </div>
                <small class="text-muted">Klik untuk lihat</small>
            </div>
        </div>
    </div>

    <!-- Navigasi Cepat -->
    <div class="row mt-5 g-3">
        <div class="col-md-3">
            <a href="pemasukan_add.php" class="btn btn-success w-100 btn-custom"><i class="fas fa-plus-circle me-1"></i>Tambah Pemasukan</a>
        </div>
        <div class="col-md-3">
            <a href="pengeluaran_add.php" class="btn btn-danger w-100 btn-custom"><i class="fas fa-minus-circle me-1"></i>Tambah Pengeluaran</a>
        </div>
        <div class="col-md-3">
            <a href="laporan_gabungan.php" class="btn btn-primary w-100 btn-custom"><i class="fas fa-file-alt me-1"></i>Laporan</a>
        </div>
        <div class="col-md-3">
            <a href="grafik_keuangan.php" class="btn btn-info w-100 btn-custom"><i class="fas fa-chart-line me-1"></i>Grafik</a>
        </div>
    </div>
</div>

<!-- Footer -->
<footer>
    &copy; <?= date('Y') ?> FinTrack. Aplikasi manajemen keuangan harian. 💙
</footer>

</body>
</html>
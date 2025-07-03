<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require 'config/koneksi.php';

// Total pemasukan dan pengeluaran
$pemasukan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) AS total FROM pemasukan"))['total'] ?? 0;
$pengeluaran = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) AS total FROM pengeluaran"))['total'] ?? 0;
$selisih = $pemasukan - $pengeluaran;

// Motivasi harian (acak)
$motivasi = [
    "Kendalikan uangmu sebelum dia mengendalikanmu 💸",
];
$random_motivasi = $motivasi[array_rand($motivasi)];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - FinTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f8ff;
        }
        .card {
            border-radius: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        .card:hover {
            transform: scale(1.03);
        }
        .navbar {
            background-color: #007bff;
        }
        .navbar-brand {
            color: white;
            font-weight: bold;
        }
        .navbar-buttons a {
            margin-left: 10px;
        }
        .logout-btn {
            background-color: #dc3545;
            color: white !important;
            font-weight: bold;
            padding: 6px 16px;
            border-radius: 10px;
            text-decoration: none;
        }
        .setting-btn {
            background-color: #17a2b8;
            color: white !important;
            padding: 6px 16px;
            border-radius: 10px;
            text-decoration: none;
        }
        .tips-box {
            margin-top: 40px;
            padding: 20px;
            background: #e7f3ff;
            border-radius: 16px;
            font-size: 16px;
        }
    </style>
    <script>
        function toggle(id) {
            var e = document.getElementById(id);
            if (e.style.display === "none") {
                e.style.display = "block";
            } else {
                e.style.display = "none";
            }
        }
    </script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark px-4">
    <a class="navbar-brand" href="#">FinTrack</a>
    <div class="ms-auto d-flex align-items-center gap-3">
        <div class="dropdown">
            <a class="btn btn-outline-light dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                ⚙️ Pengaturan
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="ubah_password.php">🔐 Ubah Password</a></li>
                <?php if ($_SESSION['role_id'] == 1): ?>
                    <li><a class="dropdown-item" href="pengaturan_sistem.php">⚙️ Pengaturan Sistem</a></li>
                    <li><a class="dropdown-item" href="monitoring.php">📊 Monitoring Aktivitas</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <a href="logout.php" class="btn btn-warning text-white">🚪 Logout</a>
    </div>
</nav>

<!-- Konten -->
<div class="container mt-5">
    <h3 class="mb-3 text-primary">Halo 👋, Selamat Datang di FinTrack!</h3>
    <div class="alert alert-info"><?= $random_motivasi ?></div>

    <!-- Ringkasan -->
    <div class="row g-4">
        <div class="col-md-4" onclick="toggle('showMasuk')">
            <div class="card bg-light text-dark p-4 text-center">
                <h5>💰 Total Pemasukan</h5>
                <h3 class="text-success" id="showMasuk" style="display: none;">Rp <?= number_format($pemasukan, 0, ',', '.') ?></h3>
            </div>
        </div>
        <div class="col-md-4" onclick="toggle('showKeluar')">
            <div class="card bg-light text-dark p-4 text-center">
                <h5>📤 Total Pengeluaran</h5>
                <h3 class="text-danger" id="showKeluar" style="display: none;">Rp <?= number_format($pengeluaran, 0, ',', '.') ?></h3>
            </div>
        </div>
        <div class="col-md-4" onclick="toggle('showSelisih')">
            <div class="card bg-light text-dark p-4 text-center">
                <h5>📊 Selisih</h5>
                <h3 class="text-primary" id="showSelisih" style="display: none;">Rp <?= number_format($selisih, 0, ',', '.') ?></h3>
            </div>
        </div>
    </div>

    <!-- Navigasi -->
    <div class="row mt-5 g-3">
        <div class="col-md-3">
            <a href="pemasukan_add.php" class="btn btn-success w-100 btn-custom">+ Tambah Pemasukan</a>
        </div>
        <div class="col-md-3">
            <a href="pengeluaran_add.php" class="btn btn-danger w-100 btn-custom">+ Tambah Pengeluaran</a>
        </div>
        <div class="col-md-3">
            <a href="laporan_gabungan.php" class="btn btn-primary w-100 btn-custom">📑 Laporan</a>
        </div>
        <div class="col-md-3">
            <a href="grafik_keuangan.php" class="btn btn-info w-100 btn-custom">📈 Grafik Keuangan</a>
        </div>
    </div>

    <!-- Tips & Reminder -->
    <div class="tips-box mt-5">
        <h5>💡 Tips Keuangan</h5>
        <ul>
            <li>Catat semua pemasukan dan pengeluaran setiap hari</li>
            <li>Gunakan pengingat harian untuk mencatat pengeluaran kecil</li>
            <li>Tinjau laporan keuangan bulanan secara rutin</li>
            <li>Hindari belanja impulsif dan tetapkan prioritas</li>
        </ul>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

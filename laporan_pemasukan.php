<?php
require 'config/koneksi.php';

// Ambil semua data pemasukan
$result = mysqli_query($conn, "SELECT * FROM pemasukan ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pemasukan - FinTrack</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body style="background-color: #f0f8ff;">
<div class="container mt-5">
    <h2 class="text-primary mb-4">Laporan Pemasukan</h2>
    <a href="dashboard.php" class="btn btn-secondary mb-3">Kembali ke Dashboard</a>
    <table class="table table-bordered table-hover bg-white">
        <thead class="table-primary text-center">
            <tr>
                <th>No</th>
                <th>Kategori</th>
                <th>Deskripsi</th>
                <th>Jumlah (Rp)</th>
                <th>Metode</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $total = 0;
            while ($row = mysqli_fetch_assoc($result)):
                $total += $row['jumlah'];
            ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['kategori']) ?></td>
                <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                <td><?= $row['metode_pembayaran'] ?></td>
                <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr class="fw-bold bg-light">
                <td colspan="3" class="text-end">Total Pemasukan:</td>
                <td colspan="3">Rp <?= number_format($total, 0, ',', '.') ?></td>
            </tr>
        </tfoot>
    </table>
</div>
</body>
</html>

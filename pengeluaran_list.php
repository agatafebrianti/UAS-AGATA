<?php
require 'config/koneksi.php';
session_start();

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil semua data pengeluaran dari database, join ke kategori_pengeluaran
$query = "SELECT p.*, k.nama_kategori 
          FROM pengeluaran p 
          JOIN kategori_pengeluaran k ON p.kategori_id = k.id 
          WHERE p.user_id = $user_id 
          ORDER BY p.tanggal DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Pengeluaran - FinTrack</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body style="background-color: #e6f2ff;">
<div class="container mt-5">
    <h2 class="text-primary mb-4">Daftar Pengeluaran</h2>
    <a href="pengeluaran_add.php" class="btn btn-success mb-3">+ Tambah Pengeluaran</a>
    <table class="table table-bordered table-striped table-hover bg-white">
        <thead class="table-primary text-center">
            <tr>
                <th>No</th>
                <th>Kategori</th>
                <th>Deskripsi</th>
                <th>Jumlah (Rp)</th>
                <th>Metode</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $total = 0;
            while($row = mysqli_fetch_assoc($result)):
                $total += $row['jumlah'];
            ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                <td><?= $row['metode_pembayaran'] ?></td>
                <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                <td class="text-center">
                    <a href="pengeluaran_delete.php?id=<?= $row['id'] ?>" 
                       class="btn btn-danger btn-sm" 
                       onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr class="fw-bold bg-light">
                <td colspan="3" class="text-end">Total Pengeluaran:</td>
                <td colspan="4">Rp <?= number_format($total, 0, ',', '.') ?></td>
            </tr>
        </tfoot>
    </table>
    <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
</div>
</body>
</html>

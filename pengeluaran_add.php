<?php
session_start();
require 'config/koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil kategori dari tabel kategori_pengeluaran
$kategori_result = mysqli_query($conn, "SELECT * FROM kategori_pengeluaran");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kategori_id = $_POST['kategori_id'];
    $deskripsi = $_POST['deskripsi'];
    $jumlah = $_POST['jumlah'];
    $metode = $_POST['metode_pembayaran'];
    $tanggal = $_POST['tanggal'];

    $query = "INSERT INTO pengeluaran (user_id, kategori_id, deskripsi, jumlah, metode_pembayaran, tanggal)
              VALUES ('$user_id', '$kategori_id', '$deskripsi', '$jumlah', '$metode', '$tanggal')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Pengeluaran berhasil disimpan!'); window.location.href='pengeluaran_list.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data!');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Pengeluaran - FinTrack</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body style="background-color: #f0f8ff;">
<div class="container mt-5">
    <h2 class="text-primary mb-4">Tambah Pengeluaran</h2>
    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Kategori</label>
            <select class="form-select" name="kategori_id" required>
                <option value="">-- Pilih Kategori --</option>
                <?php while($row = mysqli_fetch_assoc($kategori_result)): ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nama_kategori']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <input type="text" name="deskripsi" class="form-control" placeholder="Contoh: Makan siang, Bensin, dll">
        </div>
        <div class="mb-3">
            <label class="form-label">Jumlah (Rp)</label>
            <input type="number" name="jumlah" class="form-control" placeholder="Contoh: 100000" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Metode Pembayaran</label>
            <select name="metode_pembayaran" class="form-select" required>
                <option value="">-- Pilih Metode --</option>
                <option value="Cash">Cash</option>
                <option value="QRIS">QRIS</option>
                <option value="Transfer">Transfer</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Tanggal</label>
            <input type="date" name="tanggal" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</body>
</html>

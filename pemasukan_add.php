<?php
require 'config/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kategori = $_POST['kategori'];
    $deskripsi = $_POST['deskripsi'];
    $jumlah = $_POST['jumlah'];
    $metode = $_POST['metode_pembayaran'];
    $tanggal = $_POST['tanggal'];

    $query = "INSERT INTO pemasukan (kategori, deskripsi, jumlah, metode_pembayaran, tanggal)
              VALUES ('$kategori', '$deskripsi', '$jumlah', '$metode', '$tanggal')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Pemasukan berhasil disimpan!'); window.location.href='pemasukan_list.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data!');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Pemasukan - FinTrack</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body style="background-color: #f0f8ff;">
<div class="container mt-5">
    <h2 class="text-primary mb-4">Tambah Pemasukan</h2>
    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Kategori</label>
            <select class="form-select" name="kategori" required>
                <option value="">-- Pilih Kategori --</option>
                <option value="Gaji">Gaji</option>
                <option value="Freelance">Freelance</option>
                <option value="Hadiah">Hadiah</option>
                <option value="Lainnya">Lainnya</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <input type="text" name="deskripsi" class="form-control" placeholder="Contoh: bonus proyek, hadiah ulang tahun">
        </div>
        <div class="mb-3">
            <label class="form-label">Jumlah (Rp)</label>
            <input type="number" name="jumlah" class="form-control" placeholder="Contoh: 1500000" required>
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

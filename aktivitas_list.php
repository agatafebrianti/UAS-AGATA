<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'config/koneksi.php';

$result = mysqli_query($conn, "
    SELECT a.*, u.username 
    FROM aktivitas a 
    JOIN users u ON a.user_id = u.id 
    ORDER BY waktu DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Monitoring Aktivitas - FinTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #f0f8ff;">
<div class="container mt-5">
    <h3 class="mb-4 text-primary">📌 Monitoring Aktivitas Pengguna</h3>
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Kembali</a>
    <table class="table table-striped bg-white">
        <thead>
            <tr>
                <th>No</th>
                <th>Username</th>
                <th>Aktivitas</th>
                <th>Waktu</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['aktivitas']) ?></td>
                <td><?= date('d-m-Y H:i:s', strtotime($row['waktu'])) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>

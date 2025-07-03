<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'config/koneksi.php';

// Step 1: Proses ubah password
$pesan = '';
if (isset($_POST['ubah_password'])) {
    $lama = $_POST['password_lama'];
    $baru = password_hash($_POST['password_baru'], PASSWORD_DEFAULT);
    $user_id = $_SESSION['user_id'];

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
    $data = mysqli_fetch_assoc($cek);

    if (password_verify($lama, $data['password'])) {
        mysqli_query($conn, "UPDATE users SET password = '$baru' WHERE id = $user_id");
        $pesan = "<div class='alert alert-success'>Password berhasil diubah!</div>";
    } else {
        $pesan = "<div class='alert alert-danger'>Password lama salah!</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pengaturan - FinTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8fbff; }
        .container { max-width: 900px; margin-top: 40px; }
        .card { border-radius: 16px; box-shadow: 0 6px 12px rgba(0,0,0,0.1); }
        .section-title { margin-bottom: 20px; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-primary text-center mb-4">⚙️ Pengaturan FinTrack</h2>

    <!-- Step 2: Notifikasi pesan -->
    <?= $pesan ?>

    <!-- Step 3: Form ubah password -->
    <div class="card mb-4 p-4">
        <h5 class="section-title text-danger">🔐 Ubah Password</h5>
        <form method="POST">
            <div class="mb-3">
                <label>Password Lama</label>
                <input type="password" name="password_lama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password Baru</label>
                <input type="password" name="password_baru" class="form-control" required>
            </div>
            <button type="submit" name="ubah_password" class="btn btn-danger">Simpan Perubahan</button>
        </form>
    </div>

    <!-- Step 4: Pengaturan Sistem (simulasi) -->
    <div class="card mb-4 p-4">
        <h5 class="section-title text-info">⚙️ Pengaturan Sistem</h5>
        <form>
            <div class="mb-3">
                <label>Mode Tampilan</label>
                <select class="form-select">
                    <option>Default (Biru Cerah)</option>
                    <option>Gelap (Dark Mode)</option>
                    <option>Putih Minimalis</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Notifikasi Email</label>
                <select class="form-select">
                    <option>Aktif</option>
                    <option>Nonaktif</option>
                </select>
            </div>
            <button class="btn btn-primary" disabled>Simpan (Simulasi)</button>
        </form>
    </div>

    <!-- Step 5: Monitoring aktivitas login -->
    <div class="card p-4">
        <h5 class="section-title text-success">📋 Monitoring Aktivitas Login</h5>
        <table class="table table-bordered table-hover bg-white">
            <thead class="table-success">
                <tr>
                    <th>No</th>
                    <th>Username</th>
                    <th>Waktu Login</th>
                    <th>Alamat IP</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $login_log = mysqli_query($conn, "SELECT * FROM login_logs ORDER BY waktu_login DESC LIMIT 10");
                $no = 1;
                while ($log = mysqli_fetch_assoc($login_log)) :
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($log['username']) ?></td>
                        <td><?= $log['waktu_login'] ?></td>
                        <td><?= $log['ip_address'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="text-center mt-4">
        <a href="dashboard.php" class="btn btn-secondary">⬅️ Kembali ke Dashboard</a>
    </div>
</div>

</body>
</html>

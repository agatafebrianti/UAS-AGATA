<?php
session_start();
require 'config/koneksi.php';

// Cek role admin
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    echo "<script>alert('Hanya admin yang dapat mengakses halaman ini.'); window.location='dashboard.php';</script>";
    exit;
}

// Proses update jika disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['setting'] as $id => $value) {
        $id = (int)$id;
        $value = mysqli_real_escape_string($conn, $value);
        mysqli_query($conn, "UPDATE settings SET setting_value = '$value' WHERE id = $id");
    }
    $success = "Pengaturan berhasil disimpan!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pengaturan Sistem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f8ff; padding: 20px; font-family: 'Segoe UI', sans-serif; }
        .container { max-width: 720px; margin: auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<div class="container">
    <h3 class="mb-4 text-primary">⚙️ Pengaturan Sistem</h3>

    <?php if (isset($success)) : ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST">
        <?php
        $result = mysqli_query($conn, "SELECT * FROM settings");
        while ($row = mysqli_fetch_assoc($result)) :
        ?>
            <div class="mb-3">
                <label class="form-label"><?= htmlspecialchars($row['setting_name']) ?></label>
                <input type="text" class="form-control" name="setting[<?= $row['id'] ?>]" value="<?= htmlspecialchars($row['setting_value']) ?>">
            </div>
        <?php endwhile; ?>
        <button type="submit" class="btn btn-primary">💾 Simpan Pengaturan</button>
        <a href="dashboard.php" class="btn btn-secondary">🔙 Kembali</a>
    </form>
</div>
</body>
</html>

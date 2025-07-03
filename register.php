<?php
require 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role_id  = 2;
    $created_at = date('Y-m-d H:i:s');

    $check = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' OR email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Username atau email sudah terdaftar!";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO users (name, email, username, password, role_id, created_at)
            VALUES ('$name', '$email', '$username', '$password', '$role_id', '$created_at')");
        if ($insert) {
            header("Location: login.php");
            exit();
        } else {
            $error = "Gagal mendaftar. Coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar - FinTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #d6ecff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .register-box {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 35px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }
        .register-title {
            font-weight: bold;
            font-size: 24px;
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .form-control {
            border-radius: 10px;
        }
        .btn-success {
            background-color: #4dafff;
            border: none;
            border-radius: 10px;
            padding: 10px;
            font-weight: 600;
        }
        .btn-success:hover {
            background-color: #1ea8ff;
        }
        .register-footer {
            text-align: center;
            margin-top: 15px;
        }
        .register-footer a {
            color: #4dafff;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="register-box">
    <div class="register-title">Daftar Akun FinTrack</div>
    <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label>Nama Lengkap</label>
            <input type="text" name="name" required class="form-control">
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" required class="form-control">
        </div>
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" required class="form-control">
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" required class="form-control">
        </div>
        <button type="submit" class="btn btn-success w-100">Daftar</button>
    </form>
    <div class="register-footer">
        Sudah punya akun? <a href="login.php">Login di sini</a>
    </div>
</div>
</body>
</html>

<?php
session_start();
require 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    $data = mysqli_fetch_assoc($cek);

    if ($data && password_verify($password, $data['password'])) {
        $_SESSION['user_id'] = $data['id'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role_id'] = $data['role_id'];

        // ✨ Tambahkan log aktivitas login
        $user_id = $data['id'];
        $aktivitas = "Login ke sistem";
        mysqli_query($conn, "INSERT INTO aktivitas (user_id, aktivitas) VALUES ('$user_id', '$aktivitas')");

        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - FinTrack</title>
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
        .login-box {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 35px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }
        .login-title {
            font-weight: bold;
            font-size: 24px;
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .form-control {
            border-radius: 10px;
        }
        .btn-primary {
            background-color: #4dafff;
            border: none;
            border-radius: 10px;
            padding: 10px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: #1ea8ff;
        }
        .login-footer {
            text-align: center;
            margin-top: 15px;
        }
        .login-footer a {
            color: #4dafff;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="login-box">
    <div class="login-title">Masuk ke FinTrack</div>
    <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" required class="form-control">
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" required class="form-control">
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
    <div class="login-footer">
        Belum punya akun? <a href="register.php">Daftar di sini</a>
    </div>
</div>
</body>
</html> 
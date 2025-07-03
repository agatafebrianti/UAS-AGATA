<?php
require 'config/koneksi.php';
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Cek apakah parameter ID ada
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // hindari SQL Injection

    // Ambil ID user login sekarang
    $user_id = $_SESSION['user_id'];

    // Hapus hanya jika data milik user yang sedang login
    $query = "DELETE FROM pengeluaran WHERE id = $id AND user_id = $user_id";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data pengeluaran berhasil dihapus.'); window.location.href='pengeluaran_list.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data.'); window.location.href='pengeluaran_list.php';</script>";
    }
} else {
    // Jika tidak ada ID, kembali ke list
    header("Location: pengeluaran_list.php");
    exit;
}
?>

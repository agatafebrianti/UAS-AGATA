<?php
require 'config/koneksi.php';

$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM pemasukan WHERE id=$id");

echo "<script>alert('Data berhasil dihapus!'); window.location.href='pemasukan_list.php';</script>";

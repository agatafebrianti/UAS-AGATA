<?php
session_start(); // Mulai session

// Hapus semua session
$_SESSION = [];
session_unset();
session_destroy();

// Redirect ke halaman login
header("Location: login.php");
exit;
?>

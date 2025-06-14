<?php
require_once 'koneksi.php';
start_session();

// Hapus semua session
session_destroy();

// Redirect ke halaman login
header("Location: index.php");
exit();
?>

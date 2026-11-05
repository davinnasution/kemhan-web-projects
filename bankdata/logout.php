<?php
session_start();
session_destroy(); // hapus semua session

// setelah logout langsung ke halaman login
header("Location: login.php");
exit();
?>
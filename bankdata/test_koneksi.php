<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'config.php';

if ($conn) {
    echo "✅ Koneksi ke database berhasil!";
} else {
    echo "❌ Koneksi gagal: " . mysqli_connect_error();
}
?>

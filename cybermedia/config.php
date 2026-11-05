<?php
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_CRAWL_NAME') ?: 'data_crawl';

$conn = mysqli_connect("$host", "$user", "$pass", "$db");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>

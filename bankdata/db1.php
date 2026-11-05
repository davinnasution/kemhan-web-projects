<?php
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_CRAWL_NAME') ?: 'data_crawl';

$conn1 = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn1) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>

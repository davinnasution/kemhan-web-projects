<?php
header('Content-Type: application/json');
include 'db1.php'; // koneksi ke database data_crawl

$bulan = [];

$sql = "SELECT DISTINCT DATE_FORMAT(tanggal, '%Y-%m') AS bln
        FROM crawl
        WHERE tanggal IS NOT NULL
        ORDER BY bln ASC";

$result = mysqli_query($conn1, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $bulan[] = $row['bln'];
}

echo json_encode($bulan);

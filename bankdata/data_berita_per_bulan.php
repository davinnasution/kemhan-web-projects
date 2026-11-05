<?php
header('Content-Type: application/json');
include 'db1.php'; // koneksi ke database data_crawl

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : "";

$data = [
    "labels" => [],
    "jumlah" => []
];

if ($bulan) {
    // Ambil jumlah berita per hari di bulan tertentu
    $sql = "SELECT DATE(tanggal) AS tgl, COUNT(*) AS total 
            FROM crawl
            WHERE MONTH(tanggal) = MONTH('$bulan-01') 
              AND YEAR(tanggal) = YEAR('$bulan-01')
            GROUP BY DATE(tanggal)
            ORDER BY tgl ASC";
} else {
    // Ambil jumlah berita per bulan
    $sql = "SELECT DATE_FORMAT(tanggal, '%Y-%m') AS bln, COUNT(*) AS total 
            FROM crawl
            GROUP BY DATE_FORMAT(tanggal, '%Y-%m')
            ORDER BY bln ASC";
}

$result = mysqli_query($conn1, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $data['labels'][] = $bulan ? $row['tgl'] : $row['bln'];
    $data['jumlah'][] = (int)$row['total'];
}

echo json_encode($data);

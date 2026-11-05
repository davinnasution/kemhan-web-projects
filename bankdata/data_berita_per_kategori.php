<?php
header('Content-Type: application/json');
include 'db1.php'; // koneksi ke database data_crawl

$data = [
    "kategori" => [],
    "jumlah" => []
];

// Ambil jumlah semua berita per kategori (aktif + nonaktif)
$sql = "SELECT 
            IFNULL(kategori, 'Tidak Ada') AS kategori, 
            COUNT(*) AS total 
        FROM crawl 
        GROUP BY kategori";

$result = mysqli_query($conn1, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $data['kategori'][] = $row['kategori'];
    $data['jumlah'][] = (int)$row['total'];
}

echo json_encode($data);

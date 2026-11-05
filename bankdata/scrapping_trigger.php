<?php
header('Content-Type: application/json');

// === 1️⃣ Koneksi ke database ===
include 'db1.php';

// Hitung total berita sebelum scraping
$sql_before = "SELECT COUNT(*) as total FROM crawl";
$result_before = $conn1->query($sql_before);
$row_before = $result_before->fetch_assoc();
$total_before = (int)$row_before['total'];

// === 2️⃣ Jalankan Python scraping ===
// Sesuaikan path ke file scraping.py kamu
$command = escapeshellcmd("python3 /var/www/html/bankdata/scraping.py 2>&1");
$output = shell_exec($command);

// === 3️⃣ Hitung total berita setelah scraping ===
$sql_after = "SELECT COUNT(*) as total FROM crawl";
$result_after = $conn1->query($sql_after);
$row_after = $result_after->fetch_assoc();
$total_after = (int)$row_after['total'];

// === 4️⃣ Hitung jumlah data yang benar-benar ditambahkan ===
$inserted = $total_after - $total_before;
if ($inserted < 0) {
    $inserted = 0; // jaga-jaga kalau ada penghapusan data
}

// === 5️⃣ Kirim response JSON ke frontend ===
echo json_encode([  
    "status"   => "ok",
    "msg"      => "Scraping selesai",
    "inserted" => $inserted,
    "total"    => $total_after,
    "log"      => $output // untuk debug kalau mau cek hasil python
]);
?>

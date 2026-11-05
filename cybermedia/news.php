<?php
require_once "config.php";

$sql = "SELECT 
            id, 
            judul AS title, 
            kategori AS category, 
            ringkasan AS content, 
            tanggal AS date, 
            link
        FROM crawl
        WHERE status = 'aktif'   -- ✅ hanya tampilkan berita aktif
        and tanggal >= CURDATE() - INTERVAL 7 DAY
        ORDER BY tanggal DESC 
        ";

$result = $conn->query($sql);
$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) { 
        // Pastikan link valid dan bisa diakses langsung
        if (!empty($row['link'])) {
            $row['link'] = preg_match('/^https?:\/\//i', $row['link'])
                ? $row['link']
                : 'http://' . $row['link'];
        } else {
            $row['link'] = '#';
        }
        $data[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($data, JSON_UNESCAPED_UNICODE);
?>

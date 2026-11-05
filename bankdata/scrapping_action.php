<?php
include 'db1.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Ambil data dari tabel hasil scraping
    $result = mysqli_query($conn1, "SELECT * FROM crawl WHERE id = $id");
    $data_scraping = mysqli_fetch_assoc($result);

    if ($data_scraping) {
        // Sanitasi data sebelum disimpan
        $judul      = mysqli_real_escape_string($conn1, $data_scraping['judul']);
        $kategori   = mysqli_real_escape_string($conn1, $data_scraping['kategori']);
        $deskripsi  = mysqli_real_escape_string($conn1, $data_scraping['ringkasan']); // ubah ke deskripsi
        $link       = mysqli_real_escape_string($conn1, $data_scraping['link']);
        $penerbit   = mysqli_real_escape_string($conn1, $data_scraping['penerbit']);

        // Simpan ke tabel publish_berita
        $query_publish = "
            INSERT INTO publish_berita 
            (scraping_id, judul, kategori, deskripsi, link, penerbit, created_at, updated_at) 
            VALUES 
            ({$data_scraping['id']}, '$judul', '$kategori', '$deskripsi', '$link', '$penerbit', NOW(), NOW())
        ";

        if (mysqli_query($conn1, $query_publish)) {
            header('Location: scrapping_berita.php');
            exit;
        } else {
            echo "❌ Gagal memproses hasil scraping: " . mysqli_error($conn1);
        }
    } else {
        echo "⚠️ Data hasil scraping tidak ditemukan.";
    }
} else {
    echo "⚠️ ID scraping tidak disertakan.";
}
?>

<?php
include 'db1.php'; // koneksi ke database

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Query hapus berdasarkan id
    $query = "DELETE FROM berita_crawl WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Berita berhasil dihapus!'); window.location.href='publish_berita.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus berita.'); window.location.href='publish_berita.php';</script>";
    }
} else {
    header("Location: publish_berita.php");
    exit;
}
?>

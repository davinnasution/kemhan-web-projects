<?php
include 'config.php';

$judul = $_POST['judul'];
$isi = $_POST['isi'];
$kategori = $_POST['kategori'];
$status = $_POST['status'];

$nama_gambar = $_FILES['gambar']['name'];
$tmp_gambar = $_FILES['gambar']['tmp_name'];

if ($nama_gambar != "") {
    move_uploaded_file($tmp_gambar, "uploads/" . $nama_gambar);
}

$sql = "INSERT INTO berita (judul, isi, kategori, gambar, status)
        VALUES ('$judul', '$isi', '$kategori', '$nama_gambar', '$status')";

if (mysqli_query($conn, $sql)) {
    echo "Berita berhasil disimpan!";
} else {
    echo "Gagal menyimpan berita: " . mysqli_error($conn);
}
?>

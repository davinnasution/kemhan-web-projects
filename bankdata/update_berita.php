<?php
include 'db1.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = (int) $_POST['id'];
    $judul = mysqli_real_escape_string($conn1, $_POST['judul']);
    $ringkasan = mysqli_real_escape_string($conn1, $_POST['deskripsi']);
    $kategori = mysqli_real_escape_string($conn1, $_POST['kategori']);

    $query = "UPDATE crawl 
              SET judul='$judul', 
                  ringkasan='$ringkasan', 
                  kategori='$kategori'
              WHERE id=$id";

    if (mysqli_query($conn1, $query)) {
        header("Location: scrapping_berita.php");
        exit;
    } else {
        die('Gagal update: ' . mysqli_error($conn1));
    }
}
?>


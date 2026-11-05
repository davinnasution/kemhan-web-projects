<?php
include 'db1.php'; // koneksi ke DB

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: publish_berita.php');
    exit;
}

$judul     = trim($_POST['judul']);
$deskripsi = trim($_POST['deskripsi']);
$kategori  = trim($_POST['kategori']);

$gambar_path = null;
$gambar_type = null;
$gambar_size = null;

if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $tmp_name = $_FILES['gambar']['tmp_name'];
    $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
    $new_name = time() . '_' . uniqid() . '.' . $ext;
    $dest = 'uploads/' . $new_name;

    if (move_uploaded_file($tmp_name, $dest)) {
        $gambar_path = $dest;
        $gambar_type = $_FILES['gambar']['type'];
        $gambar_size = $_FILES['gambar']['size'];
    }
}

$sql = "INSERT INTO publish_berita (judul, deskripsi, kategori, gambar, gambar_type, gambar_size)
        VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn1->prepare($sql);
$stmt->bind_param("sssssi", $judul, $deskripsi, $kategori, $gambar_path, $gambar_type, $gambar_size);

if ($stmt->execute()) {
    header("Location: publish_berita.php?success=1");
    exit;
} else {
    echo "Error: " . $conn->error;
}

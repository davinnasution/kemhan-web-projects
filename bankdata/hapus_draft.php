<?php
include 'db1.php'; // koneksi database

// pastikan ada id
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // hapus data dari tabel publish_berita
    $query = "DELETE FROM publish_berita WHERE id = $id";
    $result = mysqli_query($conn1, $query);

    if ($result) {
        // kalau berhasil hapus
        header("Location: publish_berita.php?msg=deleted");
        exit();
    } else {
        echo "Gagal menghapus data: " . mysqli_error($conn);
    }
} else {
    echo "ID tidak ditemukan.";
}
?>

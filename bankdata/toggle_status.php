<?php
// Pastikan tidak ada output sebelum JSON
ob_start();
error_reporting(0);
header('Content-Type: application/json; charset=utf-8');

include 'db1.php'; // koneksi database

// Bersihkan buffer sebelum kirim response
ob_clean();

// Pastikan parameter id dikirim
if (!isset($_POST['id'])) {
    echo json_encode(['status' => 'error', 'msg' => 'ID tidak dikirim']);
    exit;
}

$id = intval($_POST['id']);

// Cek apakah data dengan ID tersebut ada
$q = mysqli_query($conn1, "SELECT status FROM crawl WHERE id = $id LIMIT 1");
if (!$q || mysqli_num_rows($q) == 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Data tidak ditemukan']);
    exit;
}

$row = mysqli_fetch_assoc($q);
$current_status = strtolower(trim($row['status'] ?? ''));

// Jika kolom kosong/null → anggap 'aktif'
if ($current_status === '' || $current_status === null) {
    $current_status = 'aktif';
}

// Tentukan status baru
$new_status = ($current_status === 'aktif') ? 'unaktif' : 'aktif';

// Jalankan update
$update = mysqli_query($conn1, "UPDATE crawl SET status = '$new_status' WHERE id = $id");

// Jika berhasil, kirimkan JSON bersih
if ($update) {
    // Hentikan semua output buffering dan kirim JSON
    ob_clean();
    echo json_encode(['status' => $new_status]);
} else {
    ob_clean();
    echo json_encode(['status' => 'error', 'msg' => mysqli_error($conn1)]);
}

exit;
?>

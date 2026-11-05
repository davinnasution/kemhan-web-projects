<?php
include 'db1.php';
header('Content-Type: application/json; charset=utf-8');
ob_clean();

if (!isset($_POST['status'])) {
    echo json_encode(['success' => false, 'message' => 'Status tidak dikirim']);
    exit;
}

$status = $_POST['status'] === 'aktif' ? 'aktif' : 'unaktif';

$query = "UPDATE crawl SET status = '$status'";
if (mysqli_query($conn1, $query)) {
    echo json_encode(['success' => true, 'message' => "Semua data berhasil diubah menjadi '$status'"]);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal memperbarui data']);
}
?>

<?php
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id_profile'];
    $username = trim($_POST['username']);
    $nama = trim($_POST['nama']);

    // Update profil
    $sql = "UPDATE users SET username = ?, nama_lengkap = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $username, $nama, $id);
    $stmt->execute();

    // Cek apakah ada permintaan ganti password
    if (!empty($_POST['old_password']) && !empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Ambil password lama dari DB (plain text)
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && $old_password === $user['password']) {
            if ($new_password === $confirm_password) {
                // Simpan password baru (plain text dulu, nanti bisa diubah ke hash)
                $sql = "UPDATE users SET password = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $new_password, $id);
                $stmt->execute();

                $_SESSION['success'] = "Profil & Password berhasil diperbarui!";
            } else {
                $_SESSION['error'] = "Password baru dan konfirmasi tidak cocok!";
            }
        } else {
            $_SESSION['error'] = "Password lama salah!";
        }
    } else {
        $_SESSION['success'] = "Profil berhasil diperbarui!";
    }

    header("Location: edit_profile.php");
    exit();
}

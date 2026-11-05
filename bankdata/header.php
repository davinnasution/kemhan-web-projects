<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyber Bank</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="logo">
        <img src="logo-kemhan.png" alt="Logo Kemhan">
        <h2>CYBER BANK</h2>
    </div>
    <ul class="menu">
        <li><a href="dashboard.php"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
        <li>
            <i class="fa-solid fa-newspaper"></i> Berita
            <ul class="submenu">
                <li><a href="publish_berita.php">Publish Berita</a></li>
                <li><a href="draft_berita.php" class="active">Draft Berita</a></li>
            </ul>
        </li>
        <li><a href="admin.php"><i class="fa-solid fa-user"></i> Admin</a></li>
        <li><a href="log_aktivitas.php"><i class="fa-solid fa-clipboard-list"></i> Log Aktivitas</a></li>
        <li><a href="logout.php"><i class="fa-solid fa-power-off"></i> Log Out</a></li>
    </ul>
</div>

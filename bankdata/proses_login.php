<?php
// ===============================
// 1. Atur Cookie Session Aman
// ===============================
session_set_cookie_params([
    'lifetime' => 600,           // 30 menit
    'path'     => '/',
    'domain'   => '',
    'secure'   => isset($_SERVER['HTTPS']), // hanya aktif kalau HTTPS
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();
include "config.php";

// ===============================
// 2. Timeout Sesi (Idle Timeout)
// ===============================
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit;
}
$_SESSION['last_activity'] = time();

// ===============================
// 3. Proses Login
// ===============================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = trim(mysqli_real_escape_string($conn, $_POST['username']));
    $password = trim(mysqli_real_escape_string($conn, $_POST['password']));

    $sql = "SELECT * FROM users WHERE username='$username' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // ===============================
        // Password masih plaintext
        // ===============================
        if ($password === $row['password']) {

            // ===============================
            // 4. Rotasi Session Setelah Login
            // ===============================
            session_regenerate_id(true);

            // Simpan data ke session
            $_SESSION['id']          = $row['id'];
            $_SESSION['username']    = $row['username'];
            $_SESSION['login_time']  = time();      // waktu login
            $_SESSION['last_activity'] = time();     // untuk idle timeout

            // ===============================
            // 5. Validasi sesi dari sisi server
            //    (lock session ke User-Agent & IP)
            // ===============================
            $_SESSION['user_agent']  = $_SERVER['HTTP_USER_AGENT'];
            $_SESSION['ip_address']  = $_SERVER['REMOTE_ADDR'];

            header("Location: dashboard.php");
            exit;
        } 
        else {
            echo "<script>alert('Gagal login!'); window.location='login.php';</script>";
        }
    } 
    else {
        echo "<script>alert('Gagal login!'); window.location='login.php';</script>";
    }
}
?>

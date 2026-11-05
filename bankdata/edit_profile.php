<?php include 'sidebar.php'; ?>
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';

// Ambil data user dari database berdasarkan username session
$username_session = $_SESSION['username'];
$sql = "SELECT id, username, nama_lengkap, password FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username_session);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Profile - Cyber Bank</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background: #fff;
      overflow-x: hidden;
    }

    .main {
      margin-left: 220px;
      min-height: 100vh;
    }

    /* === HEADER DIBUAT SAMA SEPERTI DASHBOARD === */
.header {
    background: #ffffff;
    color: #000;
    padding: 15px 20px;
    font-size: 20px;
    font-weight: bold;
    display: flex;
    justify-content: space-between;
    align-items: center;

    position: sticky;
    top: 0;
    z-index: 100;

    margin-left: 10px;
    border-bottom: none; /* tidak perlu lagi */
    box-shadow: 0 6px 20px -6px rgba(0,0,0,0.15);
}

/* Garis bawah bergerak */
.header::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0;

    width: 100%;
    height: 4px; /* ketebalan garis */

    background: linear-gradient(135deg, #800000, #0d1b2a, #1b263b);
    background-size: 300% 300%;
    animation: gradientShift 15s ease infinite;
}

 .header h1 {
      margin: 0;
      font-size: 20px;
      background: linear-gradient(135deg, #800000 15%, #0d1b2a 75%, #1b263b 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    }

    .header span {
      font-size: 14px;
      background: linear-gradient(135deg, #800000 15%, #0d1b2a 75%, #1b263b 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    }

    .container {
      width: 1000px;
      margin: 20px 40px;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .btn-group {
      margin-bottom: 20px;
      display: flex;
      gap: 10px;
    }

    .btn {
      padding: 8px 15px;
      border: none;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      font-size: 14px;
    }

    .btn-yellow {
      background: #facc15;
      color: #7c2d12;
    }

    .btn-blue {
      background: #2563eb;
      color: #fff;
    }

    .btn:hover {
      opacity: 0.9;
    }

    .profile-box {
      display: flex;
      gap: 25px;
      padding: 25px;
      border-radius: 14px;
      border: 1px solid rgba(139, 29, 29, 0.25);
      background: linear-gradient(145deg, #ffffff, #f5f5f5);
      box-shadow: 0 8px 22px rgba(0,0,0,0.15);
      position: relative;
      overflow: hidden;
    }

    .profile-box::before {
      content: "";
      position: absolute;
      inset: 0;
      pointer-events: none;
    }

    .avatar {
      width: 130px;
      height: 130px;
      border-radius: 14px;
      overflow: hidden;
      background: linear-gradient(135deg, #d9d9d9, #f7f7f7);
      border: 1px solid #c8c8c8;
      box-shadow: 0 6px 15px rgba(0,0,0,0.2);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .avatar img {
      border-radius: 8px;
      width: 100%;
      height: 100%;
    }

    .profile-form {
      flex: 1;
      padding: 20px;
      border-radius: 12px;
      border: 1px solid rgba(178, 34, 34, 0.25);
      background: #ffffff;
      box-shadow: inset 0 0 12px rgba(0,0,0,0.05);
    }

    .profile-form label {
      font-weight: bold;
      display: block;
      margin-top: 10px;
      margin-bottom: 5px;
    }

    .profile-form input {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #cfcfcf;
      background: #fafafa;
      transition: 0.2s;
    }

    .profile-form input:focus {
      border-color: #8b1d1d;
      box-shadow: 0 0 6px rgba(139,29,29,0.35);
      background: #fff;
    }

    .save-btn {
      margin-top: 15px;
      padding: 12px 22px;
      border: none;
      background: #8b1d1d;
      color: #fff;
      font-weight: bold;
      border-radius: 8px;
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(139,29,29,0.3);
      transition: 0.2s;
    }

    .save-btn:hover {
      background: #a12222;
      box-shadow: 0 6px 18px rgba(139,29,29,0.4);
    }

    hr {
      margin: 20px 0;
      border: none;
      border-top: 2px solid #b22222;
      opacity: 0.4;
    }

  </style>
</head>
<body>

<div class="main">
  <!-- === HEADER MIRIP DASHBOARD === -->
  <div class="header">
    <h1>Edit Profile</h1>
    <span>Selamat datang, <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username']); ?></span>
  </div>

  <div class="container">

  <?php if(isset($_SESSION['success'])): ?>
    <div style="padding:10px; background:#d1fae5; color:#065f46; border:1px solid #10b981; margin-bottom:15px; border-radius:5px;">
      <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
  <?php endif; ?>

  <?php if(isset($_SESSION['error'])): ?>
    <div style="padding:10px; background:#fee2e2; color:#991b1b; border:1px solid #ef4444; margin-bottom:15px; border-radius:5px;">
      <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
  <?php endif; ?>

  <div class="btn-group">
    <a href="admin.php" class="btn btn-yellow"><i class="fa-solid fa-user"></i> Profile</a>
  </div>

  <!-- FORM EDIT PROFIL + GANTI PASSWORD -->
  <form class="profile-box" method="post" action="simpan_profile.php">
    <div class="avatar">
      <img src="https://placehold.co/120x120/png?text=User+Avatar" alt="User Avatar">
    </div>
    <div class="profile-form">
      <label for="id_profile">ID Profile</label>
      <input type="text" id="id_profile" name="id_profile" value="<?= htmlspecialchars($user['id']); ?>" readonly>

      <label for="username">Username</label>
      <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']); ?>">

      <label for="nama">Nama Lengkap</label>
      <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($user['nama_lengkap']); ?>">

      <!-- Bagian Ganti Password -->
      <hr>
      <h3 style="color:#1d4ed8;"><i class="fa-solid fa-lock"></i> Ganti Password</h3>

      <label for="old_password">Password Lama</label>
      <input type="password" id="old_password" name="old_password">

      <label for="new_password">Password Baru</label>
      <input type="password" id="new_password" name="new_password">

      <label for="confirm_password">Konfirmasi Password Baru</label>
      <input type="password" id="confirm_password" name="confirm_password">

      <button type="submit" class="save-btn">
        <i class="fa-solid fa-save"></i> Simpan Perubahan
      </button>
    </div>
  </form>

  </div>
</div>

</body>
</html>

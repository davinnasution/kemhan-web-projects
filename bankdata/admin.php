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

// Ambil ID user dari session
$id_user = $_SESSION['id'];

// Query ambil data user (termasuk role)
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Variabel user siap dipakai
$username     = $user['username'];
$nama_lengkap = $user['nama_lengkap'];
$role         = $user['role']; // ← tambahan untuk menampilkan role

// ==== PROSES TAMBAH USER ====
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['tambah_user'])) {
    $new_username = trim($_POST['new_username']);
    $new_password = trim($_POST['new_password']);
    $new_nama     = trim($_POST['new_nama']);
    $new_role     = $_POST['new_role'];

    if ($new_username && $new_password && $new_role) {
        $sql_add = "INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)";
        $stmt_add = $conn->prepare($sql_add);
        $stmt_add->bind_param("ssss", $new_username, $new_password, $new_nama, $new_role);
        if ($stmt_add->execute()) {
            $success_msg = "✅ User baru berhasil ditambahkan.";
        } else {
            $error_msg = "❌ Gagal menambahkan user.";
        }
    } else {
        $error_msg = "⚠️ Semua field harus diisi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Profile - Cyber Bank</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #fff;
      overflow-x: hidden;
    }

    .main {
      margin-left: 220px;
      min-height: 100vh;
    }

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
      background-color: #fff;
      padding: 30px;
      border-radius: 10px;
      color: #000;
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
      background: rgba(255, 255, 255, 0.55);
      border-radius: 15px;
      border: 1px solid rgba(139, 29, 29, 0.25);
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      position: relative;
      overflow: hidden;
    }

    .profile-box::before {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(140deg,
      rgba(255,255,255,0.35),
      rgba(255,255,255,0.05));
      z-index: 0;
      pointer-events: none;
    }

    .avatar {
      width: 130px;
      height: 130px;
      border-radius: 18px;
      overflow: hidden;
      background: linear-gradient(145deg, #d9d9d9, #f7f7f7);
      box-shadow: 0 6px 12px rgba(0,0,0,0.2);
      position: relative;
      z-index: 1;
    }

    .avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .profile-info {
      flex: 1;
      padding: 20px;
      border-radius: 15px;
      border: 1px solid rgba(178, 34, 34, 0.25);
      background: rgba(255, 255, 255, 0.45);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      box-shadow: inset 0 0 12px rgba(0,0,0,0.08);
      position: relative;
      z-index: 1;
    }

    .profile-info p {
      font-size: 15px;
      margin: 8px 0;
      font-weight: 500;
      color: #333;
    }

    .profile-info strong {
      color: #8b1d1d;
      font-weight: 700;
    }

/* === FORM TAMBAH USER (glassmorphism ringan) === */
    .add-user-form {
      margin-top: 30px;
      background: rgba(255, 255, 255, 0.25);
      backdrop-filter: blur(15px);
      -webkit-backdrop-filter: blur(15px);
      border-radius: 15px;
      border: 1px solid rgba(255, 255, 255, 0.4);
      box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2);
      padding: 25px;
      color: #000;
      position: relative;
      overflow: hidden;
    }

    .add-user-form::before {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, rgba(255,255,255,0.3), rgba(255,255,255,0.05));
      border-radius: 15px;
      z-index: 0;
    }

    .add-user-form h3 {
      margin-top: 0;
      color: #8b1d1d;
      font-weight: bold;
      position: relative;
      z-index: 1;
    }

    .form-group {
      margin-bottom: 15px;
      position: relative;
      z-index: 1;
    }

    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }

    .form-group input,
    .form-group select {
      width: 100%;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 14px;
    }

/* === ALERT === */
    .alert {
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 15px;
      font-size: 14px;
      z-index: 1;
      position: relative;
    }

    .alert-success {
      background: #d1fae5;
      color: #065f46;
      border: 1px solid #10b981;
    }

    .alert-error {
      background: #fee2e2;
      color: #7f1d1d;
      border: 1px solid #ef4444;
    }

/* === TABEL USER MODERN GLOSSY === */
    .modern-table {
      width: 100%;
      border-collapse: collapse;
      background: rgba(255, 255, 255, 0.3);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 16px rgba(0,0,0,0.1);
      font-size: 14px;
      z-index: 1;
      position: relative;
    }

    .modern-table th {
      background: linear-gradient(135deg, #b22222, #8b1d1d);
      color: #fff;
      text-align: left;
      padding: 12px;
      font-weight: 600;
      text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }

    .modern-table td {
      padding: 10px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.3);
      color: #222;
    }

    .modern-table tr:hover {
      background: rgba(255, 255, 255, 0.55);
      transition: 0.3s ease;
    }

/* === BADGE ROLE GLOSSY === */
    .badge {
      padding: 5px 12px;
      border-radius: 12px;
      font-size: 13px;
      font-weight: bold;
      letter-spacing: 0.3px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.15);
    }

    .badge-admin {
      background: linear-gradient(135deg, #ff5f5f, #b22222);
      color: #fff;
    }

    .badge-user {
      background: linear-gradient(135deg, #4f9cff, #2563eb);
      color: #fff;
    }

  </style>
</head>
<body>

<div class="main">
  <div class="header">
    <h1>Admin Profile</h1>
    <span>Selamat datang, <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username']); ?></span>
  </div>

  <div class="container">

    <div class="btn-group">
      <a href="edit_profile.php" class="btn btn-yellow">
        <i class="fa-solid fa-edit"></i> Edit Profile
      </a>

      <!-- Button Tambah User hanya muncul untuk admin -->
      <?php if ($role === 'admin'): ?>
      <button class="btn btn-blue" onclick="toggleForm()">
        <i class="fa-solid fa-user-plus"></i> Tambah User
      </button>
      <?php endif; ?>
    </div>

    <?php if (!empty($success_msg)): ?>
      <div class="alert alert-success"><?= $success_msg; ?></div>
    <?php elseif (!empty($error_msg)): ?>
      <div class="alert alert-error"><?= $error_msg; ?></div>
    <?php endif; ?>

    <div class="profile-box">
      <div class="avatar">
        <img src="https://placehold.co/120x120/png?text=User+Avatar" alt="User Avatar">
      </div>
      <div class="profile-info">
        <p><strong>ID Profile :</strong> <?= $user['id']; ?></p>
        <p><strong>Username :</strong> <?= htmlspecialchars($username); ?></p>
        <p><strong>Nama Lengkap :</strong> <?= htmlspecialchars($nama_lengkap); ?></p>
        <p><strong>Role :</strong> <?= htmlspecialchars($role); ?></p>
      </div>
    </div>

    <!-- FORM TAMBAH USER -->
    <?php if ($role === 'admin'): ?>
    <div id="formTambahUser" class="add-user-form" style="display:none;">
      <h3><i class="fa-solid fa-user-plus"></i> Tambah User Baru</h3>
      <form method="POST">
        <div class="form-group">
          <label>Username</label>
          <input type="text" name="new_username" required>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="new_password" required>
        </div>
        <div class="form-group">
          <label>Nama Lengkap</label>
          <input type="text" name="new_nama">
        </div>
        <div class="form-group">
          <label>Role</label>
          <select name="new_role" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <button type="submit" name="tambah_user" class="btn btn-blue">
          <i class="fa-solid fa-save"></i> Simpan
        </button>
      </form>
    </div>
    <?php endif; ?>

    <?php if ($role === 'admin'): ?>
    <!-- === DAFTAR USER (UNTUK SEMUA ROLE) === -->
    <div class="add-user-form" style="margin-top:30px;">
      <h3><i class="fa-solid fa-users"></i> Daftar Pengguna Sistem</h3>
      <?php
        $sql_users = "SELECT id, username, nama_lengkap, role FROM users ORDER BY id ASC";
        $result_users = $conn->query($sql_users);
        if ($result_users && $result_users->num_rows > 0):
      ?>
      <div style="overflow-x:auto;">
        <table class="modern-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Username</th>
              <th>Nama Lengkap</th>
              <th>Role</th>
            </tr>
          </thead>
       <tbody>

  <?php $no = 1; ?>
  <?php while ($row = $result_users->fetch_assoc()): ?>
  <tr>
    <td><?= $no++; ?></td>
              <td><?= htmlspecialchars($row['username']); ?></td>
              <td><?= htmlspecialchars($row['nama_lengkap'] ?? '-'); ?></td>
              <td>
                <?php if ($row['role'] === 'admin'): ?>
                  <span class="badge badge-admin">Admin</span>
                <?php else: ?>
                  <span class="badge badge-user">User</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
	    <?php endif; ?> 
	</div> 
	   <?php endif; ?> 
	</div>
      </div>
</div>

<script>
function toggleForm() {
  const form = document.getElementById("formTambahUser");
  form.style.display = (form.style.display === "none") ? "block" : "none";
}
</script>

</body>
</html>

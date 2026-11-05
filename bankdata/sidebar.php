<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Deteksi halaman aktif otomatis
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BankData</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>

        /* ========================================
           🔥 Background gradasi bergerak (sama login)
        ========================================= */
 body {
            margin: 0;
            font-family: "Inter", sans-serif;
            background: #f6f7fb;
            color: #333;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* ========================================
           🔥 Sidebar transparan (glassmorphism)
        ========================================= */
        .sidebar {
            width: 200px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;

	     background: linear-gradient(135deg, #800000, #0d1b2a, #1b263b);
    background-size: 300% 300%;
    animation: gradientShift 15s ease infinite;
            backdrop-filter: blur(10px);              /* Efek kaca */
            border-right: 1px solid rgba(255,255,255,0.35);

            padding: 15px;
            box-shadow: 10px 0 30px rgba(0,0,0,0.25);
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            z-index: 150;
        }

        .sidebar .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar .logo img {
            width: 120px;
            height: auto;
            margin-bottom: 5px;
        }

        .sidebar .logo h2 {
            font-family: "Inter", sans-serif !important;
            font-size: 16px !important;
            font-weight: 700 !important;
            color: #fff !important;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .menu li {
            margin-bottom: 8px;
        }

        .menu li a,
        .menu li span {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #f0f0f0;
            font-weight: 500;
            font-size: 14px;
            padding: 10px 12px;
            border-radius: 8px;
            transition: all 0.25s ease;
        }

        .menu li i {
            margin-right: 10px;
            font-size: 15px;
            color: #fff;
        }

        .menu li:hover a,
        .menu li:hover span {
            background: rgba(255,255,255,0.12);
            color: #fff;
        }

        /* ========================================
           🔥 ACTIVE MENU → Transparan seperti login-container
        ========================================= */
        .menu li.active > a,
        .menu li.active > span {
            background: rgba(255, 255, 255, 0.15);   /* Transparan */
            color: #fff !important;
            border: 1px solid rgba(255, 255, 255, 0.35);
            backdrop-filter: blur(10px);
            box-shadow: 0 0 10px rgba(255,255,255,0.25);
            transform: translateX(5px);
        }

        .menu li.active i {
            color: #fff;
        }

        .submenu {
            list-style: none;
            padding-left: 25px;
            display: none;
        }

        .menu li:hover .submenu {
            display: block;
        }

        .submenu li a {
            font-size: 13px;
            color: #eee;
            padding: 8px 0;
        }

        .submenu li a:hover {
            color: #fff;
        }

        /* Popup tetap sama */
        .popup-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.35);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .popup-box {
            background: #fff;
            padding: 22px;
            border-radius: 10px;
            text-align: center;
            width: 260px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.12);
        }

        .popup-box h3 {
            margin-bottom: 18px;
            font-weight: 500;
            color: #444;
        }

        .popup-box button {
            border: none;
            border-radius: 6px;
            padding: 8px 14px;
            margin: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.3s;
        }

        .popup-box .yes {
            background: #c62828;
            color: white;
        }

        .popup-box .yes:hover {
            background: #b71c1c;
        }

        .popup-box .no {
            background: #e0e0e0;
        }

        .popup-box .no:hover {
            background: #ccc;
        }
    </style>
</head>

<body>

<!-- ===== SIDEBAR ===== -->
<div class="sidebar">
    <div class="logo">
        <img src="logo-kemhan.png" alt="Logo Kemhan">
        <h2>
          BankData<br>
          Sistem Crawling Informasi Siber<br>
          Pussiber Inteltekhan Kemhan<br>
        </h2>
    </div>

    <ul class="menu">
        <li class="<?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
            <a href="dashboard.php"><i class="fa-solid fa-table-columns"></i> Dashboard</a>
        </li>

        <li class="<?= ($current_page == 'scrapping_berita.php' || $current_page == 'publish_berita.php') ? 'active' : '' ?>">
            <span><i class="fa-solid fa-newspaper"></i> Berita <i class="fa fa-caret-down" style="margin-left:auto;"></i></span>
            <ul class="submenu">
                <li><a href="scrapping_berita.php">Scrapping Berita</a></li>
            </ul>
        </li>

        <li class="<?= ($current_page == 'admin.php') ? 'active' : '' ?>">
            <a href="admin.php"><i class="fa-solid fa-user"></i> Profile</a>
        </li>

        <li><a href="#" onclick="return confirmLogout(event)"><i class="fa-solid fa-power-off"></i> Log Out</a></li>
    </ul>
</div>

<script>
function confirmLogout(event) {
    event.preventDefault();
    let confirmBox = document.createElement("div");
    confirmBox.classList.add("popup-overlay");
    confirmBox.innerHTML = `
        <div class="popup-box">
            <h3>Apakah Anda yakin ingin logout?</h3>
            <button class="yes" id="yesBtn">Yes</button>
            <button class="no" id="noBtn">No</button>
        </div>
    `;
    document.body.appendChild(confirmBox);
    document.getElementById("yesBtn").onclick = function() {
        window.location.href = "logout.php";
    };
    document.getElementById("noBtn").onclick = function() {
        confirmBox.remove();
    };
    return false;
}
</script>

</body>
</html>

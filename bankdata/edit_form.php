<?php 
include 'sidebar.php';
include 'db1.php';

// Ambil ID berita dari parameter URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Coba ambil dari publish_berita dulu
$query_publish = "SELECT judul, deskripsi, kategori, gambar FROM publish_berita WHERE id = $id LIMIT 1";
$result_publish = mysqli_query($conn1, $query_publish);
$data = mysqli_fetch_assoc($result_publish);

// Jika tidak ditemukan di publish_berita, ambil dari crawl
if (!$data) {
    $query_crawl = "SELECT judul, ringkasan AS deskripsi, kategori FROM crawl WHERE id = $id LIMIT 1";
    $result_crawl = mysqli_query($conn1, $query_crawl);
    $data = mysqli_fetch_assoc($result_crawl);
}

// Jika tetap tidak ditemukan
if (!$data) {
    die("<h2 style='color:red; text-align:center;'>Data tidak ditemukan!</h2>");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Berita - Cyber Bank</title>
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

    /* HEADER */
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
        .content {
            flex: 1;
            padding: 20px;
        }

.container {
    max-width: 800px;
    margin: auto;
    padding: 30px;
    border-radius: 16px;

    /* 🌈 GRADIENT SAMA KAYA SIDEBAR */
    background: linear-gradient(135deg, #800000, #0d1b2a, #1b263b);
    background-size: 300% 300%;
    animation: gradientShift 15s ease infinite;

    /* 🔥 GLASSMORPHISM */
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);

    /* ✨ BORDER PUTIH GLOSSY */
    border: 1px solid rgba(255,255,255,0.35);

    /* 🌟 BAYANGAN */
    box-shadow: 0 10px 28px rgba(0, 0, 0, 0.35);

    /* TEXT WARNA */
    color: #fff;
}

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"], textarea, select {
            width: 100%;
            padding: 8px;
            border: none;
            border-radius: 4px;
            box-sizing: border-box;
	    font-family: 'Poppins', sans-serif;
        }

        input[type="file"] {
            background-color: white;
            color: black;
            padding: 5px;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .current-image {
            margin: 10px 0;
        }

        .current-image img {
            max-width: 200px;
            border-radius: 6px;
        }

    </style>
</head>
<body>

<!-- MAIN CONTENT -->
<div class="main">
    <!-- ===== HEADER SESUAI DASHBOARD ===== -->
    <div class="header">
        <h1>Edit Berita</h1>
        <span>Selamat datang, <?= $_SESSION['nama'] ?? $_SESSION['username'] ?></span>
    </div>

    <div class="content">
        <div class="container">
            <form action="update_berita.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $id; ?>">

                <div class="form-group">
                    <label>Judul Berita</label>
                    <input type="text" name="judul" value="<?php echo htmlspecialchars($data['judul']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Deskripsi Berita</label>
                    <textarea name="deskripsi" rows="4" required><?php echo htmlspecialchars($data['deskripsi']); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="malware" <?php if($data['kategori']=="malware") echo "selected"; ?>>Malware</option>
                        <option value="serangan-siber" <?php if($data['kategori']=="serangan-siber") echo "selected"; ?>>Serangan Siber</option>
                        <option value="teknologi" <?php if($data['kategori']=="teknologi") echo "selected"; ?>>Teknologi</option>
                        <option value="keamanan" <?php if($data['kategori']=="keamanan") echo "selected"; ?>>Keamanan</option>
			<option value="kebocoran-data" <?php if($data['kategori']=="kebocoran-data") echo "selected"; ?>>Kebocoran Data</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
                <a href="scrapping_berita.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>

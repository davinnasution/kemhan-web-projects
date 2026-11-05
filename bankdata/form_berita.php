<?php 
include 'sidebar.php';
include 'db1.php';

// Ambil ID berita dari parameter URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Query data berita dari tabel crawl
$query = "SELECT judul, ringkasan, kategori FROM crawl WHERE id = $id LIMIT 1"; 
$result = mysqli_query($conn1, $query);
$data = mysqli_fetch_assoc($result);

// Ambil daftar kategori unik dari crawl
$kategori_query = "SELECT DISTINCT kategori FROM crawl ORDER BY kategori ASC";
$kategori_result = mysqli_query($conn1, $kategori_query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publish Berita - Cyber Bank</title>
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

        /* ===== HEADER STYLE SESUAI DASHBOARD ===== */
        .header {
            background-color: #ffffff; /* putih */
            color: #b22222;           /* teks merah tua */
            padding: 15px 20px;
            font-size: 20px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #b22222; /* garis merah bawah */
            box-shadow: 0 6px 20px -6px rgba(0, 0, 0, 0.25); /* shadow bawah */
            position: sticky;
            top: 0;
            z-index: 100;
            margin-left: 10px; /* agar tidak nempel ke sidebar */
        }

        .header h1 { margin: 0; font-size: 20px; }
        .header span { font-size: 14px; }

        .content {
            flex: 1;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background-color: #8b1d1d;
            padding: 30px;
            border-radius: 10px;
            color: white;
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
            /* == BARIS YANG DITAMBAHKAN == */
            font-family: Arial, sans-serif; /* Menyamakan jenis font */
            font-size: 14px;               /* Menyamakan ukuran font */
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
    </style>
</head>
<body>

<div class="main">
    <div class="header">
        <h1>Publish Berita</h1>
    </div>

    <div class="content">
        <div class="container">
            <form action="simpan_berita.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $id; ?>">

                <div class="form-group">
                    <label>Judul Berita</label>
                    <input type="text" name="judul" value="<?php echo htmlspecialchars($data['judul']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Deskripsi Berita</label>
                    <textarea name="deskripsi" rows="4" required><?php echo htmlspecialchars($data['ringkasan']); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php while ($row = mysqli_fetch_assoc($kategori_result)) { ?>
                            <option value="<?php echo $row['kategori']; ?>" 
                                <?php if ($data['kategori'] == $row['kategori']) echo "selected"; ?>>
                                <?php echo ucfirst($row['kategori']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="publish_berita.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>
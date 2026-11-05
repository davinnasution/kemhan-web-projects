<?php
include 'sidebar.php';
include 'db1.php'; // koneksi database

// Sinkronisasi untuk mengisi kolom penerbit dan link yang kosong
$syncQuery = "
UPDATE publish_berita pb
JOIN crawl c ON pb.judul = c.judul
SET pb.penerbit = c.penerbit,
    pb.link = c.link
WHERE pb.penerbit IS NULL OR pb.link IS NULL
";
mysqli_query($conn1, $syncQuery);

// Cek apakah ada kategori yang dipilih
$kategori_filter = isset($_GET['kategori']) ? mysqli_real_escape_string($conn1, $_GET['kategori']) : '';

// Query dengan filter kategori + urut berdasarkan tanggal dibuat
$query = "SELECT * FROM publish_berita";
if ($kategori_filter != '') {
    $query .= " WHERE kategori = '$kategori_filter'";
}
$query .= " ORDER BY created_at DESC";

$result = mysqli_query($conn1, $query);

// Ambil semua kategori unik buat dropdown
$kategori_list = mysqli_query($conn1, "SELECT DISTINCT kategori FROM publish_berita ORDER BY kategori ASC");

// Fungsi ambil domain penerbit dari link
function getPenerbit($url) {
    $host = parse_url($url, PHP_URL_HOST);
    $host = preg_replace('/^www\./', '', $host);
    return $host ? $host : 'Tidak diketahui';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Publish Berita</title>
  <style>
    /* === GENERAL & LAYOUT === */
    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        margin: 0;
        background-color: #f4f6f9;
        color: #333;
    }
    .main {
        margin-left: 220px;
        padding: 0;
    }

    /* ===== HEADER STYLE ASLI ===== */
    .header {
        background-color: #ffffff;
        color: #b22222;
        padding: 15px 25px;
        font-weight: bold;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 3px solid #b22222;
        box-shadow: 0 6px 20px -6px rgba(0, 0, 0, 0.25);
        position: sticky;
        top: 0;
        z-index: 100;
    }
    .header h1 { margin: 0; font-size: 20px; }
    .header span { font-size: 14px; }
    
    /* Area Konten */
    .content-area {
        padding: 25px;
    }

    /* === CARD STYLE === */
    .card {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        padding: 25px;
    }

    /* === ALERT MESSAGE === */
    .alert {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        padding: 10px 15px;
        margin-bottom: 20px;
        border-radius: 6px;
    }
    
    /* === FILTER AREA === */
    .filter-form {
        display: flex;
        gap: 10px;
        margin-bottom: 25px;
        align-items: center;
    }
    .filter-form label {
        font-weight: 600;
        color: #555;
    }
    .filter-form select, .filter-form button {
        padding: 8px 12px;
        border-radius: 6px;
        border: 1px solid #ccc;
        font-size: 14px;
    }
    .filter-form button {
        background-color: #b22222;
        color: white;
        cursor: pointer;
        border-color: #b22222;
    }

    /* === TABLE STYLE === */
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    .data-table th, .data-table td {
        padding: 14px 16px;
        text-align: left;
        border-bottom: 1px solid #dee2e6;
        vertical-align: top;
    }
    .data-table thead th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
    }
    .data-table tbody tr:nth-child(even) {
        background-color: #f8f9fa; /* Zebra stripes */
    }
    .data-table tbody tr:hover {
        background-color: #e9ecef;
    }
    .description-cell {
        max-width: 250px;
        white-space: normal;
        word-wrap: break-word;
        font-size: 14px;
        color: #555;
    }

    /* === BUTTONS IN TABLE === */
    .actions-cell {
        white-space: nowrap;
    }
    .btn {
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        color: white;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s ease;
        display: inline-block;
        margin-bottom: 5px;
    }
    .btn:hover { opacity: 0.85; transform: translateY(-1px); }
    .btn-link { background-color: #17a2b8; }   /* Cyan */
    .btn-edit { background-color: #007bff; }   /* Biru */
    .btn-hapus { background-color: #dc3545; }  /* Merah */
  </style>
</head>
<body>

<div class="main">
    <div class="header">
        <h1>Publish Berita</h1>
        <span>Selamat datang, <?= htmlspecialchars($_SESSION['nama'] ?? $_SESSION['username'] ?? 'User', ENT_QUOTES, 'UTF-8') ?> 👋</span>
    </div>

    <div class="content-area">
        <div class="card">
            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
                <div class="alert">✅ Data berhasil diperbarui.</div>
            <?php endif; ?>
            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
                <div class="alert">✅ Data berhasil dihapus.</div>
            <?php endif; ?>

            <form method="GET" class="filter-form">
                <label for="kategori">Filter Kategori:</label>
                <select name="kategori" id="kategori">
                    <option value="">-- Semua Kategori --</option>
                    <?php while ($kat = mysqli_fetch_assoc($kategori_list)): ?>
                        <option value="<?= htmlspecialchars($kat['kategori']) ?>" <?= ($kategori_filter == $kat['kategori']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars(ucfirst($kat['kategori'])) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit">Tampilkan</button>
            </form>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Deskripsi</th>
                        <th>Kategori</th>
                        <th>Dibuat</th>
                        <th>Diupdate</th>
                        <th>Penerbit</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php $no = 1; while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['judul']) ?></td>
                                <td class="description-cell"><?= htmlspecialchars(substr($row['deskripsi'], 0, 100)) ?>...</td>
                                <td><?= htmlspecialchars($row['kategori']) ?></td>
                                <td><?= date('d M Y H:i', strtotime($row['created_at'])) ?></td>
                                <td><?= date('d M Y H:i', strtotime($row['updated_at'])) ?></td>
                                <td><?= htmlspecialchars(getPenerbit($row['link'])) ?></td>
                                <td class="actions-cell" style="text-align: center;">
                                    <?php if (!empty($row['link'])): ?>
                                        <a href="<?= htmlspecialchars($row['link']) ?>" target="_blank" class="btn btn-link">Link</a>
                                    <?php endif; ?>
                                    <a href="edit_form.php?id=<?= $row['id'] ?>" class="btn btn-edit">Edit</a>
                                    <a href="hapus_draft.php?id=<?= $row['id'] ?>" class="btn btn-hapus" onclick="return confirm('Yakin ingin menghapus berita ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 20px;">Tidak ada berita yang sesuai dengan filter.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
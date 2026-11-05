<?php
include 'sidebar.php';
include 'db1.php'; // koneksi database

// Cek filter kategori & penerbit
$kategori_filter = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$penerbit_filter = isset($_GET['penerbit']) ? $_GET['penerbit'] : '';

// Proses hapus multiple berita
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_selected'])) {
    if (!empty($_POST['selected_id'])) {
        $ids = implode(",", array_map('intval', $_POST['selected_id']));
        $delete_query = "DELETE FROM crawl WHERE id IN ($ids)";
        mysqli_query($conn1, $delete_query);
    }
}

// Ambil semua kategori unik
$kategori_list = mysqli_query($conn1, "SELECT DISTINCT kategori FROM crawl ORDER BY kategori ASC");

// Ambil semua penerbit unik (domain)
$penerbit_list = mysqli_query($conn1, "SELECT DISTINCT SUBSTRING_INDEX(REPLACE(REPLACE(link,'https://',''),'http://',''), '/', 1) AS domain
                                       FROM crawl WHERE link IS NOT NULL AND link != '' ORDER BY domain ASC");

// Query berita
$query = "SELECT
            id, kategori, judul, link, ringkasan, tanggal, status
          FROM crawl
          WHERE 1=1";

if ($kategori_filter != '') {
    $query .= " AND kategori = '".mysqli_real_escape_string($conn1, $kategori_filter)."'";
}
if ($penerbit_filter != '') {
    $query .= " AND SUBSTRING_INDEX(REPLACE(REPLACE(link,'https://',''),'http://',''), '/', 1) = '".mysqli_real_escape_string($conn1, $penerbit_filter)."'";
}

// Urutkan berdasarkan tanggal terbaru
$query .= " ORDER BY tanggal DESC, id DESC";

$result = mysqli_query($conn1, $query);

// Fungsi ambil domain penerbit
function getPenerbit($url) {
    $host = parse_url($url, PHP_URL_HOST);
    $host = preg_replace('/^www\./', '', $host);
    return $host ? $host : 'Tidak diketahui';
}

// Fungsi pilih tanggal
function getTanggal($row) {
    return !empty($row['tanggal']) ? date('Y-m-d H:i', strtotime($row['tanggal'])) : '-';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Scrapping Berita</title>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

  <style>
    /* === GENERAL & LAYOUT === */

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
    /* === CONTENT AREA === */
    .content-area { padding: 25px; }

    /* === CARD === */
    .card {
        background: rgba(255,255,255,0.85);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        padding: 25px;
        backdrop-filter: blur(8px);
    }

    /* === TOOLBAR === */
    .actions-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 25px;
    }
    .filter-form { display: flex; align-items: center; gap: 10px; }
    .filter-form label { font-weight: 600; color: #444; }
    .filter-form select {
        padding: 8px 12px;
        border-radius: 6px;
        border: 1px solid #ccc;
        background: #fff;
    }

    /* === BUTTONS === */
    .btn {
        padding: 9px 18px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        color: white;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .btn:hover { opacity: 0.9; transform: translateY(-1px); }
    .btn-scrapping { background: linear-gradient(135deg, #198754, #28a745); }
    .btn-tampilkan { background: linear-gradient(135deg, #b22222, #dc3545); }
    .btn-mode { background: #6c757d; }
    .btn-delete { background: linear-gradient(135deg, #dc3545, #b22222); }
    .btn-source, .btn-edit, .btn-publish {
        background: #17a2b8;
        padding: 6px 12px;
    }
    .btn-publish-all { background: linear-gradient(135deg, #007bff, #0056b3); }
    .btn-unpublish-all { background: linear-gradient(135deg, #dc3545, #a71d2a); }

    /* === TABLE === */
    .table-actions { margin-bottom: 15px; }
    table.dataTable {
        border-collapse: collapse !important;
        width: 100% !important;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
    }
    th, td {
        padding: 14px 16px;
        text-align: left;
        border-bottom: 1px solid #dee2e6;
        vertical-align: middle;
    }
    uthead th {
        background-color: #b22222;
        color: #fff;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
    }
    tbody tr:hover { background-color: #f7f7f7; }
    td .ringkasan {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        max-width: 350px;
        font-size: 14px;
        color: #555;
    }

    /* === DATATABLES === */
    .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter { margin-bottom: 20px; }
    .dataTables_filter input, .dataTables_length select {
        padding: 6px 10px;
        border-radius: 6px;
        border: 1px solid #ccc;
    }

    /* === CHECKBOX & MODAL === */
    .col-checkbox { display: none; text-align: center; }
    .show-checkbox .col-checkbox { display: table-cell; }
    .modal {
        display:none;
        position:fixed;
        z-index:100;
        padding-top:120px;
        left:0; top:0;
        width:100%; height:100%;
        background-color: rgba(0,0,0,0.6);
    }
    .modal-content {
        background-color:#fff;
        margin:auto;
        padding:30px;
        border-radius:10px;
        width:400px;
        text-align:center;
        box-shadow:0 5px 15px rgba(0,0,0,0.3);
    }
    .modal-content h2 { color:#b22222; }
    .close { color:#aaa; float:right; font-size:24px; font-weight:bold; cursor:pointer; }

    /* === ACTION BUTTONS BAR === */
    .action-buttons {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: nowrap;
    }
  </style>
</head>

<body>

<div class="main">
  <div class="header">
    <h1>Scrapping Berita</h1>
    <span>Selamat datang, <?= htmlspecialchars($_SESSION['nama'] ?? $_SESSION['username'] ?? 'User', ENT_QUOTES, 'UTF-8') ?></span>
  </div>

  <div class="content-area">
    <div class="card">
      <div class="actions-toolbar">
          <div>
              <button id="btnScrappingNow" class="btn btn-scrapping">Scrapping Sekarang</button>
              <button id="btnPublishAll" class="btn btn-mode btn-publish-all">Publish Semua</button>
              <button id="btnUnpublishAll" class="btn btn-mode btn-unpublish-all">Unpublish Semua</button>
          </div>
          <form method="GET" class="filter-form">
              <label for="kategori">Kategori:</label>
              <select name="kategori" id="kategori">
                  <option value="">-- Semua Kategori --</option>
                  <?php mysqli_data_seek($kategori_list, 0); while ($kat = mysqli_fetch_assoc($kategori_list)) { ?>
                  <option value="<?= htmlspecialchars($kat['kategori'], ENT_QUOTES, 'UTF-8'); ?>" <?= ($kategori_filter == $kat['kategori']) ? 'selected' : '' ?>>
                      <?= ucfirst(htmlspecialchars($kat['kategori'], ENT_QUOTES, 'UTF-8')); ?>
                  </option>
                  <?php } ?>
              </select>
              <label for="penerbit">Penerbit:</label>
              <select name="penerbit" id="penerbit">
                  <option value="">-- Semua Penerbit --</option>
                  <?php mysqli_data_seek($penerbit_list, 0); while ($pen = mysqli_fetch_assoc($penerbit_list)) { ?>
                  <option value="<?= htmlspecialchars($pen['domain'], ENT_QUOTES, 'UTF-8'); ?>" <?= ($penerbit_filter == $pen['domain']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($pen['domain'], ENT_QUOTES, 'UTF-8'); ?>
                  </option>
                  <?php } ?>
              </select>
              <button type="submit" class="btn btn-tampilkan">Tampilkan</button>
          </form>
      </div>

      <form method="POST" id="hapusForm" onsubmit="return confirm('Yakin hapus berita terpilih?');">
        <div class="table-actions">
            <button type="button" id="toggleDelete" class="btn btn-mode">Hapus Beberapa</button>
            <button type="submit" name="hapus_selected" id="hapusSelected" class="btn btn-delete" style="display:none;">Hapus Terpilih</button>
        </div>

        <table id="beritaTable" class="display" style="width:100%">
          <thead>
            <tr>
              <th class="col-checkbox"><input type="checkbox" id="checkAll"></th>
              <th>No</th>
              <th>Judul</th>
              <th>Kategori</th>
              <th>Ringkasan</th>
              <th>Tanggal</th>
              <th>Penerbit</th>
              <th>Aksi</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)) {
                    $penerbit = htmlspecialchars(getPenerbit($row['link']), ENT_QUOTES, 'UTF-8');
                    $tanggal = getTanggal($row);
                    $title = htmlspecialchars($row['judul'], ENT_QUOTES, 'UTF-8');
                    $kategori_disp = htmlspecialchars($row['kategori'], ENT_QUOTES, 'UTF-8');
                    $ringkasan = htmlspecialchars($row['ringkasan'], ENT_QUOTES, 'UTF-8');

                    $safe_href = filter_var(trim($row['link']), FILTER_SANITIZE_URL);
                    if ($safe_href && strpos($safe_href, 'http') !== 0) $safe_href = 'http://' . $safe_href;

                    // Toggle Publish/Unpublish sesuai status di DB
                    $status = $row['status'] ?? 'aktif'; // default aktif
                    $btnText = ($status == 'aktif') ? 'Publish' : 'Unpublish';
                    $btnColor = ($status == 'aktif') ? '#007bff' : '#dc3545';

                    echo "<tr>";
                    echo "<td class='col-checkbox'><input type='checkbox' name='selected_id[]' value='".intval($row['id'])."'></td>";
                    echo "<td>".($no++)."</td>";
                    echo "<td>".$title."</td>";
                    echo "<td>".$kategori_disp."</td>";
                    echo "<td><span class='ringkasan' title='".$ringkasan."'>".$ringkasan."</span></td>";
                    echo "<td>".$tanggal."</td>";
                    echo "<td>".$penerbit."</td>";
                    echo "<td style='white-space: nowrap;'>";
                    if ($safe_href) echo "<a href='{$safe_href}' target='_blank' rel='noopener noreferrer' class='btn btn-source'>Link</a> ";
                    echo "<a href='edit_form.php?id=".intval($row['id'])."' class='btn btn-edit'>Edit</a>";
                    echo "</td>";
                    echo "<td style='text-align:center; white-space: nowrap;'>";
                    echo "<a href='#' class='btn btn-publish' style='background-color:$btnColor;' onclick='togglePublish({$row['id']}, this)'>$btnText</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<script>
                        window.onload = function() {
                          document.getElementById('noDataModal').style.display = 'block';
                        }
                      </script>";
            }
            ?>
          </tbody>
        </table>
      </form>
    </div>
  </div>

  <div id="noDataModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <h2>Oops 😢</h2>
      <p>Data berita tidak ditemukan untuk filter yang dipilih.</p>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    $('#beritaTable').DataTable({
        "order": [[5, "desc"]],
        "pageLength": 10,
        "lengthMenu": [10, 25, 50, 100],
        "language": {
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ data",
            "info": "Menampilkan _START_-_END_ dari _TOTAL_ data",
            "paginate": {"next": "›", "previous": "‹"}
        }
    });

    $('#toggleDelete').on('click', function() {
        $('#beritaTable').toggleClass('show-checkbox');
        $('#hapusSelected').toggle($('#beritaTable').hasClass('show-checkbox'));
        if (!$('#beritaTable').hasClass('show-checkbox')) {
            $('#checkAll, input[name="selected_id[]"]').prop('checked', false);
        }
    });

    $('#checkAll').on('click', function() {
        $('input[name="selected_id[]"]').prop('checked', $(this).prop('checked'));
    });

    $('#btnScrappingNow').on('click', async function() {
        if (!confirm('Mulai proses scrapping sekarang?')) return;
        const btn = $(this);
        btn.prop('disabled', true).text('Scrapping...');
        try {
            const response = await fetch('scrapping_trigger.php', { method: 'POST', headers: {'Accept': 'application/json'} });
            const data = await response.json();
            if (data.status === 'ok') {
                alert(`✅ ${data.msg}\n➕ Ditambahkan: ${data.inserted}\n📊 Total di DB: ${data.total}`);
                location.reload();
            } else {
                alert('Error: ' + (data.msg || 'Terjadi kesalahan.'));
            }
        } catch (err) { alert('Request error: ' + err.message); }
        finally { btn.prop('disabled', false).text('Scrapping Sekarang'); }
    });

    window.closeModal = function() { $('#noDataModal').hide(); }
    $(window).on('click', function(event) { if ($(event.target).is('#noDataModal')) closeModal(); });
});

    // ===== PUBLISH / UNPUBLISH SEMUA =====
    $('#btnPublishAll, #btnUnpublishAll').on('click', async function() {
        const isPublish = this.id === 'btnPublishAll';
        const actionText = isPublish ? 'Publish semua berita?' : 'Unpublish semua berita?';
        const statusValue = isPublish ? 'aktif' : 'unaktif';
        if (!confirm(`Yakin ingin ${actionText}`)) return;

        const btn = $(this);
        btn.prop('disabled', true).text('Memproses...');
        try {
            const response = await fetch('toggle_all.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `status=${statusValue}`
            });
            const data = await response.json();
            if (data.success) {
                alert(`✅ ${data.message}`);
                location.reload();
            } else {
                alert('⚠️ Gagal: ' + (data.message || 'Terjadi kesalahan.'));
            }
        } catch (err) {
            alert('⚠️ Request error: ' + err.message);
        } finally {
            btn.prop('disabled', false).text(isPublish ? 'Publish Semua' : 'Unpublish Semua');
        }
    });


// ===== Fungsi Toggle Publish/Unpublish =====
function togglePublish(id, btn) {
    btn.disabled = true;
    const originalText = btn.textContent;
    btn.textContent = 'Memproses...';
    btn.style.opacity = '0.6';

    $.ajax({
        url: 'toggle_status.php',
        type: 'POST',
        data: { id: id },
        dataType: 'json', // ini penting agar langsung parse JSON
        success: function(res) {
            if (res.status === 'aktif') {
                btn.textContent = 'Publish';
                btn.style.backgroundColor = '#007bff';
            } else if (res.status === 'unaktif') {
                btn.textContent = 'Unpublish';
                btn.style.backgroundColor = '#dc3545';
            } else {
                alert('⚠️ Gagal mengubah status.');
                btn.textContent = originalText;
            }
        },
        error: function(xhr, status, error) {
            alert('⚠️ Gagal mengubah status: ' + error);
            btn.textContent = originalText;
        },
        complete: function() {
            btn.disabled = false;
            btn.style.opacity = '1';
        }
    });
}

</script>

</body>
</html>

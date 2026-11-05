<?php #ffffff
require_once "config.php";

// ✅ Query berita aktif
$sql = "SELECT
            id,
            judul AS title,
            kategori AS category,
            ringkasan AS content,
            tanggal AS date,
            link
        FROM crawl
        WHERE status = 'aktif'
        and tanggal >= CURDATE() - INTERVAL 7 DAY 
        ORDER BY tanggal DESC
        ";
$result = $conn->query($sql);

// Simpan hasil berita ke array
$dataBerita = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dataBerita[] = $row;
    }
}

// ✅ Query grafik jumlah per kategori (hanya yang aktif)
$sqlKategori = "SELECT kategori, COUNT(*) AS jumlah
                FROM crawl
                WHERE status = 'aktif'
                and YEAR(tanggal) = YEAR(CURDATE()) 
                GROUP BY kategori";
$resultKategori = $conn->query($sqlKategori);

$dataKategori = [];
if ($resultKategori && $resultKategori->num_rows > 0) {
    while ($row = $resultKategori->fetch_assoc()) {
        $dataKategori[] = $row;
    }
}

// ✅ Query grafik jumlah berita per bulan (hanya yang aktif)
$sqlBulanan = "SELECT DATE_FORMAT(tanggal, '%Y-%m') AS bulan, COUNT(*) AS jumlah
               FROM crawl
               WHERE status = 'aktif'
               GROUP BY DATE_FORMAT(tanggal, '%Y-%m')
               ORDER BY bulan ASC";
$resultBulanan = $conn->query($sqlBulanan);

$dataBulanan = [];
if ($resultBulanan && $resultBulanan->num_rows > 0) {
    while ($row = $resultBulanan->fetch_assoc()) {
        $dataBulanan[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-w  idth, initial-scale=1.0">
    <title>Cyber Media</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="js/app.js" defer></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@600;700&family=Source+Sans+Pro:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* Semua CSS kamu tetap seperti semula */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            background-color: #0a192f;
            color: #ccd6f6;
        }
        #bgVideo { position: fixed; top: 50%; left: 50%; width: 100%; height: 100%; object-fit: cover; z-index: -1; opacity: 0.2; filter: blur(2px); transform: translate(-50%, -50%) scale(1.2); transition: transform 0.6s ease; }
        header { position: relative; display: flex; align-items: center; gap: 15px; padding: 10px 25px; background: rgba(10, 25, 47, 0.85); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255, 255, 255, 0.1);  position: sticky; top: 0; z-index: 10; }
        header img { width: 50px; height: auto; }
        header h1 { font-family: 'Montserrat', sans-serif; font-size: 24px; font-weight: 700; letter-spacing: 2px; color: #ffffff; }
        <div class="judul-kanan">
            Sistem Crawling Informasi Siber<br>
            <span>Pussiber Inteltekhan Kemhan</span>
       </div.
        header span { color: #ffffffff; }
        .judul-kanan {
            position: absolute;
            top: 10px;
            right: 25px;
            text-align: right;
            font-size: 22px;
         /* Efek tulisan RGB berganti warna dari kiri ke kanan */
.rgb-text {
  background: linear-gradient(90deg, red, orange, yellow, green, cyan, blue, violet);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-size: 400% 100%;
  animation: rgbShift 8s linear infinite;
}

@keyframes rgbShift {
  0% { background-position: 0% 0%; }
  100% { background-position: 100% 0%; }
}
   line-height: 1.3;
            font-family:  'Montserrat' , sans-serif !important;
            font-weight: 700! important;
            letter-spacing: 2px;
            color: #ffffff;
	    line-height: 1.3;

        }

        .judul-kanan span {
            display : block;
            font-size: 22px;
            color: #ffffff;
            font-weight: 500;
        }



        .container { flex: 1; display: grid; grid-template-columns: 2fr 1fr; gap: 25px; padding: 25px; width: 100%; max-width: 1400px; margin: 0 auto; }
        .card { background: rgba(10, 25, 47, 0.75); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1); display: flex; flex-direction: column; width: 100%; }
        .slideshow { display: flex; align-items: center; justify-content: center; }
        .slide-content { width: 100%; display: flex; flex-direction:  column; padding-bottom: 35px; }
        .slide-text-wrapper { flex-grow: 1; }
        .slide-content h2 { font-family: 'Montserrat', sans-serif; font-size: 42px; font-weight: 700; color: #ffffff; margin-bottom: 15px; text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7); }
        .slide-content .kategori { font-size: 20px; font-weight: 600; color: #ffffffff; margin-bottom: 10px; text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.5); }
        .slide-content .isi-berita { font-size: 24px; color: #ffffffff; line-height: 1.7; margin-bottom: 15px; text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.5); text-align: justify; }
        .slide-content .tanggal { font-size: 18px; color: #ffffffff; text-align: left; margin-top: 15px; }
        .link-sumber { margin-top: 20px; }
        .link-sumber a { display: inline-block; padding: 10px 18px; border: 1px solid #ff0000ff; border-radius: 4px; color: #ff0000ff; text-decoration: none; font-size: 14px; font-weight: 500; transition: background-color 0.3s ease, color 0.3s ease; word-break: normal; }
        .link-sumber a:hover { background-color: rgba(100, 255, 218, 0.1); }
        .chart-container { display: flex; flex-direction: column; gap: -50px: margin-top: -40px; }
        .chart-container h3 { margin-bottom: 20px; font-size: 18px; font-family: 'Montserrat', sans-serif; color: #ffffff; text-align: center; }
        .chart-table { width: 100%; border-collapse: collapse; font-size: 14px; font-family: 'Inter', sans-serif; margin-top: 10px; }
        .chart-table th, .chart-table td { padding: 12px 10px; text-align: left; border-bottom: 1px solid rgba(248, 246, 246, 0.1); }
        .chart-table th { font-weight: 600; color: #64ffda; }
        .chart-table tbody tr:last-child td { border-bottom: none; }
        @media (max-width: 900px) { .container { grid-template-columns: 1fr; } header { padding: 10px 15px; } .slide-content { min-height: auto; } .slide-content h2 { font-size: 28px; } .slide-content p, .slide-content .isi-berita, .slide-content .tanggal, .slide-content .kategori { font-size: 15px; } }
/* 🌈 Efek tulisan RGB berganti warna dari kiri ke kanan */
.rgb-text {
  background: linear-gradient(90deg, red, orange, yellow, green, cyan, blue, violet);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-size: 400% 100%;
  animation: rgbShift 8s linear infinite;
}

@keyframes rgbShift {
  0% { background-position: 0% 0%; }
  100% { background-position: 100% 0%; }
}
/* Background logo Kemhan */
#bgLogo {
    position: fixed;
    top: 50%;
    left: 50%;
    width: 60vh;
    height: 60vh;
    background-image: url('logo-kemhan.png');
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    opacity: 0.06;
    transform: translate(-50%, -50%) scale(1.1);
    z-index: -1;
    pointer-events: none;
    transition: opacity 1.2s ease, transform 1.2s ease;
}

/* Ketika aktif */
#bgLogo.show {
    opacity: 0.12;
    transform: translate(-50%, -50%) scale(1);
}

</style>
</head>

<body>
 <div id="bgLogo"></div>
<video autoplay muted loop id="bgVideo">
    <source src="background.mp4" type="video/mp4">
</video>

<header>
    <img src="logo-kemhan.png" alt="logo kemhan">
    <h1>Cyber <span>Media</span></h1>
</div>
<div class="judul-kanan">
     <p class="instansi rgb-text">
        Sistem Crawling Informasi Siber<br>
       <span class="rgb-text">Pussiber Inteltekhan Kemhan</span>
</p>

   </div>
</header>

<div class="container">
    <div class="berita">
        <div id="slideshow" class="slideshow">
            <p>Sedang memuat berita...</p>
        </div>
        <!-- 🔹 CARD BARU: GRAFIK TOTAL BERITA HARIAN -->
        <div class="card" style="margin-top:30px;">
            <h3 style="text-align:center; color:#64ffda;">Grafik Total Berita Harian</h3>
            <canvas id="chartBeritaHarian" height="200"></canvas>
        </div>
    </div>

    <div class="chart-container">
        <div class="card" style="margin-top:5px;">
            <h3>Grafik Berita per Kategori Tahun <?php echo date('Y'); ?></h3>
            <canvas id="chartKategori"></canvas>
        </div>

        <div class="card-container">
	<div class="card" style="margin-top:10px;">
            <h3>Tabel Berita per Kategori Tahun <?php echo date('Y'); ?></h3>
            <table class="chart-table">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
		<?php
    		$totalKategori = 0;
    		if (!empty($dataKategori)):
 foreach ($dataKategori as $row):
        $totalKategori += $row['jumlah'];
?>                            <tr>
                                <td><?= htmlspecialchars($row['kategori']) ?></td>
                                <td><?= htmlspecialchars($row['jumlah']) ?></td>
                            </tr>
                        <?php endforeach; ?>
			<!-- 🔹 Baris Total -->
        		<tr style="font-weight:bold; color:#64ffda;">
           		 <td>Total</td>
            		<td><?= $totalKategori ?></td>
        		</tr>
                    <?php else: ?>
                        <tr><td colspan="2">Belum ada data kategori</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <p style="margin-top:10px; margin-left:335px; font-size:10px; color:gray" >FIK-2023-UPNVJ <br>Magang 2025</p>
    </div>
</div>

<script>
window.dataKategori = <?= json_encode($dataKategori) ?>;
window.dataBulanan  = <?= json_encode($dataBulanan) ?>;
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const slideshowContainer = document.getElementById("slideshow");
    const dataBerita = <?= json_encode($dataBerita) ?>;

    if (!dataBerita || dataBerita.length === 0) {
        slideshowContainer.innerHTML = "<p>Tidak ada berita aktif di database.</p>";
        return;
function renderSlide(index) {

    // 🔹 Animasi LOGO di belakang
    const bgLogo = document.getElementById("bgLogo");
    bgLogo.classList.remove("show");
    setTimeout(() => {
        bgLogo.classList.add("show");    }, 150);

    const berita = dataBerita[index];
    const link = berita.link || "#";

    let deskripsi = berita.content || "";
    if (deskripsi.length > 400) {
        deskripsi = deskripsi.substring(0, 400) + "...";
    }

    deskripsi = deskripsi
        .replace(/baca\s*(selengkapnya|lebih lanjut|lebih lengkap|disini)?[:\s]*/gi, '')
        .replace(/<a[^>]*>.*?<\/a>/gi, '')
        .trim();

    // 🔹 ISI SLIDE + ANIMASI
    slideshowContainer.innerHTML = `
        <div class="slide-content" id="slideContent">
            <h2 class="slide-title">${escapeHtml(berita.title)}</h2>
            <p class="slide-date">${escapeHtml(berita.date)}</p>
            <p class="slide-desc">${escapeHtml(deskripsi)}</p>
            <a class="slide-link" href="${link}" target="_blank">Baca selengkapnya</a>
        </div>
    `;

    // 🔹 Trigger Animasi Slide
    setTimeout(() => {
        document.getElementById("slideContent").classList.add("show");
    }, 50);
}
    }

    let currentIndex = 0;

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        const p = document.createElement("p");
        p.textContent = str;
        return p.innerHTML;
    }

    function renderSlide(index) {


    document.getElementById("bgLogo").classList.remove("show");
    setTimeout(() => {
        document.getElementById("bgLogo").classList.add("show");
    }, 150);
        const berita = dataBerita[index];
        const link = berita.link || "#";

        let deskripsi = berita.content || "";
        if (deskripsi.length > 400) {
            deskripsi = deskripsi.substring(0, 400) + "...";
        }

        deskripsi = deskripsi
            .replace(/baca\s*(selengkapnya|lebih lanjut|lebih lengkap|disini)?[:\s]*/gi, '')
            .replace(/<a[^>]*>.*?<\/a>/gi, '')
            .trim();

        slideshowContainer.innerHTML = `
            <div class="slide-content">
                <div class="slide-text-wrapper">
            </div>`;
    }

    renderSlide(currentIndex);
    setInterval(() => {
        currentIndex = (currentIndex + 1) % dataBerita.length;
        renderSlide(currentIndex);
    }, 40000);

    // 🔹 Chart Berita Harian
    const ctx = document.getElementById("chartBeritaHarian");
    if (ctx) {
        // Kelompokkan berita berdasarkan tanggal
        const dailyCounts = {};
        dataBerita.forEach(b => {
            const tgl = b.date.split(" ")[0];
            dailyCounts[tgl] = (dailyCounts[tgl] || 0) + 1;
        });

        const labels = Object.keys(dailyCounts).sort();
        const values = labels.map(l => dailyCounts[l]);

        new Chart(ctx, {
            type: "line",
            data: {
                labels: labels,
                datasets: [{
                    label: "Total Berita",
                    data: values,
                    borderColor: "#64ffda",
                    backgroundColor: "rgba(100,255,218,0.2)",
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: "#ccd6f6" },
                        grid: { color: "rgba(204,214,246,0.1)" }
                    },
                    x: {
                        ticks: { color: "#ccd6f6" },
                        grid: { color: "rgba(204,214,246,0.05)" }
                    }
                },
                plugins: {
                    legend: { labels: { color: "#ccd6f6" } }
                }
            }
        });
    }
});




</script>
<script> setTimeout(function() { location.reload(); }, 3600000); </script>


</body>
</html>

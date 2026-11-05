<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'sidebar.php';
include 'db.php';   // koneksi db_admin
include 'db1.php';  // koneksi data_crawl

// Hitung jumlah Admin (dari db_admin.users)
$q_admin = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users");
$admin = mysqli_fetch_assoc($q_admin)['total'];

// Hitung jumlah berita crawl (dari data_crawl.berita)
$q_crawl = mysqli_query($conn1, "SELECT COUNT(*) AS total FROM crawl");
$crawl = mysqli_fetch_assoc($q_crawl)['total'];

// Hitung jumlah berita publish (dari status aktif di scrapping_berita)
$q_publish = mysqli_query($conn1, "SELECT COUNT(*) AS total FROM crawl WHERE status='aktif'");
$publish = mysqli_fetch_assoc($q_publish)['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard</title>
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

    /* CARDS */
    .cards {
      display: flex;
      gap: 20px;
      margin: 25px;
    }

    .card {
      flex: 1;
      background: #ffffff;
      color: #000000;
      padding: 25px;
      border-radius: 15px;
      text-align: center;
      border: 1px solid rgba(255, 255, 255, 0.5);
      backdrop-filter: blur(18px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.25);
      position: relative;
      overflow: hidden;
      cursor: pointer;
      transition: all 0.3s ease-in-out;
    }

    .card::before {
      content: "";
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle at top left, rgba(255,255,255,0.35), transparent 60%);
      transform: rotate(25deg);
      opacity: 0.6;
    }

    .card::after {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 8px; /* ketebalan garis kiri */
  height: 100%;
  background: linear-gradient(135deg, #800000 15%, #0d1b2a 75%, #1b263b 100%);
}
	
    .card:hover {
      transform: translateY(-8px) scale(1.05);
      box-shadow: 0 10px 30px rgba(0,0,0,0.3);
      border-color: #fff;
    }

    .card h2 {
      font-size: 34px;
      margin: 0;
      text-shadow: 0 2px 8px rgba(0,0,0,0.3);
      color: #0000000;
    }

    .card p {
      margin: 10px 0;
      font-weight: 600;
      font-size: 16px;
      letter-spacing: 0.5px;
      color: #0000000;
    }

    .card i {
      position: absolute;
      right: 25px;
      bottom: 20px;
      opacity: 0.8;
      font-size: 2.2em;
      text-shadow: 0 3px 8px rgba(0,0,0,0.4);
      background: linear-gradient(135deg, #800000 15%, #0d1b2a 75%, #1b263b 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    }

    /* TABLE CONTAINERS */
    .tables {
      margin: 25px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 25px;
    }

    .table-box {
      background: rgba(255, 255, 255, 0.25);
      padding: 20px;
      border-radius: 15px;
      text-align: center;
      font-weight: bold;
      box-shadow: 0 8px 20px rgba(0,0,0,0.25);
      border: 1px solid rgba(255, 255, 255, 0.6);
      backdrop-filter: blur(20px);
      height: 380px;
      position: relative;
      color: #333;
    }

    .table-box h3 {
      color: #0000000;
      text-shadow: 0 2px 6px rgba(0,0,0,0.2);
      margin-bottom: 10px;
    }

    .table-box canvas {
      width: 100% !important;
      height: 260px !important;
      border-radius: 10px;
    }

    .filter {
      position: absolute;
      top: 10px;
      right: 20px;
      background: rgba(255, 255, 255, 0.3);
      padding: 5px 8px;
      border-radius: 8px;
      border: 1px solid rgba(255, 255, 255, 0.6);
      backdrop-filter: blur(10px);
    }

    select {
      border: none;
      background: transparent;
      font-weight: 500;
      color: #333;
      outline: none;
      font-size: 14px;
    }

    /* Animasi lembut saat load */
    .card, .table-box {
      animation: fadeInUp 0.6s ease both;
    }

    @keyframes fadeInUp {
      from { transform: translateY(30px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
  </style>
</head>
<body>

<div class="main">
  <div class="header">
    <h1>Dashboard</h1>
    <span>Selamat datang, <?= $_SESSION['nama'] ?? $_SESSION['username'] ?> </span>
  </div>

  <div class="cards">
    <div class="card">
      <h2><?= $admin; ?></h2>
      <p>Admins</p>
      <i class="fa-solid fa-user"></i>
    </div>
    <div class="card">
      <h2><?= $crawl; ?></h2>
      <p>Scraping Berita</p>
      <i class="fa-solid fa-file-lines"></i>
    </div>
    <div class="card">
      <h2><?= $publish; ?></h2>
      <p>Publish Berita</p>
      <i class="fa-solid fa-layer-group"></i>
    </div>
  </div>

  <div class="tables">
    <div class="table-box">
      <h3>Jumlah Berita per Kategori</h3>
      <canvas id="barKategori"></canvas>
    </div>

    <div class="table-box">
      <h3>Jumlah Berita</h3>
      <div class="filter">
        <label for="filterBulan">Bulan:</label>
        <select id="filterBulan">
          <option value="">Semua</option>
        </select>
      </div>
      <canvas id="lineTanggal"></canvas>
    </div>
  </div>
</div>

<!-- CDN Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
Chart.defaults.font.family = 'Poppins';
Chart.defaults.font.size = 13;
Chart.defaults.color = '#333';

async function loadKategoriChart() {
  try {
    const res = await fetch('data_berita_per_kategori.php', { cache: 'no-store' });
    const data = await res.json();

    if (window.barKategori instanceof Chart) window.barKategori.destroy();
    const ctx = document.getElementById('barKategori').getContext('2d');

    window.barKategori = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: data.kategori,
        datasets: [{
          label: 'Jumlah Berita',
          data: data.jumlah,
          backgroundColor: ['#b23b3b', '#8b1d1d', '#d35454', '#a84c3c', '#734141', '#c15e4a'],
          borderRadius: 6,
          borderWidth: 1,
          borderColor: '#fff'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: { 
              backgroundColor: '#fff',
              titleColor: '#b22222',
              bodyColor: '#333',
              borderWidth: 1, 
              borderColor: '#b22222'
           }
         },
         interaction: {
              mode: 'nearest',
              intersect: true
        },
        scales: {
          y: { beginAtZero: true, ticks: { precision: 0 } }
        }
      }
    });
  } catch (err) {
    console.error("Gagal load chart kategori:", err);
  }
}

async function loadTanggalChart(bulan = "") {
  try {
    const res = await fetch('data_berita_per_bulan.php?bulan=' + bulan, { cache: 'no-store' });
    const data = await res.json();

    if (window.lineTanggal instanceof Chart) window.lineTanggal.destroy();
    const ctx = document.getElementById('lineTanggal').getContext('2d');

    const gradient = ctx.createLinearGradient(0, 0, 0, 250);
    gradient.addColorStop(0, 'rgba(178,34,34,0.8)');
    gradient.addColorStop(1, 'rgba(255,255,255,0.1)');

    window.lineTanggal = new Chart(ctx, {
      type: 'line',
      data: {
        labels: data.labels,
        datasets: [{
          label: bulan ? 'Jumlah Berita per Hari' : 'Jumlah Berita per Bulan',
          data: data.jumlah,
          borderColor: '#b22222',
          backgroundColor: gradient,
          tension: 0.35,
          fill: true,
          borderWidth: 2,
          pointBackgroundColor: '#fff',
          pointBorderColor: '#b22222',
          pointHoverRadius: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: { backgroundColor: '#fff', titleColor: '#b22222', bodyColor: '#333', borderWidth: 1, borderColor: '#b22222' }
        },
        scales: {
          y: { beginAtZero: true, ticks: { precision: 0 } }
        }
      }
    });
  } catch (err) {
    console.error("Gagal load chart tanggal:", err);
  }
}

async function loadDropdownBulan() {
  try {
    const res = await fetch('get_bulan.php');
    const data = await res.json();
    const select = document.getElementById('filterBulan');
    data.forEach(b => {
      const opt = document.createElement('option');
      opt.value = b;
      opt.textContent = b;
      select.appendChild(opt);
    });
  } catch (err) {
    console.error("Gagal load dropdown bulan:", err);
  }
}

window.addEventListener('DOMContentLoaded', () => {
  loadKategoriChart();
  loadDropdownBulan();
  loadTanggalChart();

  document.getElementById('filterBulan').addEventListener('change', function() {
    loadTanggalChart(this.value);
  });
});
</script>
</body>
</html>

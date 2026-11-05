<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Grafik Berita per Hari</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <h2>Jumlah Berita per Hari (Bulan Berjalan)</h2>
  <canvas id="myChart" width="600" height="300"></canvas>

  <script>
  fetch("data_berita_per_hari.php")
    .then(response => response.json())
    .then(json => {
      const ctx = document.getElementById('myChart').getContext('2d');
      new Chart(ctx, {
        type: 'line', // bisa 'bar' juga kalau mau batang
        data: {
          labels: json.tanggal,
          datasets: [{
            label: 'Jumlah Berita per Hari',
            data: json.jumlah,
            borderColor: 'rgba(75,192,192,1)',
            backgroundColor: 'rgba(75,192,192,0.2)',
            borderWidth: 2,
            fill: true,
            tension: 0.3
          }]
        },
        options: {
          responsive: true,
          plugins: { legend: { position: 'top' } },
          scales: { y: { beginAtZero: true } }
        }
      });
    });
  </script>
</body>
</html>


// File: js/app.js
document.addEventListener("DOMContentLoaded", () => {
  console.log("App.js loaded and ready!");

  // ----- Chart.js default fonts -----
  if (window.Chart) {
    Chart.defaults.font.family = "Inter, system-ui, -apple-system, 'Segoe UI', Roboto, Arial, sans-serif";
    Chart.defaults.font.size = 12;
  }

  // ----- Render Chart -----
  if (window.dataKategori && window.dataKategori.length > 0) {
    const labels = window.dataKategori.map(r => r.kategori);
    const values = window.dataKategori.map(r => parseInt(r.jumlah, 10));

    // ----- Ubah warna bar manual di sini -----
    const backgroundColor = [
      '#C62828',
      '#FF1744',
      '#0A3D91',
      '#003366'
    ];
    const ctx = document.getElementById('chartKategori').getContext('2d');

    new Chart(ctx, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: 'Jumlah Berita',
          data: values,
          backgroundColor: backgroundColor.slice(0, values.length),
          borderRadius: 6
        }]
      },
      options: {
        responsive: true,
        indexAxis: 'y', // <-- Tambahan satu baris ini agar grafik horizontal
        plugins: {
          legend: {
            display: false,
          },
          title: {
            display: false,
          },
          tooltip: {
            titleFont: { family: "Montserrat, system-ui, sans-serif", size: 13 },
            bodyFont: { family: "Inter, system-ui, sans-serif", size: 12 }
          }
        },
        scales: {
          x: {
            ticks: {
              font: { family: "Inter, system-ui, sans-serif", size: 12 },
              color: '#FFFFFF' // Mengubah warna tulisan kategori menjadi putih
            },
            grid: {
              color: 'rgba(255, 255, 255, 0.1)' // Warna grid agar tampak di tema gelap
            }
          },
     y: {
  ticks: {
    color: '#FFFFFF',
    font: {
      family: 'Inter, system-ui, sans-serif',
      size: 13,
      weight: '500'
    },
    align: 'start', // biar sejajar kiri dulu
    crossAlign: 'center',
    padding: 20,
    callback: function(value, index) {
      const labels = window.dataKategori.map(r => r.kategori);
      const label = labels[index] || value;
        
      // Opsional: tetap kasih efek “rata tengah” visual antar label
      const maxLength = Math.max(...labels.map(l => l.length));
      const totalPad = maxLength - label.length;
      const leftPad = Math.floor(totalPad / 2);
      const rightPad = totalPad - leftPad;

      return ' '.repeat(leftPad) + label + ' '.repeat(rightPad);
    }
  },
  grid: {
    display: false
  }
}


        }
      }
    });
  }

  // ----- Fetch berita untuk slideshow -----
  const slideshowContainer = document.getElementById("slideshow");

  fetch("news.php")
    .then(response => response.json())
    .then(data => {
      if (!data || data.length === 0) {
        slideshowContainer.innerHTML = "<p>Tidak ada berita tersedia.</p>";
        return;
      }

      let currentIndex = 0;

      function renderSlide(index) {
        const berita = data[index];
        slideshowContainer.innerHTML = `
          <div class="slide-content">
            <h2>${escapeHtml(berita.title)}</h2>
            <p style="color:#caac1d" class="kategori">Kategori: ${escapeHtml(berita.category || "-")}</p> 
            <br>
            <p class="isi-berita">${escapeHtml(
              berita.content
                .replace(/baca\s*(selengkapnya|lebih lanjut|lebih lengkap|disini)?[:\s]*/gi, '')
                .replace(/baca.*$/gi, '')
                .replace(/<a[^>]*>.*?<\/a>/gi, '')
                .trim()
            )}</p>
            <p class="tanggal">${escapeHtml(berita.date)}</p>
            <a style="color:#caac1d" href="${berita.link}" onclick="
  const w = 1000;
  const h = 800;
  const left = (screen.width - w) / 2;
  const top = (screen.height - h) / 2;
  window.open(this.href, 'newwindow', 'width=' + w + ',height=' + h + ',top=' + top + ',left=' + left + ',resizable=yes,scrollbars=yes');
  return false;
">${berita.link}</a>
          </div>
        `;
      }

      renderSlide(currentIndex);

      setInterval(() => {
        currentIndex = (currentIndex + 1) % data.length;
        renderSlide(currentIndex);
      }, 10000);
    })
    .catch(error => {
      console.error(error);
      slideshowContainer.innerHTML = "<p class='text-red-500'>Gagal memuat berita.</p>";
    });

  // ----- Tombol refresh berita (opsional) -----
  const refreshBtn = document.getElementById("refreshNews");
  if (refreshBtn) {
    refreshBtn.addEventListener("click", () => location.reload());
  }

  // ----- Helper -----
  function escapeHtml(str) {
    if (!str && str !== 0) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }
});

# Cyber Media & BankData

Aplikasi pengelolaan berita teknologi dan keamanan siber. Proyek ini berisi panel admin untuk mengumpulkan berita dari RSS, meninjau dan mengelola status publikasi, serta situs publik untuk menampilkan berita aktif dan ringkasan statistik.

## Fitur

- Mengambil berita dari beberapa RSS teknologi dan keamanan siber.
- Mengelompokkan berita ke kategori tematik seperti malware, ransomware, dan kebocoran data.
- Panel admin untuk meninjau, mengedit, menghapus, dan mengatur status berita.
- Dashboard dengan ringkasan dan grafik berita.
- Situs Cyber Media untuk menampilkan berita yang berstatus aktif.

## Teknologi

- PHP dengan MySQLi
- MySQL
- Python dengan `requests`, `beautifulsoup4`, dan `PyMySQL`
- JavaScript untuk grafik dan interaksi antarmuka

## Struktur proyek

```text
bankdata/    Panel admin dan proses pengambilan berita
cybermedia/  Situs publik untuk berita aktif
```

## Menjalankan secara lokal

1. Siapkan PHP dengan ekstensi MySQLi, MySQL, dan Python 3.
2. Buat database dan tabel yang digunakan aplikasi (`db_admin` dan `data_crawl`). Skema database belum disertakan dalam repositori ini.
3. Atur variabel lingkungan sesuai `.env.example` pada lingkungan PHP dan shell yang menjalankan Python. File `.env.example` adalah dokumentasi nama variabel; PHP tidak memuat file `.env` secara otomatis.
4. Instal dependensi Python:

   ```bash
   python -m pip install requests beautifulsoup4 PyMySQL
   ```

5. Jalankan aplikasi melalui server PHP lokal dengan `bankdata/` dan `cybermedia/` sebagai direktori aplikasi masing-masing. Pastikan konfigurasi server PHP dan Python memiliki variabel database yang sama.
6. Jalankan pengambil berita dari direktori `bankdata/`:

   ```bash
   python scraping.py
   ```

## Catatan

- Kredensial database tidak disimpan di repository. Jangan commit file `.env` atau memasukkan password ke source code.
- Log scraper dan file sementara diabaikan oleh Git.
- Proyek ini dibuat sebagai demonstrasi portofolio. Tinjau keamanan autentikasi, validasi input, dan perlindungan endpoint sebelum menjalankannya di server publik.
- Pastikan kamu memiliki hak untuk mempublikasikan logo, video latar, dan aset lain yang disertakan.

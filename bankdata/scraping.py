import requests
from bs4 import BeautifulSoup
from datetime import datetime, timedelta
import pymysql
import csv
import time
import warnings
import os

# --- Hilangkan warning SSL ---
warnings.filterwarnings("ignore", message="Unverified HTTPS request")

# --- Konfigurasi Database ---
DB_NAME = "data_crawl"
TABLE_NAME = "crawl"

# --- RSS sumber berita ---
rss_sources = {
    "CNN Teknologi": "https://www.cnnindonesia.com/teknologi/rss",
    "Detik Inet": "https://inet.detik.com/rss",
    "CNBC Indonesia": "https://www.cnbcindonesia.com/rss",
    "Kompas Tekno": "https://indeks.kompas.com/rss/tekno",
    "Liputan6 Tekno": "https://feed.liputan6.com/tekno"
}

# --- Kategori tematik ---
categories = {
    "Serangan Siber": ["serangan siber", "cyber attack"],
    "Malware": ["malware", "virus komputer", "trojan"],
    "Hacker": ["hacker", "peretas", "deface"],
    "Ransomware": ["ransomware", "tebusan data"],
    "Kebocoran Data": ["data bocor", "kebocoran data", "data leak"],
    "Keamanan Internet": ["keamanan internet", "internet security", "enkripsi", "phishing"]
}

# --- Batas 30 hari terakhir ---
date_limit = datetime.now() - timedelta(days=30)
results = []

# --- Fungsi simpan ke DB dengan pymysql ---
def save_to_db_pymysql(rows):
    if not rows:
        print("[DB] tidak ada rows")
        return

    try:
        conn = pymysql.connect(
            host=os.getenv("DB_HOST", "localhost"),
            user=os.getenv("DB_USER", "root"),
            password=os.getenv("DB_PASS", ""),
            database=os.getenv("DB_CRAWL_NAME", "data_crawl"),
            charset='utf8mb4'
        )
        cur = conn.cursor()

        # Hapus data lama dari sumber HTML yang sudah tidak dipakai
        cur.execute(f"DELETE FROM {TABLE_NAME} WHERE sumber IN ('ID-SIRTII/CC','CSIRT KEMHAN')")

        sql = f"""INSERT INTO {TABLE_NAME}
                 (kategori, judul, ringkasan, tanggal, link, penerbit, sumber)
                 VALUES (%s,%s,%s,%s,%s,%s,%s)
                 ON DUPLICATE KEY UPDATE
                    judul=VALUES(judul),
                    kategori=VALUES(kategori),
                    ringkasan=VALUES(ringkasan),
                    tanggal=VALUES(tanggal),
                    link=VALUES(link),
                    penerbit=VALUES(penerbit),
                    sumber=VALUES(sumber),
                    created_at=CURRENT_TIMESTAMP"""
        data = []
        for row in rows:
            data.append((
                row["kategori"],
                row["judul"],
                row["ringkasan"],
                datetime.strptime(row["tanggal"], "%Y-%m-%d %H:%M"),
                row["link"],
                row["penerbit"],
                row["sumber"]
            ))
        cur.executemany(sql, data)
        conn.commit()
        print(f"[DB] Berhasil simpan {len(data)} rows")
    except Exception as e:
        print("[DB] ERROR:", e)
    finally:
        cur.close()
        conn.close()

# --- Scraping RSS ---
for source, url in rss_sources.items():
    print(f"Mengambil dari: {source}")
    try:
        response = requests.get(url, headers={"User-Agent": "Mozilla/5.0"}, timeout=15)
    except Exception as e:
        print(f"[WARN] Gagal ambil RSS {source}: {e}")
        continue

    soup = BeautifulSoup(response.content, "xml")
    items = soup.find_all("item")
    for item in items:
        title = item.title.get_text(strip=True)
        link = item.link.get_text(strip=True)

        # hapus <img> dalam description
        description_html = item.description.get_text(strip=True) if item.description else "-"
        soup_desc = BeautifulSoup(description_html, "html.parser")
        for img in soup_desc.find_all("img"):
            img.decompose()
        description = soup_desc.get_text(strip=True)

        try:
            pub_date = datetime.strptime(item.pubDate.get_text(strip=True), "%a, %d %b %Y %H:%M:%S %z")
            pub_date_naive = pub_date.replace(tzinfo=None)

            # --- KHUSUS CNBC: ambil tanggal publish asli di halaman berita ---
            if "cnbcindonesia.com" in link:
                try:
                    art_resp = requests.get(link, headers={"User-Agent": "Mozilla/5.0"}, timeout=15, verify=False)
                    art_soup = BeautifulSoup(art_resp.content, "html.parser")

                    # meta tag publish time
                    meta_time = art_soup.find("meta", {"property": "article:published_time"})
                    if meta_time and meta_time.get("content"):
                        pub_date_real = datetime.fromisoformat(meta_time["content"].replace("Z", "+00:00"))
                        pub_date_naive = pub_date_real.replace(tzinfo=None)
                except Exception as e:
                    print(f"[WARN] Gagal ambil tanggal asli CNBC: {e}")
        except:
            continue

        if pub_date_naive < date_limit:
            continue

        combined_text = f"{title} {description}".lower()
        kategori = "Lainnya"
        for cat, kws in categories.items():
            if any(kw in combined_text for kw in kws):
                kategori = cat
                break

        if kategori == "Lainnya":
            continue

        publisher = link.split("/")[2]
        results.append({
            "kategori": kategori,
            "judul": title,
            "ringkasan": description,
            "tanggal": pub_date_naive.strftime("%Y-%m-%d %H:%M"),
            "link": link,
            "penerbit": publisher,
            "sumber": source
        })

# --- Simpan ke database ---
save_to_db_pymysql(results)

# --- Simpan ke CSV ---
with open("berita_cyber.csv", "w", newline="", encoding="utf-8") as f:
    writer = csv.DictWriter(f, fieldnames=["kategori","judul","ringkasan","tanggal","link","penerbit","sumber"])
    writer.writeheader()
    writer.writerows(results)

# --- Output hasil scraping agar bisa dibaca PHP ---
total_berita = len(results)
print(f"Berhasil ambil dan simpan {total_berita} berita")

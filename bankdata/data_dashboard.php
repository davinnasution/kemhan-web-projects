<?php
header('Content-Type: application/json');
session_start();
include 'koneksi.php'; // pastikan $conn (mysqli) ada

// optional: batasi akses ke user login
if (!isset($_SESSION['username'])) {
    echo json_encode(['error' => 'unauthorized']);
    exit();
}

function tableExists($conn, $name){
    $name = $conn->real_escape_string($name);
    $r = $conn->query("SHOW TABLES LIKE '$name'");
    return $r && $r->num_rows > 0;
}

$response = [
    'admins' => 0,
    'publish' => 0,
    'draft' => 0,
    'months' => [],
    'monthly_counts' => []
];

// 1) jumlah admin (jika ada tabel admin)
if (tableExists($conn,'admin')) {
    $res = $conn->query("SELECT COUNT(*) AS cnt FROM admin");
    $row = $res->fetch_assoc();
    $response['admins'] = (int)$row['cnt'];
} elseif (tableExists($conn,'users')) {
    // fallback: hitung semua user
    $res = $conn->query("SELECT COUNT(*) AS cnt FROM users");
    $row = $res->fetch_assoc();
    $response['admins'] = (int)$row['cnt'];
}

// 2) publish / draft (jika ada tabel berita)
if (tableExists($conn,'berita')) {
    $res = $conn->query("SELECT 
        SUM(status='publish') AS publish,
        SUM(status='draft') AS draft
        FROM berita");
    if ($res) {
        $r = $res->fetch_assoc();
        $response['publish'] = (int)$r['publish'];
        $response['draft']   = (int)$r['draft'];
    }
}

// 3) tren per bulan — deteksi nama kolom tanggal di tabel berita
if (tableExists($conn,'berita')) {
    $possibleDateCols = ['tanggal_publish','created_at','tgl','date_publish','tanggal'];
    $dateCol = null;
    foreach ($possibleDateCols as $c) {
        $c = $conn->real_escape_string($c);
        $check = $conn->query("SHOW COLUMNS FROM berita LIKE '$c'");
        if ($check && $check->num_rows > 0) { $dateCol = $c; break; }
    }

    if ($dateCol) {
        // buat list 12 bulan terakhir (YYYY-MM)
        $months = [];
        $now = new DateTime('first day of this month');
        for ($i = 11; $i >= 0; $i--) {
            $m = clone $now;
            $m->modify("-{$i} months");
            $months[] = $m->format('Y-m');
        }

        // ambil counts per bulan
        $start = $months[0] . '-01';
        $stmt = $conn->prepare(
            "SELECT DATE_FORMAT($dateCol, '%Y-%m') AS ym, COUNT(*) AS cnt
             FROM berita
             WHERE $dateCol IS NOT NULL AND $dateCol >= ?
             GROUP BY ym
             ORDER BY ym ASC"
        );
        $stmt->bind_param("s", $start);
        $stmt->execute();
        $res = $stmt->get_result();

        $countsMap = [];
        while ($r = $res->fetch_assoc()) {
            $countsMap[$r['ym']] = (int)$r['cnt'];
        }

        $finalCounts = [];
        foreach ($months as $m) {
            $finalCounts[] = $countsMap[$m] ?? 0;
        }

        $response['months'] = $months;
        $response['monthly_counts'] = $finalCounts;
    } else {
        // tidak ada kolom tanggal, kirim array kosong
        $response['months'] = [];
        $response['monthly_counts'] = [];
    }
}

echo json_encode($response);
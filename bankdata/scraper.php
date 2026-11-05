<?php
header('Content-Type: application/json');

// Jalankan Python scraper
$output = shell_exec("python C:/xampp/htdocs/data_crawl/scraping.py 2>&1");

// Balikin hasil dalam bentuk JSON
if ($output === null) {
    echo json_encode([
        "status" => "error",
        "message" => "Gagal menjalankan scraper."
    ]);
} else {
    echo json_encode([
        "status" => "success",
        "message" => "Scraping selesai!",
        "log" => $output
    ]);
}
?>

<?php include 'sidebar.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Log Aktivitas Admin</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #fff;
      overflow-x: hidden;
    }

    .main {
    margin-left: 220px;       /* kasih jarak sesuai lebar sidebar */
    min-height: 100vh;
    }

    /* HEADER */
    .header {
      background-color: #b22222;
      padding: 15px 20px;
      color: white;
      font-size: 20px;
      font-weight: bold;
      width: 100%;
    }

    .container {
      max-width: 1100px;
      margin: 20px auto;
      padding: 20px;
      background-color: #8b0000; /* Merah maroon */
      border-radius: 8px;
      color: #fff;
    }

    h2 {
      margin-top: 0;
      color: #fff;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;
      color: #000;
      border-radius: 5px;
      overflow: hidden;
    }

    thead {
      background-color: #b22222; /* Merah maroon lebih terang */
      color: #fff;
    }

    th, td {
      padding: 12px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }

    tbody tr:hover {
      background-color: #f2f2f2;
    }

    .status-online {
      color: green;
      font-weight: bold;
    }

    .status-offline {
      color: red;
      font-weight: bold;
    }

    .badge {
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 12px;
      color: #fff;
    }
    .publish {
      background-color: green;
    }
    .edit {
      background-color: orange;
    }
    .draft {
      background-color: gray;
    }
  </style>
</head>
<body>

<div class="container">
  
  <h2>Log Aktivitas Admin</h2>
  <table>
    <thead>
      <tr>
        <th>Status</th>
        <th>Nama</th>
        <th>ID Admin</th>
        <th>Aktivitas Terakhir</th>
        <th>Judul Berita</th>
        <th>Waktu</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="status-online">Online</td>
        <td>Raka Pratama</td>
        <td>ADM-0012</td>
        <td><span class="badge publish">Publish</span></td>
        <td>Rilis Fitur Autentikasi 2FA</td>
        <td>15 Agu 2025 09:42</td>
      </tr>
      <tr>
        <td class="status-offline">Offline</td>
        <td>Nadia Salsabila</td>
        <td>ADM-0045</td>
        <td><span class="badge edit">Edit</span></td>
        <td>Update Kurs Tengah BI</td>
        <td>15 Agu 2025 08:15</td>
      </tr>
      <tr>
        <td class="status-online">Online</td>
        <td>Yusuf Ramadhan</td>
        <td>ADM-0009</td>
        <td><span class="badge draft">Draft</span></td>
        <td>Roadmap Pengembangan Cyber Media Q4</td>
        <td>15 Agu 2025 07:58</td>
      </tr>
    </tbody>
  </table>
</div>

</body>
</html>
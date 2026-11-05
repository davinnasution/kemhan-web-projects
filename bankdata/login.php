<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login Admin - Kemenhan</title>
  <style>

    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: linear-gradient(135deg, #800000, #0d1b2a, #1b263b);
      background-size: 300% 300%;
      animation: gradientShift 15s ease infinite;
      position: relative;
      overflow: hidden;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    body::before {
      position: absolute;
      top: 0;
      left: -50%;
      width: 200%;
      height: 100%;
      background: linear-gradient(
        120deg,
        rgba(255, 255, 255, 0.25) 0%,
        rgba(255, 255, 255, 0.1) 20%,
        rgba(255, 255, 255, 0) 60%
      );
      transform: skewY(-10deg);
      animation: glossyMove 6s linear infinite;
    }

    @keyframes gradientShift {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    @keyframes glossyMove {
      0% { top: 100%; }
      100% { top: 100%; }
    }

    .login-container {
      background: rgba(255, 255, 255, 0.05); /* Abu gelap */
      padding: 40px 30px;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
      width: 100%;
      max-width: 400px;
      color: white;
      text-align: center;
      border: 1px solid rgba(255, 255, 255, 0.35);
      backdrop-filter: blur(10px);
    }

    .login-container img {
      width: 80px;
      margin-bottom: 1px;
      border-radius: 50%;
      border: 2px solid #fff;
    }

    .login-container h2 {
      margin-bottom: 20px;
      font-size: 20px;
    }

    label {
      display: block;
      text-align: left;
      margin-bottom: 5px;
      font-weight: bold;
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border: none;
      border-radius: 4px;
      box-sizing: border-box;
    }

    button {
      width: 100%;
      padding: 12px;
      background: linear-gradient(135deg, #800000, #0d1b2a, #1b263b);
      background-size: 300% 300%;
      color: white;
      border: 1px solid rgba(255, 255, 255, 0.8);
      font-size: 16px;
      border-radius: 10px;
      cursor: pointer;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      animation: gradientShift 15s ease infinite; /* sama kayak body */
     }

button:hover {
  transform: translateY(-2px);
  box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
}

    .note {
      font-size: 13px;
      margin-top: 15px;
      color: #ccc;
    }
  </style>
</head>
<body>

  <div class="login-container">
    <img src="logo-kemhan.png" alt="Logo Kemhan">
    <h2>
      BankData<br>
      Sistem Crawling Informasi Siber<br>
      Pussiber Inteltekhan Kemhan
    </h2>
    <form action="proses_login.php" method="POST">
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" required>

      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required>

      <button type="submit">Login</button>
    </form>
    <div class="note">
      Hanya untuk pengguna yang memiliki akses resmi.
    </div>
  </div>

</body>
</html>

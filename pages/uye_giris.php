<?php session_start(); ?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Üye Girişi</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      height: 100vh;
      background-color: rgb(241, 234, 221);
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .container {
      display: flex;
      align-items: stretch;
      gap: 0;
    }
    .left-image img {
      height: 700px;
      width: 700px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
    .form-container {
      background-color: white;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
      width: 450px;
      height: 700px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .form-container h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }
    .form-container input[type="email"],
    .form-container input[type="password"] {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    .form-container input[type="submit"] {
      width: 100%;
      padding: 10px;
      background-color: #f57235;
      border: none;
      color: white;
      font-weight: bold;
      border-radius: 6px;
      cursor: pointer;
      margin-top: 10px;
    }
    .form-container a {
      display: block;
      text-align: center;
      margin-top: 10px;
      font-size: 14px;
      color: #555;
      text-decoration: none;
    }
    .form-container a:hover {
      color: #f57235;
    }

    #modal {
      display: none; position: fixed; top: 0; left: 0; width: 100%;
      height: 100%; background: rgba(0,0,0,0.5);
      justify-content: center; align-items: center; z-index: 999;
    }

    #modal .modal-content {
      background: white; padding: 20px; border-radius: 10px;
      width: 300px; position: relative;
    }

    .close-btn {
      position: absolute; top: 10px; right: 15px; font-size: 20px; cursor: pointer;
    }

    @media (max-width: 900px) {
      .container {
        flex-direction: column;
        gap: 30px;
        align-items: center;
      }
      .left-image img,
      .form-container {
        height: auto;
      }
    }
  </style>
</head>
<body>
<div class="container">
  <div class="left-image">
    <img src="../assets/images/login-bg.jpg" alt="Pera Pilates Görseli">
  </div>
  <div class="form-container">
    <h2>Üye Girişi</h2>
    <form action="giris_kontrol.php" method="post">
      <input type="hidden" name="tur" value="uye">
      <input type="email" name="eposta" placeholder="E-posta" required>
      <input type="password" name="sifre" placeholder="Şifre" required>
      <input type="submit" value="Giriş Yap">
      <a href="uye_kayit.php">Kayıt Ol</a>
      <a href="#" onclick="document.getElementById('modal').style.display='flex'">Şifremi Unuttum</a>
    </form>
  </div>
</div>

<!-- Modal -->
<div id="modal">
  <div class="modal-content">
    <span class="close-btn" onclick="document.getElementById('modal').style.display='none'">&times;</span>
    <h3>Şifremi Unuttum</h3>
    <form action="sifre_mail_gonder.php" method="post">
      <input type="email" name="eposta" placeholder="E-posta adresiniz" required style="width: 100%; padding: 10px; margin-bottom: 10px;">
      <button type="submit" style="width: 100%; padding: 10px; background-color: #f57235; color: white; border: none; border-radius: 6px;">E-posta Gönder</button>
    </form>
  </div>
</div>
</body>
</html>

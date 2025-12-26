<?php session_start(); ?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Üye Kayıt</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      height: 100vh;
      background-color:rgb(241, 234, 221);
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .container {
      display: flex;
      align-items: stretch;
      gap: 0px;
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
    .form-container input,
    .form-container textarea {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    .form-container input[type="submit"] {
      background-color: #f57235;
      color: white;
      font-weight: bold;
      cursor: pointer;
      margin-top: 10px;
    }
    .form-container input[type="submit"]:hover {
      background-color: #c12;
    }
    @media (max-width: 900px) {
      .container { flex-direction: column; gap: 30px; align-items: center; }
      .left-image img, .form-container { height: auto; }
    }
  </style>
</head>
<body>
<div class="container">
  <div class="left-image">
    <img src="../assets/images/kayit-ol.jpg" alt="Pera Pilates Görseli">
  </div>
  <div class="form-container">
    <h2>Üye Kayıt</h2>
    <form method="post" action="uye_kayit_kontrol.php" onsubmit="return sifreKontrol()">
      <input type="text" name="ad" placeholder="Ad" required>
      <input type="text" name="soyad" placeholder="Soyad" required>
      <input type="email" name="eposta" placeholder="E-posta" required>
      <input type="password" id="sifre" name="sifre" placeholder="Şifre" required>
      <input type="password" id="sifre_tekrar" placeholder="Şifre (Tekrar)" required>
      <input type="text" name="telefon" placeholder="Telefon">
      <input type="text" name="kan_grubu" placeholder="Kan Grubu">
      <textarea name="saglik_durumu" placeholder="Sağlık Durumu"></textarea>
      <input type="submit" name="kayit" value="Kayıt Ol">
    </form>
  </div>
</div>

<script>
function sifreKontrol() {
  var s1 = document.getElementById("sifre").value;
  var s2 = document.getElementById("sifre_tekrar").value;
  if (s1 !== s2) {
    alert("Şifreler uyuşmuyor!");
    return false;
  }
  return true;
}
</script>
</body>
</html>

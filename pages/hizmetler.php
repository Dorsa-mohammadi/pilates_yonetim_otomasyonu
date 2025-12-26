<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hizmetler - Pera Pilates</title>
  <style>
    * { box-sizing: border-box; }

    body, html {
      margin: 0;
      padding: 0;
      font-family: Georgia;
      background-color: #fff;
    }

    header {
      background-color: #FAFAFA;
      padding: 10px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }

    .logo img {
      height: auto;
      max-width: 120px;
    }

    .menu-container {
      flex: 1;
      display: flex;
      justify-content: center;
    }

    nav {
      display: flex;
      gap: 60px;
    }

    nav a {
      text-decoration: none;
      color: #333;
      font-weight: bold;
      font-size: 20px;
      transition: all 0.3s ease;
    }

    nav a:hover {
      color: #f57235;
    }

    .hamburger, .slide-menu {
      display: none;
    }

    @media (max-width: 768px) {
      .menu-container {
        display: none;
      }
      .hamburger {
        display: block;
        font-size: 30px;
        background: none;
        border: none;
        color: #333;
      }

      .slide-menu {
        display: flex;
        flex-direction: column;
        position: absolute;
        top: 80px;
        right: 20px;
        background: white;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        z-index: 99;
      }

      .slide-menu a {
        padding: 12px 20px;
        border-bottom: 1px solid #ddd;
        text-decoration: none;
        color: #333;
      }

      .slide-menu a:hover {
        background-color: #eee;
        color: #f57235;
      }
    }

    footer {
      text-align: center;
      padding: 20px;
      background: #f2f2f2;
      color: #777;
      font-size: 14px;
      margin-top: 60px;
    }

    .hizmetler-container {
      padding: 60px 20px;
      text-align: center;
    }

    .hizmetler-container h2 {
      font-size: 36px;
      margin-bottom: 10px;
      font-weight: bold;
    }

    .hizmetler-underline {
      width: 60px;
      height: 4px;
      background-color: #f57235;
      margin: 0 auto 40px;
      border-radius: 2px;
    }

    .hizmetler-grid {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 30px;
    }

    .hizmet-karti {
      background-color: #FFFAFA;
      border-radius: 16px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.08);
      padding: 30px 20px;
      max-width: 280px;
      transition: transform 0.3s ease;
    }

    .hizmet-karti:hover {
      transform: translateY(-8px);
    }

    .hizmet-karti h3 {
      color: #f57235;
      font-size: 20px;
      margin-bottom: 15px;
    }

    .hizmet-karti p {
      color: #555;
      font-size: 15px;
      line-height: 1.5;
    }
  </style>
</head>
<body>

<header>
  <div class="logo">
    <a href="../anasayfa.php"><img src="../assets/images/pera_logo.png" alt="Pera Pilates"></a>
  </div>

  <div class="menu-container">
    <nav>
      <a href="../anasayfa.php">Anasayfa</a>
      <a href="../anasayfa.php#hakkimizda">Hakkımızda</a>
      <a href="hizmetler.php">Hizmetler</a>
      <a href="../anasayfa.php#iletisim">İletişim</a>
    </nav>
  </div>

  <button class="hamburger" onclick="toggleMenu()">☰</button>
  <div class="slide-menu" id="slideMenu">
    <a href="uye_giris.php">Üye Girişi</a>
    <a href="admin_giris.php">Yönetici Girişi</a>
    <a href="uye_kayit.php">Üye Kayıt</a>
  </div>
</header>

<section class="hizmetler-container">
  <h2>Hizmetlerimiz</h2>
  <div class="hizmetler-underline"></div>

  <div class="hizmetler-grid">
    <div class="hizmet-karti">
      <h3>Mat Pilates</h3>
      <p>Vücut ağırlığını kullanarak yapılan bu klasik pilates yöntemi, kasları güçlendirir, esnekliği artırır ve dengeyi geliştirir. Her seviyeye uygundur.</p>
    </div>

    <div class="hizmet-karti">
      <h3>Hamile Pilatesi</h3>
      <p>Anne adayları için özel olarak hazırlanan bu derslerde, gebelik sürecine uygun güvenli hareketlerle hem beden hem de ruh sağlığı desteklenir.</p>
    </div>

    <div class="hizmet-karti">
      <h3>Grup Dersleri</h3>
      <p>Küçük gruplar halinde yapılan bu derslerle motivasyonunuzu artırır, sosyalleşirken aynı zamanda bedeninizi güçlendirirsiniz.</p>
    </div>

    <div class="hizmet-karti">
      <h3>Özel Dersler</h3>
      <p>Kişisel hedeflerinize özel programlanan birebir seanslarla hızlı ve etkili sonuçlar elde edebilirsiniz. Eğitmeniniz size özel rehberlik eder.</p>
    </div>

    <div class="hizmet-karti">
      <h3>Postür Analizi</h3>
      <p>Vücut analiziniz yapılır ve yanlış duruş alışkanlıklarınızı düzeltmeye yönelik özel egzersizler uygulanır.</p>
    </div>
  </div>
</section>

<footer>
  &copy; <?= date('Y') ?> Pera Pilates. Tüm hakları saklıdır.
</footer>

<script>
  function toggleMenu() {
    const menu = document.getElementById('slideMenu');
    menu.style.display = (menu.style.display === 'flex') ? 'none' : 'flex';
    menu.style.flexDirection = 'column';
  }
</script>

</body>
</html>

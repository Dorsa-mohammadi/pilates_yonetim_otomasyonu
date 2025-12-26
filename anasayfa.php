<!DOCTYPE html><html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pera Pilates Studio</title>
  <style>
    * {
      box-sizing: border-box;
    }html, body {
  margin: 0;
  padding: 0;
  height: 100%;
  font-family: 'Georgia';
  background: #fff;
  color: #333;
}

body {
  display: flex;
  flex-direction: column;
}

header {
  background-color: #FAFAFA;
  height: 90px;
  overflow: visible;
  padding: 0 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: relative;
}

.logo img {
  height: auto;
  max-height: 120px;
}

nav {
  display: flex;
  gap: 60px;
}

nav a {
  font-size:20px;
  color: #333;
  text-decoration: none;
  font-weight: 900;
  transition: all 0.3s ease;
}

nav a:hover {
  color: #f57235;
  transform: scale(1.05);
}

.hamburger {
  font-size: 34px;
  cursor: pointer;
  background: none;
  border: none;
  color: #333;
  transform: scale(1.1);
  transition: all 0.3s ease;
  display: block;
  z-index: 100;
}

.hamburger:hover{
  color: #f57235;
  transform: scale(1.1);
  transition: all 0.3s ease;
}

.slide-menu {
  position: absolute;
  top: 100%;
  right: 0;
  background-color: #f9f9f9;
  border-radius: 6px;
  display: none;
  flex-direction: column;
  min-width: 180px;
  z-index: 99;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.slide-menu a {
  padding: 12px 16px;
  text-decoration: none;
  color: #333;
  border-bottom: 1px solid #ddd;
  transition: background 0.3s;
}

.slide-menu a:hover {
  background-color: #eee;
  color: #f57235;
}

.hero {
  flex: 1;
  text-align: center;
  padding: 50px 20px;
  background: url('assets/images/hero.jpg') no-repeat center center/cover;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 550px;
}

.hero-content {
  z-index: 2;
  background-color: rgba(241, 225, 225, 0.8);
  padding: 60px;
  border-radius: 10px;
  max-width: 600px;
}

.hero h1 {
  font-size: 50px;
  margin-bottom: 35px;
  color: #222;
}

.hero p {
  font-size: 28px;
  margin-bottom: 40px;
  color: #444;
}

.buttons {
  display: flex;
  justify-content: center;
  gap: 15px;
  flex-wrap: wrap;
}

.btn {
  padding: 22px 34px;
  background:rgb(245, 114, 53);
  color: white;
  border: none;
  font-size: 26px;
  border-radius: 20px;
  cursor: pointer;
  text-decoration: none;
}



footer {
  text-align: center;
  padding: 20px;
  background-color: #f2f2f2;
  font-size: 14px;
  color: #777;
}

@media (max-width: 768px) {
  nav {
    display: none;
  }
  .hamburger {
    display: block;
  }
}

  </style>
</head>
<body><header>
  <div class="logo">
    <img src="assets/images/pera_logo.png" alt="Pera Pilates">
  </div>
  <nav>
    <a href="#">Anasayfa</a>
    <a href="#hakkimizda">Hakkımızda</a>
    <a href="pages/hizmetler.php">Hizmetler</a>

    <a href="#iletisim">İletişim</a>
  </nav>
  <button class="hamburger" onclick="toggleMenu()">☰</button>
  <div class="slide-menu" id="slideMenu">
    <a href="pages/uye_giris.php">Üye Girişi</a>
    <a href="pages/admin_giris.php">Yönetici Girişi</a>
    <a href="pages/uye_kayit.php">Üye Kayıt</a>
  </div>
</header><section class="hero">
  <div class="hero-content">
    <h1 style="font-family:'Great Vibes';">Pera Pilates</h1>
    <p style="font-family: 'open sans';">Daha iyi hareket et,Daha iyi görün, <br>Daha iyi hisset...</p>
    <div class="buttons">
      <a href="pages/uye_kayit.php" class="btn">Bize Katılın</a>
    </div>
  </div>
</section>

<section style="padding: 150px 30px; background-color: #ffffff;">
  <div style="max-width: 800px; margin: 0 auto; text-align: center;">
    <h2 style="font-size: 36px; color: #333; font-family: 'Georgia', sans-serif; margin-bottom: 10px;">
      <span style="color: #f57235;">Neden</span> <span style="font-weight: bold;">Pera Pilates?</span>
    </h2>
    <hr style="width: 60px; border: 2px solid #f57235; margin: 20px auto;">
    <ul style="list-style-type: disc; text-align: left; padding-left: 20px; font-size: 22px; color: #444; line-height: 1.8;">
      <li>Zinde Bir Vücut İçin Çalış</li>
      <li>Güçlü Bir Beden, Düzgün Bir Duruş İçin: PİLATES</li>
      <li>Fit Olmak Bir Hedef Değil, Yaşam Biçimidir.</li>
      <li>Zinde Bir Vücut İçin Çalış</li>
    </ul>
  </div>
</section>


<section id="hakkimizda" style="background-color: #fff7f2; padding: 80px 20px;">
  <div style="max-width: 1100px; margin: 0 auto; display: flex; flex-wrap: wrap; align-items: center; gap: 40px;">
    
    <!-- Sol: Görsel -->
    <div style="flex: 1 1 45%; text-align: center;">
      <img src="assets/images/hakkimizda.jpg" alt="Hakkımızda Görseli" style="max-width: 100%; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
    </div>

    <!-- Sağ: Metin -->
    <div style="flex: 1 1 50%;">
      <h2 style="font-size: 36px; color: #f57235; font-family: 'Georgia', serif; margin-bottom: 20px;">Hakkımızda</h2>

      <p style="font-size: 17px; color: #444; line-height: 1.8; font-family: 'Open Sans', sans-serif; margin-bottom: 20px;">
        <strong style="color: #333;">Pera Pilates</strong> olarak bedeninizi güçlendirirken ruhunuzu da dinlendirmeyi amaçlıyoruz.
        Uzman eğitmen kadromuz ve kişiye özel programlarımızla, her seviyeye uygun ders seçenekleri sunuyoruz.
        Modern stüdyomuzda, hem klasik mat pilates hem de ekipmanlı pilates dersleriyle sağlıklı bir yaşam için yanınızdayız.
      </p>

      <p style="font-size: 17px; color: #444; line-height: 1.8;">
        Misyonumuz; <strong style="color: #f57235;">doğru hareket</strong>, <strong style="color: #f57235;">düzenli egzersiz</strong> ve 
        <strong style="color: #f57235;">bilinçli nefes teknikleri</strong>yle hayat kalitenizi artırmak.
        Samimi ve destekleyici ortamımızda, hedeflerinize ulaşmanız için sizi her adımda motive ediyoruz.
      </p>

      <p style="font-style: italic; color: #555; font-size: 16px; margin-top: 30px;">
        “Siz de kendiniz için bir adım atın, bedeninizi keşfedin ve güçlenin.”
      </p>

      <h3 style="margin-top: 25px; font-size: 18px; color: #333; font-weight: bold;">
        Pera Pilates – Güç, denge ve huzurun buluşma noktası.
      </h3>
    </div>
  </div>
</section>

<section id="iletisim" style="padding: 80px 20px; background-color: #fff7f2;">
  <div style="max-width: 1100px; margin: 0 auto; display: flex; flex-wrap: wrap; gap: 40px; align-items: center;">
    
    <!-- SOL: Google Harita -->
    <div style="flex: 1 1 48%;">
      <iframe 
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3067.636927510322!2d39.50392657511211!3d39.74780579608207!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40700de974110577%3A0x883eecd29a74acec!2sPera%20Pilates!5e0!3m2!1str!2str!4v1747739495120!5m2!1str!2str" 
        width="100%" height="350" style="border:0; border-radius: 12px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
      </iframe>
    </div>

    <!-- SAĞ: Bilgiler -->
    <div style="flex: 1 1 48%;">
      <h2 style="font-size: 36px; color: #f57235; font-family: 'Georgia', serif; margin-bottom: 20px;">İletişim</h2>

      <p style="font-size: 17px; color: #444; line-height: 1.8; font-family: 'Open Sans', sans-serif; margin-bottom: 15px;">
        <strong>Adres:</strong><br>
        Vakıf Bank Karşısı Ünsal Plaza, Atatürk, Fevzi Paşa Cd. No:21<br>
        D:404, 4. Kat, 24000 Erzincan Merkez / Erzincan
      </p>

      <p style="font-size: 17px; color: #444; margin-bottom: 10px;">
        <strong>Instagram:</strong><br>
        <a href="https://instagram.com/perapilatestudio" target="_blank" style="color: #f57235; text-decoration: none;">
          @perapilatesstudio
        </a>
      </p>

      <p style="font-size: 16px; color: #777; font-style: italic;">Bize ulaşın, bedeninize ve ruhunuza iyi gelecek ilk adımı atın!</p>
    </div>

  </div>
</section>




<footer>
  &copy; <?= date('Y') ?> Pera Pilates. Tüm hakları saklıdır.
</footer><script>
  function toggleMenu() {
    const menu = document.getElementById('slideMenu');
    if (menu.style.display === 'flex') {
      menu.style.display = 'none';
    } else {
      menu.style.display = 'flex';
      menu.style.flexDirection = 'column';
    }
  }
</script></body>
</html>


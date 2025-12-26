<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config.php';

// Admin kontrolü
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin' || !isset($_SESSION['kullanici_id'])) {
    header("Location: admin_giris.php");
    exit;
}

// Sayı değişkenlerini sıfırla
$bekleyenUyelik = 0;
$bekleyenRandevu = 0;
$bekleyenMesaj = 0;
$anketCevap = 0;

// Uyelik talepleri sayısı (tablo varsa)
try {
    $stmt = $conn->query("SELECT COUNT(*) FROM uyelik_talepleri WHERE durum = 'beklemede'");
    $bekleyenUyelik = $stmt->fetchColumn();
} catch (PDOException $e) {
    $bekleyenUyelik = 0;
}

// Randevu talepleri sayısı
try {
    $stmt = $conn->query("SELECT COUNT(*) FROM randevular WHERE durum = 'beklemede'");
    $bekleyenRandevu = $stmt->fetchColumn();
} catch (PDOException $e) {
    $bekleyenRandevu = 0;
}

// Okunmamış mesaj sayısı
try {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM mesajlar WHERE alici_id = ? AND okundu = 0");
    $stmt->execute([$_SESSION['kullanici_id']]);
    $bekleyenMesaj = $stmt->fetchColumn();
} catch (PDOException $e) {
    $bekleyenMesaj = 0;
}

// Anket cevapları sayısı (tablo varsa)
try {
    $stmt = $conn->query("SELECT COUNT(*) FROM anket_cevaplari WHERE okundu = 0");
    $anketCevap = $stmt->fetchColumn();
} catch (PDOException $e) {
    $anketCevap = 0;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Yönetici Paneli</title>
  <style>
    body { margin: 0; font-family: Georgia, serif; display: flex; height: 100vh; }
    header {
      position: fixed;
      top: 0; left: 0;
      width: 100%;
      background-color: #333;
      color: white;
      padding: 15px;
      z-index: 999;
    }
    nav {
      margin-top: 60px;
      width: 230px;
      background-color: #333;
      color: white;
      padding: 20px;
      height: calc(100vh - 60px);
      overflow-y: auto;
    }
    nav h2 { color: #f57235; }
    nav a {
      color: white;
      text-decoration: none;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px;
      margin-bottom: 10px;
      border-radius: 4px;
      background-color: #444;
    }
    nav a:hover {
      background-color: #f57235;
    }
    .badge {
      background-color: #f57235;
      color: white;
      font-size: 12px;
      padding: 2px 7px;
      border-radius: 12px;
      margin-left: 5px;
    }
    iframe {
      flex: 1;
      margin-top: 60px;
      height: calc(100vh - 60px);
      border: none;
    }
  </style>
</head>
<body>
  <header>
    <strong>Yönetici Paneli</strong> - Hoş geldiniz, <?= htmlspecialchars($_SESSION['eposta']) ?>
  </header>

  <nav>
    <h2>Menü</h2>
    <a href="uyelik_talepleri.php" onclick="loadSayfa(event, this.href)">
      Üyelik Talepleri
      <?php if ($bekleyenUyelik > 0): ?><span class="badge"><?= $bekleyenUyelik ?></span><?php endif; ?>
    </a>
    <a href="admin_ders_programi.php" onclick="loadSayfa(event, this.href)">Ders Programı</a>
    <a href="admin_randevu_talepleri.php" onclick="loadSayfa(event, this.href)">
      Randevu Talepleri
      <?php if ($bekleyenRandevu > 0): ?><span class="badge"><?= $bekleyenRandevu ?></span><?php endif; ?>
    </a>
    <a href="anket_olustur.php" onclick="loadSayfa(event, this.href)">Anket Oluştur</a>
    <a href="anket_sonuclari.php" onclick="loadSayfa(event, this.href)">
      Anket Cevapları
      <?php if ($anketCevap > 0): ?><span class="badge"><?= $anketCevap ?></span><?php endif; ?>
    </a>
    <a href="admin_mesaj.php" onclick="loadSayfa(event, this.href)">
      Mesajlar
      <?php if ($bekleyenMesaj > 0): ?><span class="badge"><?= $bekleyenMesaj ?></span><?php endif; ?>
    </a>
    <a href="admin_uyeler.php" onclick="loadSayfa(event, this.href)">Üyeler</a>
    <a href="admin_odemeler.php" onclick="loadSayfa(event, this.href)">Ödemeler</a>
    <a href="cikis.php">Çıkış</a>
  </nav>

  <iframe id="icerik" src="uyelik_talepleri.php"></iframe>

  <script>
    function loadSayfa(e, url) {
      e.preventDefault();
      document.getElementById("icerik").src = url;
    }
  </script>
</body>
</html>

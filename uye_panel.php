<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';

$kullanici_id = $_SESSION['kullanici_id'] ?? 0;
$eposta = $_SESSION['eposta'] ?? 'Üye';

// Bildirim sayaçları
$okunmamisMesaj = 0;
$cevaplanmamisAnket = 0;
$bugunkuRandevu = 0;

try {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM mesajlar WHERE alici_id = ? AND okundu = 0");
    $stmt->execute([$kullanici_id]);
    $okunmamisMesaj = $stmt->fetchColumn();
} catch (PDOException $e) {}

try {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM anketler WHERE id NOT IN (
        SELECT anket_id FROM anket_cevaplari WHERE uye_id = ?
    )");
    $stmt->execute([$kullanici_id]);
    $cevaplanmamisAnket = $stmt->fetchColumn();
} catch (PDOException $e) {}

try {
    $bugun = date('Y-m-d');
    $stmt = $conn->prepare("SELECT COUNT(*) FROM randevular 
        WHERE uye_id = ? AND tarih = ? AND durum = 'onaylandi'");
    $stmt->execute([$kullanici_id, $bugun]);
    $bugunkuRandevu = $stmt->fetchColumn();
} catch (PDOException $e) {}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Üye Paneli</title>
  <style>
    body {
      margin: 0;
      font-family: Georgia, serif;
      display: flex;
      height: 100vh;
    }
    header {
      position: fixed;
      top: 0; left: 0;
      width: 100%;
      background-color: #2c3e50;
      color: white;
      padding: 15px;
      z-index: 999;
    }
    nav {
      margin-top: 60px;
      width: 230px;
      background-color: #2c3e50;
      color: white;
      padding: 20px;
      height: calc(100vh - 60px);
      overflow-y: auto;
    }
    nav h2 {
      color: #f57235;
      font-size: 24px;
      margin-bottom: 20px;
    }
    nav a {
      color: white;
      text-decoration: none;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px;
      margin-bottom: 10px;
      border-radius: 4px;
      background-color: #34495e;
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
    }
    .icerik {
      flex: 1;
      margin-top: 60px;
      padding: 30px;
      background-color: #f8f8f8;
      overflow-y: auto;
    }
  </style>
</head>
<body>
  <header>
    <strong>Üye Paneli</strong> - Hoş geldiniz, <?= htmlspecialchars($eposta) ?>
  </header>

  <nav>
    <h2>Menü</h2>
    
    <a href="?sayfa=bilgilerim">Bilgilerim</a>
    <a href="?sayfa=ders_programi">
      Ders Programı
      <?php if ($bugunkuRandevu > 0): ?><span class="badge"><?= $bugunkuRandevu ?></span><?php endif; ?>
    </a>
    <a href="?sayfa=mesajlar">
      Mesajlar
      <?php if ($okunmamisMesaj > 0): ?><span class="badge"><?= $okunmamisMesaj ?></span><?php endif; ?>
    </a>
    <a href="?sayfa=anketler">
      Anketler
      <?php if ($cevaplanmamisAnket > 0): ?><span class="badge"><?= $cevaplanmamisAnket ?></span><?php endif; ?>
    </a>
    <a href="?sayfa=odemelerim">Ödemelerim</a>
    <a href="/pages/cikis.php">Çıkış</a>
  </nav>

  <div class="icerik">
    <?php
      $sayfa = $_GET['sayfa'] ?? 'hosgeldin';

      switch ($sayfa) {
        case 'bilgilerim':
          require 'pages/bilgilerim.php';
          break;
        case 'ders_programi':
          require 'uye_ders_programi.php';
          break;
        case 'mesajlar':
          require 'pages/uye_mesajlar.php';
          break;
        case 'anketler':
          require 'pages/uye_anketler.php';
          break;
        case 'odemelerim':
          require 'pages/odemelerim.php';
          break;
        case 'hosgeldin':
        default:
          echo "<h1 style='color:#f57235'>Hoşgeldiniz!</h1>";
          echo "<p>".htmlspecialchars($eposta)." adresiyle giriş yaptınız.</p>";
          echo "<p>Soldaki menüden seçim yaparak devam edebilirsiniz.</p>";
          break;
      }
    ?>
  </div>
</body>
</html>

<?php
session_start();
require_once '../config.php';

// Admin oturum kontrolü
if (!isset($_SESSION['kullanici_id']) || $_SESSION['rol'] !== 'admin') {
    echo "Erişim reddedildi.";
    exit;
}

$admin_id = $_SESSION['kullanici_id'];

// Üyeleri al
$uyeler = $conn->query("SELECT id, ad, soyad, kullanici_id FROM uyeler")->fetchAll(PDO::FETCH_ASSOC);

// Gelen mesajlar
$gelen = $conn->prepare("
    SELECT m.*, u.ad, u.soyad FROM mesajlar m
    LEFT JOIN uyeler u ON m.gonderen_id = u.kullanici_id
    WHERE m.alici_id = ? 
    ORDER BY m.tarih DESC
");
$gelen->execute([$admin_id]);
$gelen = $gelen->fetchAll(PDO::FETCH_ASSOC);

// Giden mesajlar
$giden = $conn->prepare("
    SELECT m.*, u.ad, u.soyad FROM mesajlar m
    LEFT JOIN uyeler u ON m.alici_id = u.kullanici_id
    WHERE m.gonderen_id = ?
    ORDER BY m.tarih DESC
");
$giden->execute([$admin_id]);
$giden = $giden->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Yönetici Mesaj Paneli</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f6f9;
      padding: 30px;
    }
    .container {
      max-width: 1000px;
      margin: auto;
    }
    h2 {
      color: #333;
      margin-bottom: 10px;
    }
    form {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }
    textarea, select {
      width: 100%;
      padding: 12px;
      margin-top: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }
    select[multiple] {
      height: 100px;
    }
    button {
      background: #f39c12;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 6px;
      margin-top: 10px;
      cursor: pointer;
    }
    .mesajlar {
      display: flex;
      gap: 30px;
      flex-wrap: wrap;
    }
    .liste {
      flex: 1;
      background: white;
      padding: 20px;
      border-radius: 10px;
      height: 400px;
      overflow-y: auto;
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
      min-width: 300px;
    }
    .liste h3 {
      margin-top: 0;
      font-size: 16px;
      color: #333;
    }
    .mesaj {
      background: #f1f1f1;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 10px;
    }
    .mesaj strong {
      color: #f39c12;
    }
    small {
      color: #777;
      font-size: 11px;
    }
    .alert {
      padding: 12px;
      margin-bottom: 20px;
      border-radius: 5px;
      font-weight: bold;
    }
    .alert-success {
      background: #d4edda;
      color: #155724;
    }
    .alert-error {
      background: #f8d7da;
      color: #721c24;
    }
  </style>
</head>
<body>
<div class="container">
  <h2>Üyelere Mesaj Gönder</h2>

    <?php if (isset($_GET['durum']) && $_GET['durum'] === 'ok'): ?>
      <div style="background:#d4edda; color:#155724; padding:10px; border-radius:5px; margin-bottom:15px;">
        ✅ Mesaj(lar) başarıyla gönderildi.
      </div>
    <?php elseif (isset($_GET['durum']) && $_GET['durum'] === 'eksik'): ?>
      <div style="background:#f8d7da; color:#721c24; padding:10px; border-radius:5px; margin-bottom:15px;">
        ❗ Tüm alanları doldurmalısınız.
      </div>
    <?php elseif (isset($_GET['durum']) && $_GET['durum'] === 'gecersiz'): ?>
      <div style="background:#fff3cd; color:#856404; padding:10px; border-radius:5px; margin-bottom:15px;">
        ⚠️ Geçersiz alıcı(lar) seçildi.
      </div>
    <?php endif; ?>


  <form action="mesaj_kaydet.php" method="post">
    <label>Üye Seç (Ctrl ile çoklu):</label>
    <select name="alici_id[]" multiple required>
      <?php foreach ($uyeler as $uye): ?>
        <option value="<?= $uye['kullanici_id'] ?>">
          <?= htmlspecialchars($uye['ad'] . ' ' . $uye['soyad']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <textarea name="mesaj" rows="4" placeholder="Mesajınızı yazın..." required></textarea>
    <button type="submit">Gönder</button>
  </form>

  <div class="mesajlar">
    <div class="liste">
      <h3>Giden Mesajlar</h3>
      <?php foreach ($giden as $m): ?>
        <div class="mesaj">
          <strong><?= htmlspecialchars($m['ad'] . ' ' . $m['soyad']) ?: 'Üye' ?></strong><br>
          <?= nl2br(htmlspecialchars($m['mesaj'])) ?><br>
          <small><?= $m['tarih'] ?></small>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="liste">
      <h3>Gelen Mesajlar</h3>
      <?php foreach ($gelen as $m): ?>
        <div class="mesaj">
          <strong><?= htmlspecialchars($m['ad'] . ' ' . $m['soyad']) ?: 'Üye' ?></strong><br>
          <?= nl2br(htmlspecialchars($m['mesaj'])) ?><br>
          <small><?= $m['tarih'] ?></small>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
</body>
</html>

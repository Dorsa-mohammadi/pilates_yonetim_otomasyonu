<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$uye_id = $_SESSION['kullanici_id'] ?? 0;

// Adminleri getir
$adminler = $conn->query("SELECT id, eposta FROM kullanicilar WHERE rol = 'admin'")->fetchAll(PDO::FETCH_ASSOC);

// Mesajlar
$gelenMesajlar = [];
$gidenMesajlar = [];
if ($uye_id > 0) {
    $gelen = $conn->prepare("SELECT m.*, k.eposta AS gonderen_eposta FROM mesajlar m
        JOIN kullanicilar k ON m.gonderen_id = k.id
        WHERE m.alici_id = ? ORDER BY m.tarih DESC");
    $gelen->execute([$uye_id]);
    $gelenMesajlar = $gelen->fetchAll(PDO::FETCH_ASSOC);

    $giden = $conn->prepare("SELECT m.*, k.eposta AS alici_eposta FROM mesajlar m
        JOIN kullanicilar k ON m.alici_id = k.id
        WHERE m.gonderen_id = ? ORDER BY m.tarih DESC");
    $giden->execute([$uye_id]);
    $gidenMesajlar = $giden->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Mesajlar</title>
  <style>
    * { box-sizing: border-box; }
    html, body {
      margin: 0;
      padding: 0;
      font-family: Georgia, sans-serif;
      background-color: #f4f6f9;
      overflow-x: hidden;
    }

    nav {
      width: 230px;
      height: 100vh;
      background-color: #1e2a38;
      color: white;
      padding: 30px 20px;
      position: fixed;
      top: 0;
      left: 0;
    }

    nav h2 {
      font-size: 22px;
      margin-bottom: 35px;
      color: #f57235;
      text-align: center;
    }

    nav a {
      display: block;
      color: white;
      text-decoration: none;
      padding: 12px 16px;
      border-radius: 6px;
      margin-bottom: 10px;
      font-size: 15px;
      transition: background 0.2s;
    }

    nav a:hover {
      background-color: #f57235;
    }

    nav a.active {
      background-color: #f57235;
      font-weight: bold;
    }

    .icerik {
      margin-left: 130px;
      padding: 20px 10px 50px 10px;
      width: calc(100% - 230px);
    }

    .mesaj-formu {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
      margin-bottom: 30px;
    }

    .mesaj-formu h2 {
      font-size: 20px;
      margin-bottom: 10px;
    }

    .mesaj-formu label {
      display: block;
      font-weight: bold;
      margin-top: 10px;
      margin-bottom: 5px;
    }

    .mesaj-formu select,
    .mesaj-formu textarea {
      width: 100%;
      padding: 10px;
      margin-bottom: 12px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }

    .mesaj-formu button {
      background: #f57235;
      color: white;
      padding: 10px 16px;
      font-size: 14px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: 0.2s;
    }

    .mesaj-formu button:hover {
      background: #e25a20;
    }

    .mesajlar-kutular {
      display: flex;
      gap: 20px;
      margin-top: 20px;
      flex-wrap: wrap;
    }

    .liste {
      flex: 1;
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
      height: 400px;
      overflow-y: auto;
      min-width: 300px;
    }

    .liste h3 {
      margin-top: 0;
      margin-bottom: 15px;
      font-size: 16px;
      color: #333;
      border-bottom: 1px solid #ddd;
      padding-bottom: 8px;
    }

    .mesaj {
      background: #f1f1f1;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 10px;
    }

    .mesaj strong {
      color: #f57235;
    }

    .mesaj small {
      color: #777;
      font-size: 11px;
    }

    #mesajDurum {
      text-align: center;
      margin-bottom: 15px;
      padding: 10px;
      border-radius: 8px;
      font-weight: bold;
      display: none;
    }

    @media screen and (max-width: 768px) {
      .mesajlar-kutular {
        flex-direction: column;
      }

      .icerik {
        margin-left: 230px;
        padding: 20px;
      }
    }
  </style>
</head>
<body>

<nav>
  <h2>Üye Paneli</h2>
  <a href="uye_panel.php?sayfa=bilgilerim">Bilgilerim</a>
  <a href="uye_panel.php?sayfa=ders_programi">Ders Programı</a>
  <a href="uye_panel.php?sayfa=mesajlar" class="active">Mesajlar</a>
  <a href="uye_panel.php?sayfa=anketler">Anketler</a>
  <a href="uye_panel.php?sayfa=odemeler">Ödemelerim</a>
  <a href="/pages/cikis.php">Çıkış</a>
</nav>

<div class="icerik">
  <div class="mesaj-formu">
    <h2>Yöneticinize Mesaj Gönder</h2>
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

    <div id="mesajDurum"></div>
    <form id="mesajFormu">
      <label>Yönetici Seç:</label>
      <select name="alici_id" required>
        <option value="">-- Seçiniz --</option>
        <?php foreach ($adminler as $admin): ?>
          <option value="<?= htmlspecialchars($admin['id']) ?>"><?= htmlspecialchars($admin['eposta']) ?></option>
        <?php endforeach; ?>
      </select>

      <label>Mesajınız:</label>
      <textarea name="mesaj" rows="4" placeholder="Mesajınızı yazın..." required></textarea>

      <button type="submit">Gönder</button>
    </form>
  </div>

  <div class="mesajlar-kutular">
    <div class="liste">
      <h3>Giden Mesajlar</h3>
      <?php foreach ($gidenMesajlar as $m): ?>
        <div class="mesaj">
          <strong><?= htmlspecialchars($m['alici_eposta']) ?></strong>
          <?= nl2br(htmlspecialchars($m['mesaj'])) ?><br>
          <small><?= $m['tarih'] ?></small>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="liste">
      <h3>Gelen Mesajlar</h3>
      <?php foreach ($gelenMesajlar as $m): ?>
        <div class="mesaj">
          <strong><?= htmlspecialchars($m['gonderen_eposta']) ?></strong>
          <?= nl2br(htmlspecialchars($m['mesaj'])) ?><br>
          <small><?= $m['tarih'] ?></small>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<script>
  document.getElementById('mesajFormu').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    fetch('/pages/mesaj_kaydet.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      const mesajDurum = document.getElementById('mesajDurum');
      mesajDurum.style.display = 'block';
      mesajDurum.style.backgroundColor =
        data.durum === 'ok' ? '#28a745' :
        data.durum === 'eksik' ? '#dc3545' :
        data.durum === 'gecersiz' ? '#ffc107' : '#6c757d';
      mesajDurum.textContent = data.mesaj;

      if (data.durum === 'ok') {
        form.reset();
        setTimeout(() => location.reload(), 1000);
      }
    });
  });
</script>

</body>
</html>

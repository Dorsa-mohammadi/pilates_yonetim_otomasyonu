<?php
require_once '../config.php';
session_start();
$FONK->oturumKontrol('admin');

// Üyeyi seçtirme ekranı
if (!isset($_GET['uye_id'])) {
    $uyeler = $conn->query("SELECT id, ad, soyad FROM uyeler ORDER BY ad ASC")->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <!DOCTYPE html>
    <html lang="tr">
    <head>
        <meta charset="UTF-8">
        <title>Üye Seç</title>
        <style>
            body { font-family: Arial; background: #f4f4f4; padding: 40px; }
            .takvim {
                max-width: 500px;
                margin: auto;
                background: white;
                padding: 30px;
                border-radius: 12px;
                box-shadow: 0 0 15px rgba(0,0,0,0.1);
                text-align: center;
            }
            select, button {
                width: 100%;
                padding: 12px;
                font-size: 16px;
                margin-top: 15px;
                border-radius: 6px;
                border: 1px solid #ccc;
            }
            button {
                background-color: #f57235;
                color: white;
                border: none;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <div class="takvim">
            <h2>Üye Seç</h2>
            <form method="GET" action="admin_odemeler.php">
                <select name="uye_id" required>
                    <option value="">Bir üye seçin</option>
                    <?php foreach ($uyeler as $uye): ?>
                        <option value="<?= $uye['id'] ?>"><?= htmlspecialchars($uye['ad'].' '.$uye['soyad']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Ödemeleri Göster</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Seçilen üye bilgisi
$uye_id = $_GET['uye_id'];
$uye = $conn->prepare("SELECT ad, soyad FROM uyeler WHERE id = ?");
$uye->execute([$uye_id]);
$uyeBilgi = $uye->fetch(PDO::FETCH_ASSOC);
$uye_ad_soyad = htmlspecialchars($uyeBilgi['ad'] . ' ' . $uyeBilgi['soyad']);

// Aylar dizisi
$aylar = ['01'=>'Ocak', '02'=>'Şubat', '03'=>'Mart', '04'=>'Nisan', '05'=>'Mayıs', '06'=>'Haziran',
          '07'=>'Temmuz', '08'=>'Ağustos', '09'=>'Eylül', '10'=>'Ekim', '11'=>'Kasım', '12'=>'Aralık'];

// Yıla göre Ocak–Aralık arası oluştur
$simdiki_yil = date('Y');
$baslangic = new DateTime("$simdiki_yil-01-01");
$ayListesi = [];
for ($i = 0; $i < 12; $i++) {
    $ay = clone $baslangic;
    $ay->modify("+$i month");
    $ayListesi[]= $ay;
}

// Güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['odeme'] as $tarih => $durum) {
        $stmt = $conn->prepare("SELECT id FROM odemeler WHERE uye_id = ? AND odeme_tarihi = ?");
        $stmt->execute([$uye_id, $tarih]);
        $var = $stmt->fetch();
        if ($var) {
            $conn->prepare("UPDATE odemeler SET odeme_durumu = ? WHERE id = ?")->execute([$durum, $var['id']]);
        } else {
            $conn->prepare("INSERT INTO odemeler (uye_id, tutar, odeme_tarihi, odeme_durumu) VALUES (?, 0, ?, ?)")
                  ->execute([$uye_id, $tarih, $durum]);
        }
    }
    echo "<script>alert('Güncelleme yapıldı.'); window.location.href='admin_odemeler.php?uye_id=$uye_id';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Üye Ödemeleri</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to bottom right, #f2f2f2, #eaeaea);
      margin: 0; padding: 40px;
    }
    .takvim {
      max-width: 750px;
      background: #fff;
      margin: auto;
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 6px 16px rgba(0,0,0,0.15);
    }
    h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #333;
    }
    .uye-ad {
      text-align: center;
      font-weight: bold;
      font-size: 18px;
      color: #f57235;
      margin-bottom: 15px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: center;
    }
    th {
      background-color: #f57235;
      color: white;
    }
    select {
      padding: 8px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
    button {
      margin-top: 25px;
      background: #f57235;
      color: white;
      padding: 12px 24px;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      display: block;
      width: 100%;
    }
  </style>
</head>
<body>
  <div class="takvim">
    <h2>Yıllık Ödeme Takvimi</h2>
    <div class="uye-ad">Üye: <?= $uye_ad_soyad ?></div>
    <form method="POST">
      <table>
        <tr>
          <th>Ay</th>
          <th>Durum</th>
        </tr>
        <?php foreach ($ayListesi as $ay): 
            $tarih = $ay->format('Y-m-01');
            $ay_adi = $aylar[$ay->format('m')] . ' ' . $ay->format('Y');
            $stmt = $conn->prepare("SELECT odeme_durumu FROM odemeler WHERE uye_id = ? AND odeme_tarihi = ?");
            $stmt->execute([$uye_id, $tarih]);
            $durum = $stmt->fetchColumn();
            $durum = in_array($durum, ['yapildi', 'bekleniyor', 'iptal']) ? $durum : 'bekleniyor';
        ?>
        <tr>
          <td><?= $ay_adi ?></td>
          <td>
            <select name="odeme[<?= $tarih ?>]">
              <option value="yapildi" <?= $durum == 'yapildi' ? 'selected' : '' ?>>Ödendi</option>
              <option value="bekleniyor" <?= $durum == 'bekleniyor' ? 'selected' : '' ?>>Bekleniyor</option>
              <option value="iptal" <?= $durum == 'iptal' ? 'selected' : '' ?>>İptal Edildi</option>
            </select>
          </td>
        </tr>
        <?php endforeach; ?>
      </table>
      <button type="submit">Kaydet</button>
    </form>
  </div>
</body>
</html>

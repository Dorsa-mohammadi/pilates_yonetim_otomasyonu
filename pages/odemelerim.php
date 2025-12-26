<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';
$FONK->oturumKontrol('uye');

$kullanici_id = $_SESSION['kullanici_id'];
$stmt = $conn->prepare("SELECT id, ad, soyad FROM uyeler WHERE kullanici_id = ?");
$stmt->execute([$kullanici_id]);
$uye = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$uye) {
    echo "Üye bilgisi bulunamadı."; exit;
}

$uye_id = $uye['id'];
$ad_soyad = $uye['ad'] . ' ' . $uye['soyad'];

$aylar = ['01'=>'Ocak', '02'=>'Şubat', '03'=>'Mart', '04'=>'Nisan', '05'=>'Mayıs', '06'=>'Haziran',
          '07'=>'Temmuz', '08'=>'Ağustos', '09'=>'Eylül', '10'=>'Ekim', '11'=>'Kasım', '12'=>'Aralık'];

$yil = date('Y'); // Otomatik yıl alma
$baslangic = new DateTime("$yil-01-01");
$ayListesi = [];
for ($i = 0; $i < 12; $i++) {
    $ay = clone $baslangic;
    $ay->modify("+$i month");
    $ayListesi[] = $ay;
}
?>

<h1 style="color:#f57235;">Yıllık Ödeme Takvimi</h1>
<p style="font-weight: bold; margin-bottom: 15px;">Üye: <?= htmlspecialchars($ad_soyad) ?></p>

<table style="width: 100%; border-collapse: collapse; background-color: #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
  <thead>
    <tr style="background-color: #f57235; color: white;">
      <th style="padding: 12px;">Ay</th>
      <th>Durum</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($ayListesi as $ay): 
      $tarih = $ay->format('Y-m-01');
      $ay_adi = $aylar[$ay->format('m')] . ' ' . $ay->format('Y');

      $stmt = $conn->prepare("SELECT odeme_durumu FROM odemeler WHERE uye_id = ? AND odeme_tarihi = ?");
      $stmt->execute([$uye_id, $tarih]);
      $durum = $stmt->fetchColumn();

      if ($durum === 'yapildi') {
        $etiket = '<span style="padding:6px 12px; background:#28a745; color:white; border-radius:8px; font-weight:bold;">Ödendi</span>';
      } elseif ($durum === 'iptal') {
        $etiket = '<span style="padding:6px 12px; background:#dc3545; color:white; border-radius:8px; font-weight:bold;">İptal</span>';
      } else {
        $etiket = '<span style="padding:6px 12px; background:#ffc107; color:black; border-radius:8px; font-weight:bold;">Bekleniyor</span>';
      }
    ?>
    <tr style="text-align: center;">
      <td style="padding: 10px;"><?= $ay_adi ?></td>
      <td><?= $etiket ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

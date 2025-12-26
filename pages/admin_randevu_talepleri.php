<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: admin_giris.php");
    exit;
}

// Bekleyen randevu sayısı
$bekleyenSayisi = $conn->query("SELECT COUNT(*) FROM randevular WHERE durum = 'beklemede'")->fetchColumn();

// ONAYLA
if (isset($_GET['onayla'])) {
    $id = (int)$_GET['onayla'];

    $stmt = $conn->prepare("SELECT * FROM randevular WHERE id = ?");
    $stmt->execute([$id]);
    $randevu = $stmt->fetch();

    if ($randevu) {
        $stmt2 = $conn->prepare("SELECT id FROM egitmen_ders_saatleri WHERE egitmen_id = ? AND gun = ? AND saat = ?");
        $stmt2->execute([$randevu['egitmen_id'], $randevu['gun'], $randevu['saat']]);
        $ders = $stmt2->fetch();

        if (!$ders) {
            $stmt3 = $conn->prepare("INSERT INTO egitmen_ders_saatleri (egitmen_id, gun, saat, ders_tipi) VALUES (?, ?, ?, ?)");
            $stmt3->execute([$randevu['egitmen_id'], $randevu['gun'], $randevu['saat'], $randevu['ders_tipi']]);
            $ders_id = $conn->lastInsertId();
        } else {
            $ders_id = $ders['id'];
        }

        $stmt4 = $conn->prepare("SELECT COUNT(*) FROM kayitlar WHERE uye_id = ? AND egitmen_ders_saati_id = ?");
        $stmt4->execute([$randevu['uye_id'], $ders_id]);
        if ($stmt4->fetchColumn() == 0) {
            $conn->prepare("INSERT INTO kayitlar (uye_id, egitmen_ders_saati_id) VALUES (?, ?)")->execute([$randevu['uye_id'], $ders_id]);
        }

        $conn->prepare("UPDATE randevular SET durum = 'onaylandi' WHERE id = ?")->execute([$id]);
    }

    header("Location: admin_randevu_talepleri.php");
    exit;
}

if (isset($_GET['reddet'])) {
    $id = (int)$_GET['reddet'];
    $conn->prepare("UPDATE randevular SET durum = 'iptal', iptal_edildi_by = 'admin' WHERE id = ?")->execute([$id]);
    header("Location: admin_randevu_talepleri.php");
    exit;
}

$stmt = $conn->query("SELECT r.id, r.tarih, r.saat, r.ders_tipi, r.durum, r.iptal_edildi_by, r.gun,
                             u.ad AS uye_ad, u.soyad AS uye_soyad,
                             e.ad AS egitmen_ad, e.soyad AS egitmen_soyad
                      FROM randevular r
                      JOIN uyeler u ON r.uye_id = u.id
                      JOIN egitmenler e ON r.egitmen_id = e.id
                      ORDER BY r.tarih DESC, r.saat DESC");
$randevular = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Randevu Talepleri</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 30px; background: #f9f9f9; }
    h2 { color: #f57235; }
    table { width: 100%; border-collapse: collapse; background: white; margin-top: 30px; }
    th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
    th { background: #f57235; color: white; }
    tr:nth-child(even) { background: #f1f1f1; }
    .btn { padding: 6px 10px; border: none; border-radius: 4px; color: white; cursor: pointer; text-decoration: none; }
    .onayla { background-color: #28a745; }
    .reddet { background-color: #dc3545; }
  </style>
</head>
<body>

  <h2>Randevu Talepleri <?php if ($bekleyenSayisi > 0): ?><span style="color:white; background:#dc3545; padding:4px 8px; border-radius:10px; font-size:12px; margin-left:10px;"><?= $bekleyenSayisi ?></span><?php endif; ?></h2>

  <?php if (count($randevular) === 0): ?>
    <p>Henüz randevu talebi yok.</p>
  <?php else: ?>
    <table>
      <tr>
        <th>Üye</th>
        <th>Eğitmen</th>
        <th>Gün</th>
        <th>Tarih</th>
        <th>Saat</th>
        <th>Durum</th>
        <th>İşlem</th>
      </tr>
      <?php foreach ($randevular as $r): ?>
        <tr>
          <td><?= htmlspecialchars($r['uye_ad'] . ' ' . $r['uye_soyad']) ?></td>
          <td><?= htmlspecialchars($r['egitmen_ad'] . ' ' . $r['egitmen_soyad']) ?></td>
          <td><?= htmlspecialchars($r['gun']) ?></td>
          <td><?= htmlspecialchars($r['tarih']) ?></td>
          <td><?= htmlspecialchars($r['saat']) ?></td>
          <td>
            <?php
              if ($r['durum'] === 'reddedildi' || $r['durum'] === 'iptal') {
                  if ($r['iptal_edildi_by'] === 'admin') {
                      echo "<span style='color:red;'>Admin tarafından<br>iptal edildi</span>";
                  } elseif ($r['iptal_edildi_by'] === 'uye') {
                      echo "<span style='color:red;'>Üye tarafından<br>iptal edildi</span>";
                  } else {
                      echo "<span style='color:red;'>İptal edildi</span>";
                  }
              } else {
                  echo ucfirst($r['durum']);
              }
            ?>
          </td>
          <td>
            <?php if ($r['durum'] === 'beklemede'): ?>
              <a class="btn onayla" href="?onayla=<?= $r['id'] ?>">Onayla</a>
              <a class="btn reddet" href="?reddet=<?= $r['id'] ?>" onclick="return confirm('Reddetmek istediğinize emin misiniz?')">Reddet</a>
            <?php else: ?>
              -
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>

</body>
</html>

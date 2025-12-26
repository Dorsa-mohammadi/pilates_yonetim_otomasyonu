<?php
session_start();
require_once '../config.php';

// Giriş kontrolü
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    echo "Yetkisiz erişim.";
    exit;
}

// ONAYLA işlemi
if (isset($_POST['onayla'])) {
    $uye_id = (int)$_POST['onayla'];
    $conn->prepare("UPDATE uyeler SET onay = 1 WHERE id = ?")->execute([$uye_id]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// SİL işlemi
if (isset($_POST['sil'])) {
    $uye_id = (int)$_POST['sil'];
    $stmt = $conn->prepare("SELECT kullanici_id FROM uyeler WHERE id = ?");
    $stmt->execute([$uye_id]);
    $k = $stmt->fetch();

    if ($k) {
        $conn->prepare("DELETE FROM uyeler WHERE id = ?")->execute([$uye_id]);
        $conn->prepare("DELETE FROM kullanicilar WHERE id = ?")->execute([$k['kullanici_id']]);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Üyeleri çek
$uyeler = $conn->query("
    SELECT u.*, k.eposta 
    FROM uyeler u 
    JOIN kullanicilar k ON u.kullanici_id = k.id 
    ORDER BY u.onay ASC
")->fetchAll();

?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Üyelik Talepleri</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f2f2f2; padding: 20px; }
    table { width: 100%; border-collapse: collapse; background: white; margin-top: 20px; }
    th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
    th { background: #f57235; color: white; }
    tr:nth-child(even) { background: #f9f9f9; }
    .btn {
      padding: 6px 12px;
      font-size: 13px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      color: white;
    }
    .onayla { background-color: #28a745; }
    .sil { background-color: #dc3545; }
    form { display: inline; }
  </style>
</head>
<body>

<h2>Üyelik Talepleri</h2>

<?php if (empty($uyeler)): ?>
  <p>Henüz başvuru yok.</p>
<?php else: ?>
  <table>
    <tr>
      <th>Ad Soyad</th>
      <th>E-posta</th>
      <th>Durum</th>
      <th>İşlem</th>
    </tr>
    <?php foreach ($uyeler as $uye): ?>
      <tr>
        <td><?= htmlspecialchars($uye['ad'] . ' ' . $uye['soyad']) ?></td>
        <td><?= htmlspecialchars($uye['eposta']) ?></td>
        <td><?= $uye['onay'] ? 'Onaylı' : 'Bekliyor' ?></td>
        <td>
          <?php if (!$uye['onay']): ?>
            <form method="post" style="display:inline;">
              <button type="submit" name="onayla" value="<?= $uye['id'] ?>" class="btn onayla">Onayla</button>
            </form>
          <?php endif; ?>
          <form method="post" style="display:inline;" onsubmit="return confirm('Bu üyeyi silmek istediğinize emin misiniz?');">
            <button type="submit" name="sil" value="<?= $uye['id'] ?>" class="btn sil">Sil</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>

</body>
</html>


<?php
session_start();
require_once '../config.php';

// Giriş yapılmamışsa veya yönetici değilse çık
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'yonetici') {
    header('Location: ../index.php');
    exit;
}

// Onay işlemi
if (isset($_GET['onayla'])) {
    $uye_id = $_GET['onayla'];
    $stmt = $conn->prepare("UPDATE uyeler SET onay = 1 WHERE id = ?");
    $stmt->execute([$uye_id]);
    header("Location: uye_onayla.php");
    exit;
}

// Bekleyen üyeleri al
$stmt = $conn->query("SELECT u.id, u.ad, u.soyad, u.eposta FROM uyeler u WHERE u.onay = 0");
$bekleyen_uyeler = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Üye Onay Paneli</title>
</head>
<body>
  <h2>Bekleyen Üyeler</h2>
  <?php if (count($bekleyen_uyeler) > 0): ?>
    <ul>
      <?php foreach ($bekleyen_uyeler as $uye): ?>
        <li>
          <?= $uye['ad'] ?> <?= $uye['soyad'] ?> - <?= $uye['eposta'] ?>
          <a href="?onayla=<?= $uye['id'] ?>">Onayla</a>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p>Bekleyen üye yok.</p>
  <?php endif; ?>
</body>
</html>


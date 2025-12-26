<?php
require_once '../config.php';
session_start();

if ($_SESSION['rol'] !== 'admin') {
    header("Location: admin_giris.php");
    exit;
}

// Verileri anket bazında grupla
$sonuclar = $conn->query("
    SELECT a.id as anket_id, a.aciklama, s.soru_metni, c.cevap, COUNT(*) as adet
    FROM anket_cevaplari c
    JOIN anket_sorulari s ON s.id = c.soru_id
    JOIN anketler a ON a.id = s.anket_id
    GROUP BY a.id, s.id, c.cevap
    ORDER BY a.id DESC, s.id ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Verileri grupla
$gruplu = [];
foreach ($sonuclar as $row) {
    $aid = $row['anket_id'];
    $q = $row['soru_metni'];
    $cevap = $row['cevap'];

    $gruplu[$aid]['aciklama'] = $row['aciklama'];
    $gruplu[$aid]['sorular'][$q][$cevap] = $row['adet'];
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Anket Sonuçları</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f7f7f7;
      padding: 20px;
      font-size: 14px;
    }
    .anket {
      background: #fff;
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 25px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .anket h3 {
      margin: 0 0 10px;
      color: #333;
      font-size: 16px;
    }
    .anket .soru {
      margin: 12px 0;
    }
    .anket .soru strong {
      color: #444;
      font-size: 14px;
    }
    .anket .cevaplar {
      padding-left: 20px;
      color: #555;
    }
  </style>
</head>
<body>

<h2>Anket Cevapları</h2>

<?php if (empty($gruplu)): ?>
    <p>Henüz cevaplanmış bir anket bulunmamaktadır.</p>
<?php else: ?>
    <?php foreach ($gruplu as $anket): ?>
        <div class="anket">
            <h3><?= htmlspecialchars($anket['aciklama']) ?></h3>
            <?php foreach ($anket['sorular'] as $soru => $cevaplar): ?>
                <div class="soru">
                    <strong><?= htmlspecialchars($soru) ?></strong>
                    <div class="cevaplar">
                        <?php foreach ($cevaplar as $secim => $adet): ?>
                            <?= htmlspecialchars($secim) ?>: <?= $adet ?><br>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>

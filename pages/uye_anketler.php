<?php
$uye_id = $_SESSION['kullanici_id'];
$mesaj = "";

// Cevap gönderildiyse işle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $soru_ids = $_POST['soru_id'] ?? [];
    if (!is_array($soru_ids)) $soru_ids = [$soru_ids];

    $stmt = $conn->prepare("INSERT INTO anket_cevaplari (uye_id, soru_id, cevap) VALUES (?, ?, ?)");

    foreach ($soru_ids as $soru_id) {
        $cevap_key = "cevap_" . $soru_id;
        if (isset($_POST[$cevap_key])) {
            $cevap = $_POST[$cevap_key];
            $stmt->execute([$uye_id, $soru_id, $cevap]);
        }
    }

    $mesaj = "Anket cevaplarınız başarıyla gönderildi.";
}

// Sadece cevaplanmamış soruları getir
$sorular = $conn->query("
    SELECT s.id AS soru_id, s.soru_metni
    FROM anket_sorulari s
    WHERE s.id NOT IN (
        SELECT soru_id FROM anket_cevaplari WHERE uye_id = $uye_id
    )
")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2 style="color:#f57235; margin-bottom:20px;">Cevaplamadığınız Anketler</h2>

<?php if (!empty($mesaj)): ?>
  <div style="background-color: #d4edda; color: #155724; padding: 12px; border-radius: 6px; margin-bottom: 15px;">
    <?= htmlspecialchars($mesaj) ?>
  </div>
<?php endif; ?>

<?php if (count($sorular) > 0): ?>
  <form method="post">
    <?php foreach ($sorular as $soru): ?>
      <div style="background: white; padding: 15px; margin-bottom: 10px; border-radius: 8px; box-shadow: 0 0 5px rgba(0,0,0,0.1);">
        <p><strong><?= htmlspecialchars($soru['soru_metni']) ?></strong></p>
        <input type="hidden" name="soru_id[]" value="<?= $soru['soru_id'] ?>">
        <label><input type="radio" name="cevap_<?= $soru['soru_id'] ?>" value="Evet" required> Evet</label><br>
        <label><input type="radio" name="cevap_<?= $soru['soru_id'] ?>" value="Hayır"> Hayır</label><br>
        <label><input type="radio" name="cevap_<?= $soru['soru_id'] ?>" value="Kararsızım"> Kararsızım</label>
      </div>
    <?php endforeach; ?>
    <button type="submit" style="padding: 10px 20px; background: #f57235; color: white; border: none; border-radius: 6px; cursor: pointer;">Cevapları Gönder</button>
  </form>
<?php else: ?>
  <p>Şu anda cevaplamanız gereken anket yok.</p>
<?php endif;?>

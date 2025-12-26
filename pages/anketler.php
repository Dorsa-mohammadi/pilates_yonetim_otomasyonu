<?php
require_once 'config.php';
session_start();

$uye_id = $_SESSION['kullanici_id'];
// Kullanıcının cevaplamadığı anketlerin soruları
$sorular = $conn->query("
    SELECT s.id AS soru_id, s.soru_metni, a.id AS anket_id
    FROM anket_sorulari s
    INNER JOIN anketler a ON a.id = s.anket_id
    WHERE s.id NOT IN (
        SELECT soru_id FROM anket_cevaplari WHERE uye_id = $uye_id
    )
")->fetchAll(PDO::FETCH_ASSOC);
?>

<form action="anket_cevapla.php" method="post">
<?php foreach ($sorular as $soru): ?>
    <p><strong><?= htmlspecialchars($soru['soru_metni']) ?></strong></p>
    <input type="hidden" name="soru_id[]" value="<?= $soru['soru_id'] ?>">
    <input type="radio" name="cevap_<?= $soru['soru_id'] ?>" value="Evet" required> Evet
    <input type="radio" name="cevap_<?= $soru['soru_id'] ?>" value="Hayır"> Hayır
    <input type="radio" name="cevap_<?= $soru['soru_id'] ?>" value="Kararsızım"> Kararsızım
    <hr>
<?php endforeach; ?>
    <button type="submit">Gönder</button>
</form>

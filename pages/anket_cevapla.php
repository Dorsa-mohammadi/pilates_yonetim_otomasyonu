<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../config.php';
session_start();

$uye_id = $_SESSION['kullanici_id'] ?? null;
if (!$uye_id) die("Giriş yapmanız gerekiyor.");

$soru_ids_raw = $_POST['soru_id'] ?? [];
$soru_ids = is_array($soru_ids_raw) ? $soru_ids_raw : [$soru_ids_raw]; // HATA BURADA ENGELLENDİ

if (!empty($soru_ids)) {
    $stmt = $conn->prepare("INSERT INTO anket_cevaplari (uye_id, soru_id, cevap) VALUES (?, ?, ?)");

    foreach ($soru_ids as $soru_id) {
        $cevap_key = "cevap_" . $soru_id;
        if (isset($_POST[$cevap_key])) {
            $cevap = $_POST[$cevap_key];
            $stmt->execute([$uye_id, $soru_id, $cevap]);
        }
    }

    echo "<p style='color:green'>Cevaplar başarıyla kaydedildi.</p>";
} else {
    echo "<p style='color:red'>Hiçbir soru alınamadı.</p>";
}
?>

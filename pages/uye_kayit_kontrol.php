<?php
session_start();
require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ad = $_POST['ad'];
    $soyad = $_POST['soyad'];
    $eposta = $_POST['eposta'];
    $sifre = password_hash($_POST['sifre'], PASSWORD_DEFAULT);
    $telefon = $_POST['telefon'];
    $kan_grubu = $_POST['kan_grubu'];
    $saglik_durumu = $_POST['saglik_durumu'];

    try {
        // 1. Kullanıcıyı sadece eposta, sifre ve rol ile ekle
        $stmt = $conn->prepare("INSERT INTO kullanicilar (eposta, sifre, rol) VALUES (?, ?, 'uye')");
        $stmt->execute([$eposta, $sifre]);

        $kullanici_id = $conn->lastInsertId();

        // 2. Üyeyi ekle (ad, soyad vs. burada)
        $stmt = $conn->prepare("INSERT INTO uyeler (kullanici_id, ad, soyad, telefon, kan_grubu, saglik_durumu) 
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$kullanici_id, $ad, $soyad, $telefon, $kan_grubu, $saglik_durumu]);

        echo "<script>alert('Kayıt başarılı! Yönetici onayından sonra giriş yapabilirsiniz.'); window.location.href = '../index.php';</script>";
        exit;

    } catch (PDOException $e) {
        echo "Hata: " . $e->getMessage();
        exit;
    }
}
?>

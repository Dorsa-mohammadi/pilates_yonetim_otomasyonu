<?php
session_start();
require_once 'config.php'; // includes/db.php zaten burada çağrılıyor

// Yetki kontrolü
if (!isset($_SESSION['kullanici_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: pages/giris.php?tur=yonetici");
    exit;
}
?>

<h2>Yönetici Paneli</h2>

<!-- Menü -->
<ul>
    <li><a href="admin.php">Eğitmen Ekle</a></li>
    <li><a href="admin_ders_programi.php">Ders Programı Oluştur</a></li>
    <li><a href="admin_duyuru.php">Duyuru Yayınla</a></li>
    <li><a href="admin_mesaj.php">Mesaj Gönder</a></li>
    <li><a href="admin_anket.php">Anket Oluştur</a></li>
    <li><a href="pages/cikis.php">Çıkış Yap</a></li>
    <li><a href="admin_randevu_talepleri.php">Randevu Talepleri</a></li>
</ul>

<hr>

<!-- Eğitmen Ekleme Formu -->
<h3>Yeni Eğitmen Ekle</h3>
<form method="post">
    Ad: <input type="text" name="ad" required><br>
    Soyad: <input type="text" name="soyad" required><br>
    E-Posta: <input type="email" name="eposta" required><br>
    Şifre: <input type="password" name="sifre" required><br>
    Telefon: <input type="text" name="telefon"><br>
    <input type="submit" name="egitmen_ekle" value="Kaydet">
</form>

<?php
if (isset($_POST['egitmen_ekle'])) {
    $eposta = $_POST['eposta'];
    $sifre = $_POST['sifre'];
    $ad = $_POST['ad'];
    $soyad = $_POST['soyad'];
    $telefon = $_POST['telefon'];

    $conn->beginTransaction();
    try {
        // Kullanıcı olarak ekle
        $stmt = $conn->prepare("INSERT INTO kullanicilar (eposta, sifre, rol) VALUES (?, ?, 'egitmen')");
        $stmt->execute([$eposta, $sifre]);
        $kullanici_id = $conn->lastInsertId();

        // Eğitmen detay kaydı
        $stmt = $conn->prepare("INSERT INTO egitmenler (kullanici_id, ad, soyad, telefon, eposta) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$kullanici_id, $ad, $soyad, $telefon, $eposta]);

        $conn->commit();
        echo "<p style='color:green;'>Eğitmen başarıyla eklendi.</p>";
    } catch (Exception $e) {
        $conn->rollBack();
        echo "<p style='color:red;'>Hata: " . $e->getMessage() . "</p>";
}
}
?>


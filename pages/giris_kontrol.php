<?php
session_start();
require_once '../config.php';

$eposta = trim($_POST['eposta'] ?? '');
$sifre  = $_POST['sifre'] ?? '';
$tur    = $_POST['tur'] ?? '';

if (!$eposta || !$sifre || !$tur) {
    echo "Eksik bilgi gönderildi.";
    exit;
}

// Her zaman sifre kontrolü KULLANICILAR tablosundan yapılır
$query = $conn->prepare("SELECT * FROM kullanicilar WHERE eposta = ? AND rol = ?");
$query->execute([$eposta, $tur]);
$kullanici = $query->fetch(PDO::FETCH_ASSOC);

if (!$kullanici) {
    echo "E-posta kayıtlı değil veya rol uyuşmuyor.";
    exit;
}

// Şifre eksikse (örneğin admin ekledi ama henüz belirlenmedi)
if (!$kullanici['sifre']) {
    echo "Şifre belirlenmemiş. Lütfen e-posta ile gelen bağlantıdan şifre oluşturun.";
    exit;
}

// Şifre yanlışsa
if (!password_verify($sifre, $kullanici['sifre'])) {
    echo "Şifre hatalı.";
    exit;
}

// Eğer kullanıcı üyeyse, uyeler tablosundan onay kontrolü yap
if ($tur === 'uye') {
    $stmt = $conn->prepare("SELECT onay FROM uyeler WHERE kullanici_id = ?");
    $stmt->execute([$kullanici['id']]);
    $uye = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$uye || $uye['onay'] != 1) {
        echo "Hesabınız henüz yönetici tarafından onaylanmadı.";
        exit;
    }
}

// Giriş başarılı → oturumu başlat
$_SESSION['kullanici_id'] = $kullanici['id'];
$_SESSION['eposta'] = $kullanici['eposta'];
$_SESSION['rol'] = $kullanici['rol'];

// Yönlendirme
if ($tur === 'admin') {
    header("Location: /pages/admin_panel.php");
} else {
    header("Location: /uye_panel.php");
}
exit;

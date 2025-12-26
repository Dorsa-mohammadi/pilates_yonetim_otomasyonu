<?php
/**
 * ÖRNEK CONFIG DOSYASI
 *
 * Bu dosya GitHub reposunda örnek olarak bulunur.
 * Kullanmak için:
 * 1) Bu dosyayı kopyalayın
 * 2) config.php olarak yeniden adlandırın
 * 3) Kendi ortam (local / canlı) bilgilerinizi girin
 *
 */

// ===============================
// Ortam Kontrolü
// ===============================
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

if (
    $host === 'localhost' ||
    str_contains($host, 'localhost') ||
    str_contains($host, '127.0.0.1')
) {
    // LOCALHOST AYARLARI
    $site_url = "http://localhost/PROJE_KLASOR_ADI/";

    $db_host = "localhost";
    $db_name = "local_veritabani_adi";
    $db_user = "local_kullanici";
    $db_pass = "";
} else {
    // CANLI SUNUCU AYARLARI
    $site_url = "https://siteadresiniz.com/";

    $db_host = "localhost";
    $db_name = "canli_veritabani_adi";
    $db_user = "canli_kullanici";
    $db_pass = "canli_sifre";
}

// ===============================
// Sabitler
// ===============================
define("SITE_URL", $site_url);
define("TITLE", "Pilates Stüdyo Yönetim Otomasyonu");
define("RESIMLER", $site_url . "assets/images/");
define("CSSLER", $site_url . "assets/css/");
define("JSLER", $site_url . "assets/js/");
define("TEMA", $site_url . "assets/tema/");

// ===============================
// Dosya Dahil Etmeleri
// ===============================
// Bu dosyalar projede mevcut olmalıdır
require_once 'includes/db.php';
require_once 'includes/fonksiyonlar.php';

// ===============================
// Yardımcı Sınıf
// ===============================
$FONK = new FONK();

// ===============================
// Veritabanı Bağlantısı
// ===============================
try {
    $conn = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8",
        $db_user,
        $db_pass
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası oluştu.");
}

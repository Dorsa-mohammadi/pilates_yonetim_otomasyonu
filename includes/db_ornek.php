<?php
/**
 * ÖRNEK VERİTABANI BAĞLANTI DOSYASI
 *
 * Bu dosya örnek olarak GitHub reposunda bulunur.
 * Kullanmak için:
 * 1) Bu dosyayı kopyalayın
 * 2) db.php olarak yeniden adlandırın
 * 3) Kendi veritabanı bilgilerinizi girin
 */

// ===============================
// Ortam Algılama
// ===============================
$host = $_SERVER['HTTP_HOST'] ?? '';

if (
    $host === 'localhost' ||
    str_contains($host, 'localhost') ||
    str_contains($host, '127.0.0.1')
) {
    // LOCALHOST AYARLARI
    $db_host = "localhost";
    $db_name = "local_veritabani_adi";
    $db_user = "local_kullanici";
    $db_pass = "";
} else {
    // CANLI SUNUCU AYARLARI
    $db_host = "localhost";
    $db_name = "canli_veritabani_adi";
    $db_user = "canli_kullanici";
    $db_pass = "canli_sifre";
}

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

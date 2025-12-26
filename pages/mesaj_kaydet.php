<?php
session_start();
require_once __DIR__ . '/../config.php';

$gonderen_id = $_SESSION['kullanici_id'] ?? 0;
$rol = $_SESSION['rol'] ?? '';
$mesaj = trim($_POST['mesaj'] ?? '');

if (!$gonderen_id || !$mesaj) {
    $yonlen = ($rol === 'admin') ? 'admin_mesaj.php?durum=eksik' : 'uye_panel.php?sayfa=mesajlar&durum=eksik';
    header("Location: $yonlen");
    exit;
}

if ($rol === 'admin') {
    // ADMİN: çoklu üyeye mesaj
    $alici_idler = $_POST['alici_id'] ?? [];

    if (!is_array($alici_idler) || empty($alici_idler)) {
        header("Location: admin_mesaj.php?durum=gecersiz");
        exit;
    }

    $gecerliAlıcılar = [];
    foreach ($alici_idler as $alici_id) {
        $kontrol = $conn->prepare("SELECT COUNT(*) FROM uyeler WHERE kullanici_id = ?");
        $kontrol->execute([$alici_id]);
        if ($kontrol->fetchColumn() > 0) {
            $gecerliAlıcılar[] = $alici_id;
        }
    }

    if (empty($gecerliAlıcılar)) {
        header("Location: admin_mesaj.php?durum=gecersiz");
        exit;
    }

    $kaydet = $conn->prepare("INSERT INTO mesajlar (gonderen_id, alici_id, mesaj, tarih) VALUES (?, ?, ?, NOW())");
    foreach ($gecerliAlıcılar as $alici_id) {
        $kaydet->execute([$gonderen_id, $alici_id, $mesaj]);
    }

    header("Location: admin_mesaj.php?durum=ok");
    exit;

} else {
    // ÜYE: tek admin'e mesaj
    $alici_id = $_POST['alici_id'] ?? '';

    if (!$alici_id) {
        header("Location: uye_panel.php?sayfa=mesajlar&durum=eksik");
        exit;
    }

    $aliciKontrol = $conn->prepare("SELECT COUNT(*) FROM kullanicilar WHERE id = ? AND rol = 'admin'");
    $aliciKontrol->execute([$alici_id]);

    if ($aliciKontrol->fetchColumn() == 0) {
        header("Location: uye_panel.php?sayfa=mesajlar&durum=gecersiz");
        exit;
    }

    $kaydet = $conn->prepare("INSERT INTO mesajlar (gonderen_id, alici_id, mesaj, tarih) VALUES (?, ?, ?, NOW())");
    $kaydet->execute([$gonderen_id, $alici_id, $mesaj]);

    header("Location: uye_panel.php?sayfa=mesajlar&durum=ok");
    exit;
}

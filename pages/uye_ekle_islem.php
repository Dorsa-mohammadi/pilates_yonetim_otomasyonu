<?php
require_once '../config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ad = $_POST['ad'];
    $soyad = $_POST['soyad'];
    $eposta = $_POST['eposta'];
    $kan_grubu = $_POST['kan_grubu'];
    $telefon = $_POST['telefon'];
    $saglik_durumu = $_POST['saglik_durumu'];

    $stmt = $conn->prepare("INSERT INTO uyeler (ad, soyad, eposta, kan_grubu, telefon, saglik_durumu) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$ad, $soyad, $eposta, $kan_grubu, $telefon, $saglik_durumu]);

    header("Location: admin_uyeler.php");
    exit;
}
?>


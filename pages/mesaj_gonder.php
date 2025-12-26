<?php
session_start();
require_once 'config.php';

// Giriş kontrolü
if (!isset($_SESSION['kullanici_id']) || !isset($_SESSION['rol'])) {
    header("Location: giris.php");
    exit;
}

$kullanici_id = $_SESSION['kullanici_id'];
$rol = $_SESSION['rol'];

// Alıcı listesi
if ($rol === 'uye') {
    $alicilar = $conn->query("SELECT id, ad FROM kullanicilar")->fetchAll(PDO::FETCH_ASSOC);
} else {
    $alicilar = $conn->query("SELECT id, ad FROM uyeler")->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Mesaj Gönder</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f2f2f2;
            padding: 40px;
        }
        .mesaj-kutu {
            max-width: 500px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .mesaj-kutu h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        select, textarea, button {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
        }
        button {
            background: #f57235;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background: #e35e1d;
        }
    </style>
</head>
<body>
    <div class="mesaj-kutu">
        <h2>Mesaj Gönder</h2>
        <form action="mesaj_kaydet.php" method="post">
            <label>Alıcı Seç</label>
            <select name="alici_id" required>
                <option value="">-- Alıcı --</option>
                <?php foreach ($alicilar as $alici): ?>
                    <option value="<?= $alici['id'] ?>"><?= $alici['ad'] ?></option>
                <?php endforeach; ?>
            </select>

            <label>Mesajınız</label>
            <textarea name="mesaj" rows="5" required placeholder="Mesajınızı yazın..."></textarea>

            <button type="submit">Gönder</button>
        </form>
    </div>
</body>
</html>

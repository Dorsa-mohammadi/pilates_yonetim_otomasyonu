<?php
require_once 'config.php';

$mesaj = '';
$basarili = false;

// Token parametresi var mı?
if (!isset($_GET['token'])) {
    die("Geçersiz bağlantı.");
}

$token = $_GET['token'];

// Token uyeler tablosunda var mı ve süresi geçmemiş mi?
$stmt = $conn->prepare("SELECT id, kullanici_id, token_sure FROM uyeler WHERE sifre_token = ?");
$stmt->execute([$token]);
$uye = $stmt->fetch();

if (!$uye || strtotime($uye['token_sure']) < time()) {
    die("Bağlantı geçersiz veya süresi dolmuş.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sifre = $_POST['sifre'];
    $sifre_tekrar = $_POST['sifre_tekrar'];

    if ($sifre !== $sifre_tekrar) {
        $mesaj = "Şifreler uyuşmuyor. Lütfen tekrar deneyin.";
    } else {
        $hash = password_hash($sifre, PASSWORD_DEFAULT);

        // Şifreyi hem uyeler hem kullanicilar tablosuna yaz
        $conn->prepare("UPDATE uyeler SET sifre = ?, sifre_token = NULL, token_sure = NULL WHERE id = ?")
             ->execute([$hash, $uye['id']]);

        $conn->prepare("UPDATE kullanicilar SET sifre = ?, sifre_token = NULL, token_sure = NULL WHERE id = ?")
             ->execute([$hash, $uye['kullanici_id']]);

        $mesaj = "Şifreniz başarıyla oluşturuldu. Giriş ekranına yönlendiriliyorsunuz...";
        $basarili = true;
        header("Location: /pages/uye_giris.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Şifre Oluştur</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background: #f2f2f2;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .form-box {
      background: white;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
      width: 350px;
      text-align: center;
    }
    h2 {
      color: #f57235;
      margin-bottom: 20px;
    }
    input[type="password"] {
      width: 100%;
      padding: 12px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }
    button {
      width: 100%;
      background-color: #f57235;
      color: white;
      border: none;
      padding: 10px;
      border-radius: 6px;
      font-size: 15px;
      cursor: pointer;
      margin-top: 10px;
    }
    .message {
      margin-top: 15px;
      padding: 12px;
      background-color: #e6ffed;
      border: 1px solid #28a745;
      color: #155724;
      border-radius: 6px;
      font-size: 14px;
    }
    .error {
      margin-top: 15px;
      padding: 12px;
      background-color: #ffe6e6;
      border: 1px solid #dc3545;
      color: #721c24;
      border-radius: 6px;
      font-size: 14px;
    }
  </style>
  <?php if ($basarili): ?>
  <meta http-equiv="refresh" content="3;URL=uye_giris.php">
  <?php endif; ?>
</head>
<body>
  <div class="form-box">
    <h2>Yeni Şifre Oluştur</h2>

    <?php if ($mesaj): ?>
      <div class="<?= $basarili ? 'message' : 'error' ?>"><?= $mesaj ?></div>
    <?php endif; ?>

    <?php if (!$basarili): ?>
    <form method="POST">
      <input type="password" name="sifre" placeholder="Yeni şifre" required>
      <input type="password" name="sifre_tekrar" placeholder="Yeni şifre (tekrar)" required>
      <button type="submit">Şifreyi Kaydet</button>
    </form>
    <?php endif; ?>
  </div>
</body>
</html>
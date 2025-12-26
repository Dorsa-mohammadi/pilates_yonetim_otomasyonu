<?php
require_once '../config.php';
$token = $_GET['token'] ?? '';
$gecerli_token = false;
$mesaj = '';

if ($token) {
    $stmt = $conn->prepare("SELECT id FROM kullanicilar WHERE sifre_token = ? AND token_sure >= NOW() - INTERVAL 1 DAY");
    $stmt->execute([$token]);
    $kullanici = $stmt->fetch();s

    if ($kullanici) {
        $gecerli_token = true;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'])) {
    $token = $_POST['token'];
    $sifre = $_POST['sifre'];
    $sifre_tekrar = $_POST['sifre_tekrar'];

    if ($sifre !== $sifre_tekrar) {
        $mesaj = "Şifreler uyuşmuyor.";
        $gecerli_token = true;
    } else {
        $stmt = $conn->prepare("SELECT id FROM kullanicilar WHERE sifre_token = ? AND token_sure >= NOW() - INTERVAL 1 DAY");
        $stmt->execute([$token]);
        $kullanici = $stmt->fetch();

        if ($kullanici) {
            $hash = password_hash($sifre, PASSWORD_DEFAULT);
            $conn->prepare("UPDATE kullanicilar SET sifre = ?, sifre_token = NULL, token_sure = NULL WHERE id = ?")
                  ->execute([$hash, $kullanici['id']]);
            $mesaj = "Şifreniz başarıyla güncellendi. <a href='uye_giris.php'>Giriş Yap</a>";
        } else {
            $mesaj = "Token geçersiz veya süresi dolmuş.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Şifre Yenile</title>
  <style>
    body { margin: 0; font-family: Arial; background: #f4f4f4; height: 100vh; display: flex; justify-content: center; align-items: center; }
    .modal {
      position: relative;
      width: 100%;
      max-width: 400px;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
      animation: fadeIn 0.3s ease-in-out;
    }
    .close-btn {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 20px;
      font-weight: bold;
      cursor: pointer;
    }
    input {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    button {
      width: 100%;
      padding: 10px;
      background-color: #f57235;
      border: none;
      color: white;
      font-weight: bold;
      border-radius: 6px;
      cursor: pointer;
    }
    .message {
      margin-top: 10px;
      text-align: center;
      font-size: 14px;
      color: green;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.95); }
      to { opacity: 1; transform: scale(1); }
    }
  </style>
</head>
<body>

<div class="modal">
  <span class="close-btn" onclick="window.location.href='uye_giris.php'">&times;</span>
  <h2 style="text-align:center;">Şifre Yenile</h2>

  <?php if (!empty($mesaj)): ?>
    <p class="message"><?= $mesaj ?></p>
  <?php endif; ?>

  <?php if ($gecerli_token): ?>
    <form method="POST">
      <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
      <input type="password" name="sifre" placeholder="Yeni Şifre" required>
      <input type="password" name="sifre_tekrar" placeholder="Yeni Şifre (Tekrar)" required>
      <button type="submit">Şifreyi Güncelle</button>
    </form>
  <?php elseif (!$mesaj): ?>
    <p class="message" style="color:red;">Bağlantı geçersiz veya süresi dolmuş.</p>
  <?php endif; ?>
</div>

</body>
</html>

<?php
require_once '../config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';
require_once '../PHPMailer/src/Exception.php';

$mesaj = '';
$renk = 'green';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eposta = $_POST['eposta'];

    // 1. Kullanıcı bilgisi al
    $stmt = $conn->prepare("SELECT id FROM kullanicilar WHERE eposta = ?");
    $stmt->execute([$eposta]);
    $kullanici = $stmt->fetch();

    if ($kullanici) {
        $kullanici_id = $kullanici['id'];
        $token = bin2hex(random_bytes(32));
        $token_sure = date("Y-m-d H:i:s", strtotime("+1 hour"));

        $link = "https://perapilatesstudio.com/sifre_olustur.php?token=$token";

        // 2. kullanicilar tablosuna yaz
        $conn->prepare("UPDATE kullanicilar SET sifre_token = ?, token_sure = ? WHERE id = ?")
              ->execute([$token, $token_sure, $kullanici_id]);

        // 3. uyeler tablosuna da yaz (eşleşen kullanici_id ile)
        $conn->prepare("UPDATE uyeler SET sifre_token = ?, token_sure = ? WHERE kullanici_id = ?")
              ->execute([$token, $token_sure, $kullanici_id]);

        // 4. Mail gönder
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'perapilates24@gmail.com';
        $mail->Password = 'uqcv fyro dhdk giqb';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->CharSet = 'UTF-8';
        $mail->setFrom('perapilates24@gmail.com', 'Pera Pilates');
        $mail->addAddress($eposta);
        $mail->isHTML(true);
        $mail->Subject = 'Şifre Sıfırlama';
        $mail->Body = "
            Merhaba,<br><br>
            Şifrenizi sıfırlamak için aşağıdaki bağlantıya tıklayın:<br><br>
            <a href='$link'>$link</a><br><br>
            Bu bağlantı 1 saat geçerlidir.<br><br>
            Sevgiler,<br>Pera Pilates Ekibi
        ";

        if ($mail->send()) {
            $mesaj = "E-posta gönderildi! Gelen kutunuzu kontrol edin.";
        } else {
            $mesaj = "E-posta gönderilemedi. Lütfen tekrar deneyin.";
            $renk = 'red';
        }
    } else {
        $mesaj = "Bu e-posta sistemde kayıtlı değil.";
        $renk = 'red';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Şifre Sıfırlama</title>
  <style>
    body {
      font-family: Arial;
      background: #f4f4f4;
      margin: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .box {
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      text-align: center;
      width: 350px;
    }
    .success {
      color: green;
      font-weight: bold;
    }
    .error {
      color: red;
      font-weight: bold;
    }
    a {
      color: #f57235;
      display: inline-block;
      margin-top: 15px;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="box">
    <h2>Şifre Sıfırlama</h2>
    <p class="<?= $renk === 'red' ? 'error' : 'success' ?>"><?= $mesaj ?></p>
    <a href="uye_giris.php">Giriş Sayfasına Dön</a>
  </div>
</body>
</html>

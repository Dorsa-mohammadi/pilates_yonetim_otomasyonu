<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: /pages/admin_giris.php");
    exit;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';
require_once '../PHPMailer/src/Exception.php';

// Sil
if (isset($_GET['sil'])) {
    $uye_id = intval($_GET['sil']);
    $stmt = $conn->prepare("SELECT kullanici_id FROM uyeler WHERE id = ?");
    $stmt->execute([$uye_id]);
    $kullanici = $stmt->fetch();
    if ($kullanici) {
        $conn->prepare("DELETE FROM uyeler WHERE id = ?")->execute([$uye_id]);
        $conn->prepare("DELETE FROM kullanicilar WHERE id = ?")->execute([$kullanici['kullanici_id']]);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Güncelle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guncelle_id'])) {
    $conn->prepare("UPDATE uyeler SET ad=?, soyad=?, eposta=?, kan_grubu=?, telefon=?, saglik_durumu=? WHERE id=?")
         ->execute([
            $_POST['ad'], $_POST['soyad'], $_POST['eposta'],
            $_POST['kan_grubu'], $_POST['telefon'], $_POST['saglik_durumu'],
            $_POST['guncelle_id']
         ]);

    // Ödeme güncelle
    $uye_id = $_POST['guncelle_id'];
    $odeme_durumu = $_POST['odeme_durumu'];
    $tarih = date('Y-m-01');

    $kontrol = $conn->prepare("SELECT id FROM odemeler WHERE uye_id = ? AND odeme_tarihi = ?");
    $kontrol->execute([$uye_id, $tarih]);
    $varmi = $kontrol->fetch();

    if ($varmi) {
        $conn->prepare("UPDATE odemeler SET odeme_durumu = ? WHERE id = ?")->execute([$odeme_durumu, $varmi['id']]);
    } else {
        $conn->prepare("INSERT INTO odemeler (uye_id, tutar, odeme_tarihi, odeme_durumu) VALUES (?, 0, ?, ?)")
             ->execute([$uye_id, $tarih, $odeme_durumu]);
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Yeni Üye Ekle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ekle'])) {
    $ad = $_POST['ad'];
    $soyad = $_POST['soyad'];
    $eposta = $_POST['eposta'];
    $kan = $_POST['kan_grubu'];
    $telefon = $_POST['telefon'];
    $saglik = $_POST['saglik_durumu'];

    $kontrol = $conn->prepare("SELECT COUNT(*) FROM kullanicilar WHERE eposta = ?");
    $kontrol->execute([$eposta]);
    if ($kontrol->fetchColumn() > 0) {
        echo "<script>alert('Bu e-posta adresiyle zaten bir kullanıcı kayıtlı.'); window.location.href = 'admin_uyeler.php';</script>";
        exit;
    }

    $token = bin2hex(random_bytes(32));
    $token_sure = date('Y-m-d H:i:s', strtotime('+1 hour'));

    try {
        $conn->beginTransaction();
        $stmt1 = $conn->prepare("INSERT INTO kullanicilar (eposta, sifre, rol) VALUES (?, NULL, 'uye')");
        $stmt1->execute([$eposta]);
        $kullanici_id = $conn->lastInsertId();

        $stmt2 = $conn->prepare("INSERT INTO uyeler (kullanici_id, ad, soyad, eposta, kan_grubu, telefon, saglik_durumu, sifre_token, token_sure, onay) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt2->execute([$kullanici_id, $ad, $soyad, $eposta, $kan, $telefon, $saglik, $token, $token_sure]);
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollBack();
        echo "<script>alert('Hata: " . addslashes($e->getMessage()) . "');</script>";
        exit;
    }

    $link = "https://perapilatesstudio.com/sifre_olustur.php?token=$token";
    $konu = "Pera Pilates - Şifre Oluşturma Bağlantısı";
    $mesaj = "Merhaba $ad $soyad,<br><br>Pera Pilates'e kaydınız başarıyla yapılmıştır.<br>Şifrenizi oluşturmak için aşağıdaki bağlantıya tıklayın:<br><a href='$link'>$link</a><br><br>Not: Bu bağlantı 1 saat içinde geçerlidir.";

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
    $mail->addAddress($eposta, "$ad $soyad");
    $mail->isHTML(true);
    $mail->Subject = $konu;
    $mail->Body    = $mesaj;

    if ($mail->send()) {
        echo "<script>alert('Üye eklendi ve e-posta gönderildi.'); window.location.href = 'admin_uyeler.php';</script>";
    } else {
        echo "<script>alert('Üye eklendi ama e-posta gönderilemedi.');</script>";
    }
    exit;
}

// Üye + ödeme durumu
$uyeler = $conn->query("
  SELECT u.*, k.eposta,
  (SELECT odeme_durumu FROM odemeler WHERE uye_id = u.id AND odeme_tarihi = DATE_FORMAT(CURDATE(), '%Y-%m-01')) AS odeme_durumu
  FROM uyeler u
  JOIN kullanicilar k ON u.kullanici_id = k.id
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Üyeler</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body { font-family: Arial; background: #f5f5f5; margin: 0; }
    .container {
      max-width: 1100px;
      margin: 60px auto;
      background: white;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
      padding: 30px;
    }
    h2 { margin: 0 0 20px; font-size: 24px; color: #333; }
    .btn {
      background: #f57235; color: white; padding: 10px 18px;
      border: none; border-radius: 8px; font-weight: bold;
      cursor: pointer; float: right;
    }
    table {
      width: 100%; border-collapse: collapse; margin-top: 20px;
    }
    th, td {
      padding: 12px; border-bottom: 1px solid #ddd; text-align: center;
    }
    th { background-color: #f57235; color: white; }
    tr:hover { background: #f9f9f9; }
    .icon-btn { background: none; border: none; cursor: pointer; font-size: 16px; }
    .edit { color: #007bff; }
    .delete { color: #dc3545; }
    .badge {
      padding: 6px 10px; border-radius: 8px; font-weight: bold; color: white;
      display: inline-block; font-size: 13px;
    }
    .yesil { background-color: #28a745; }
    .sari  { background-color: #ffc107; color: #333; }
    .kirmizi { background-color: #dc3545; }

    .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
             background-color: rgba(0,0,0,0.5); z-index: 100; }
    .modal-content {
      background: white; width: 400px; margin: 10% auto; padding: 20px;
      border-radius: 12px; position: relative; box-shadow: 0 0 25px rgba(0,0,0,0.15);
    }
    .modal-content input, .modal-content select {
      width: calc(100% - 20px); margin: 10px 0; padding: 10px;
      border: 1px solid #ccc; border-radius: 8px;
    }
    .modal-content button {
      width: 100%; margin-top: 10px; padding: 10px;
      border: none; border-radius: 8px; background-color: #f57235; color: white;
      font-weight: bold;
    }
    .kapat {
      position: absolute; top: 10px; right: 15px;
      font-size: 22px; color: #888; cursor: pointer;
    }
  </style>
</head>

<body>

<div class="container">
  <h2>Üye Listesi <button class="btn" onclick="yeniUyeModalAc()">+ Yeni Üye</button></h2>
  <table>
    <tr>
      <th>Ad Soyad</th>
      <th>E-posta</th>
      <th>Kan Grubu</th>
      <th>Telefon</th>
      <th>Sağlık Durumu</th>
      <th>Ödeme Durumu</th>
      <th>İşlem</th>
    </tr>
    <?php foreach ($uyeler as $uye): ?>
    <tr>
      <td><?= htmlspecialchars($uye['ad'] . ' ' . $uye['soyad']) ?></td>
      <td><?= htmlspecialchars($uye['eposta']) ?></td>
      <td><?= htmlspecialchars($uye['kan_grubu']) ?></td>
      <td><?= htmlspecialchars($uye['telefon']) ?></td>
      <td><?= $uye['saglik_durumu'] ?: '-' ?></td>
      <td>
        <?php
          $durum = $uye['odeme_durumu'];
          if ($durum === 'yapildi') echo '<span class="badge yesil">Ödendi</span>';
          elseif ($durum === 'bekleniyor') echo '<span class="badge sari">Ödeme Bekleniyor</span>';
          elseif ($durum === 'iptal') echo '<span class="badge kirmizi">İptal Edildi</span>';
          else echo '-';
        ?>
      </td>
      <td>
        <button class="icon-btn edit" onclick='duzenleModalAc(<?= json_encode($uye) ?>)' title="Düzenle"><i class="fas fa-pen"></i></button>
        <a href="?sil=<?= $uye['id'] ?>" onclick="return confirm('Silmek istediğinize emin misiniz?')" class="icon-btn delete" title="Sil"><i class="fas fa-trash"></i></a>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>

<!-- GÜNCELLE MODAL -->
<div id="duzenleModal" class="modal">
  <div class="modal-content">
    <span class="kapat" onclick="modalKapat('duzenleModal')">&times;</span>
    <h3>Üye Güncelle</h3>
    <form method="post">
      <input type="hidden" name="guncelle_id" id="guncelle_id">
      <input name="ad" id="ad_input" placeholder="Ad" required>
      <input name="soyad" id="soyad_input" placeholder="Soyad" required>
      <input name="eposta" id="eposta_input" placeholder="E-posta" required>
      <input name="kan_grubu" id="kan_input" placeholder="Kan Grubu">
      <input name="telefon" id="tel_input" placeholder="Telefon" required>
      <input name="saglik_durumu" id="saglik_input" placeholder="Sağlık Durumu">
      <label for="odeme_input">Ödeme Durumu</label>
      <select name="odeme_durumu" id="odeme_input" required>
        <option value="yapildi">Ödendi</option>
        <option value="bekleniyor">Ödeme Bekleniyor</option>
        <option value="iptal">İptal Edildi</option>
      </select>
      <button type="submit">Güncelle</button>
    </form>
  </div>
</div>

<script>
function modalKapat(id) {
  document.getElementById(id).style.display = "none";
}
function yeniUyeModalAc() {
  document.getElementById("ekleModal").style.display = "block";
}
function duzenleModalAc(uye) {
  document.getElementById("duzenleModal").style.display = "block";
  document.getElementById("guncelle_id").value = uye.id;
  document.getElementById("ad_input").value = uye.ad;
  document.getElementById("soyad_input").value = uye.soyad;
  document.getElementById("eposta_input").value = uye.eposta;
  document.getElementById("kan_input").value = uye.kan_grubu;
  document.getElementById("tel_input").value = uye.telefon;
  document.getElementById("saglik_input").value = uye.saglik_durumu;
  document.getElementById("odeme_input").value = uye.odeme_durumu ?? 'bekleniyor';
}
</script>
<!-- EKLE MODAL -->
<div id="ekleModal" class="modal">
  <div class="modal-content">
    <span class="kapat" onclick="modalKapat('ekleModal')">&times;</span>
    <h3>Yeni Üye Ekle</h3>
    <form method="post">
      <input type="hidden" name="ekle" value="1">
      <input name="ad" placeholder="Ad" required>
      <input name="soyad" placeholder="Soyad" required>
      <input name="eposta" placeholder="E-posta" required>
      <input name="kan_grubu" placeholder="Kan Grubu">
      <input name="telefon" placeholder="Telefon" required>
      <input name="saglik_durumu" placeholder="Sağlık Durumu">
      <button type="submit">Kaydet</button>
    </form>
  </div>
</div>
</body>
</html>

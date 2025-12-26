<?php
require_once 'config.php';

$FONK->oturumKontrol('uye');

$kullanici_id = $_SESSION['kullanici_id'];
$stmt = $conn->prepare("SELECT * FROM uyeler WHERE kullanici_id = ?");
$stmt->execute([$kullanici_id]);
$uye = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $conn->prepare("UPDATE uyeler SET ad = ?, soyad = ?, kan_grubu = ?, telefon = ?, eposta = ?, saglik_durumu = ? WHERE kullanici_id = ?");
    $stmt->execute([
        $_POST['ad'], $_POST['soyad'], $_POST['kan_grubu'],
        $_POST['telefon'], $_POST['eposta'], $_POST['saglik_durumu'],
        $kullanici_id
    ]);
    header("Location: uye_panel.php?sayfa=bilgilerim&guncellendi=1");
    exit;
}
?>

<style>
  .kutucuk {
    max-width: 600px;
    margin: auto;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    padding: 30px;
    margin-top: 30px;
  }
  h2 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
    font-size: 24px;
  }
  label {
    display: block;
    margin-top: 15px;
    font-weight: bold;
    color: #555;
  }
  input, textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    margin-top: 5px;
  }
  button {
    background: #f57235;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 6px;
    font-size: 16px;
    margin-top: 20px;
    cursor: pointer;
    width: 100%;
  }
  button i {
    margin-right: 5px;
  }
  .success {
    background: #d4edda;
    padding: 10px;
    color: #155724;
    border: 1px solid #c3e6cb;
    border-radius: 6px;
    margin-bottom: 15px;
    text-align: center;
  }
</style>

<div class="kutucuk">
  <h2>Bilgilerim</h2>

  <?php if (isset($_GET['guncellendi'])): ?>
    <div class="success">Bilgiler başarıyla güncellendi.</div>
  <?php endif; ?>

  <form method="POST">
    <label>Ad</label>
    <input type="text" name="ad" value="<?= htmlspecialchars($uye['ad']) ?>" required>

    <label>Soyad</label>
    <input type="text" name="soyad" value="<?= htmlspecialchars($uye['soyad']) ?>" required>

    <label>Kan Grubu</label>
    <input type="text" name="kan_grubu" value="<?= htmlspecialchars($uye['kan_grubu']) ?>">

    <label>Telefon</label>
    <input type="text" name="telefon" value="<?= htmlspecialchars($uye['telefon']) ?>">

    <label>E-Posta</label>
    <input type="email" name="eposta" value="<?= htmlspecialchars($uye['eposta']) ?>" required>

    <label>Sağlık Durumu</label>
    <textarea name="saglik_durumu"><?= htmlspecialchars($uye['saglik_durumu']) ?></textarea>

    <button type="submit"><i class="fas fa-pen"></i> Güncelle</button>
    </form>
</div>

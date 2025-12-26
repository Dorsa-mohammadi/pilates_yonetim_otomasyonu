<?php
require_once '../config.php';
session_start();

$egitmen_id = $_SESSION['kullanici_id'];
$mesaj = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aciklama = $_POST['aciklama'] ?? '';
    $sorular = $_POST['soru'] ?? [];

    $conn->beginTransaction();
    $stmt = $conn->prepare("INSERT INTO anketler (egitmen_id, aciklama) VALUES (?, ?)");
    $stmt->execute([$egitmen_id, $aciklama]);
    $anket_id = $conn->lastInsertId();

    $soru_stmt = $conn->prepare("INSERT INTO anket_sorulari (anket_id, soru_metni) VALUES (?, ?)");
    foreach ($sorular as $soru) {
        if (trim($soru) !== '') {
            $soru_stmt->execute([$anket_id, $soru]);
        }
    }

    $conn->commit();
    $mesaj = "Anket başarıyla oluşturuldu.";
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Anket Oluştur</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f6f9;
      padding: 40px;
    }
    .container {
      background: white;
      max-width: 700px;
      margin: auto;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }
    textarea, input[type="text"] {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    .soru {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 10px;
    }
    .soru input[type="text"] {
      flex: 1;
    }
    button {
      padding: 10px 20px;
      background-color: #f57235;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      margin-top: 10px;
    }
    button:hover {
      background-color: #f57235;
    }
    .mesaj {
      background-color: #d4edda;
      color: #155724;
      padding: 12px;
      margin-bottom: 20px;
      border-radius: 6px;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Yeni Anket Oluştur</h2>

  <?php if ($mesaj): ?>
    <div class="mesaj"><?= htmlspecialchars($mesaj) ?></div>
  <?php endif; ?>

  <form method="post">
    <textarea name="aciklama" placeholder="Anket açıklamasını buraya yazın..." required></textarea>

    <div id="soru-alani">
      <div class="soru"><input type="text" name="soru[]" placeholder="Soru 1" required></div>
    </div>

    <button type="button" onclick="soruEkle()">+ Soru Ekle</button>
    <button type="submit">Anketi Oluştur</button>
  </form>
</div>

<script>
let soruSayisi = 1;
function soruEkle() {
  soruSayisi++;
  const div = document.createElement("div");
  div.className = "soru";
  div.innerHTML = '<input type="text" name="soru[]" placeholder=" " required>';
  document.getElementById("soru-alani").appendChild(div);
}
</script>

</body>
</html>

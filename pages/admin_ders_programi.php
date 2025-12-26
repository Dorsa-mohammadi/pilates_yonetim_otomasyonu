<?php
require_once '../config.php';
session_start();
$FONK->oturumKontrol('admin');

$egitmen_id = 3; // Sabit eğitmen ID
$uyeler = $conn->query("SELECT id, ad, soyad FROM uyeler")->fetchAll();

$gun_map = [
    'Monday' => 'Pazartesi', 'Tuesday' => 'Sali', 'Wednesday' => 'Carsamba',
    'Thursday' => 'Persembe', 'Friday' => 'Cuma', 'Saturday' => 'Cumartesi', 'Sunday' => 'Pazar'
];

$saatler = [
    ['09:00', '10:00'], ['10:15', '11:15'], ['11:30', '12:30'],
    ['13:30', '14:30'], ['14:45', '15:45'], ['16:00', '17:00'],
    ['17:15', '18:15'], ['18:30', '19:30'], ['19:45', '20:45'],
];

// Doluluk verileri
$dolular = [];
$stmt = $conn->prepare("
    SELECT eds.id AS ders_id, eds.gun, TIME_FORMAT(eds.saat, '%H:%i') as saat, eds.ders_tipi,
           k.id AS kayit_id, u.ad, u.soyad, u.id as uye_id
    FROM kayitlar k
    JOIN egitmen_ders_saatleri eds ON eds.id = k.egitmen_ders_saati_id
    JOIN uyeler u ON u.id = k.uye_id
    WHERE eds.egitmen_id = ?
");
$stmt->execute([$egitmen_id]);
foreach ($stmt as $row) {
    $saat = $row['saat'];
    $gun = $row['gun'];
    $dolular[$gun][$saat]['ders_id'] = $row['ders_id'];
    $dolular[$gun][$saat]['uyeler'][] = [
        'id' => $row['uye_id'],
        'ad' => $row['ad'] . ' ' . $row['soyad'],
        'kayit_id' => $row['kayit_id']
    ];
}

// Kayıt işlemi
if (isset($_POST['kaydet'])) {
    $gun = $_POST['gun'];
    $saat = $_POST['saat'];
    $uye_ids = $_POST['uye_ids'] ?? [];

    if (!empty($uye_ids)) {
        $stmt = $conn->prepare("SELECT id FROM egitmen_ders_saatleri WHERE egitmen_id = ? AND gun = ? AND saat = ?");
        $stmt->execute([$egitmen_id, $gun, $saat]);
        $row = $stmt->fetch();

        if (!$row) {
            $stmt = $conn->prepare("INSERT INTO egitmen_ders_saatleri (egitmen_id, gun, saat) VALUES (?, ?, ?)");
            $stmt->execute([$egitmen_id, $gun, $saat]);
            $ders_id = $conn->lastInsertId();
        } else {
            $ders_id = $row['id'];
        }

        $stmt = $conn->prepare("SELECT COUNT(*) FROM kayitlar WHERE egitmen_ders_saati_id = ?");
        $stmt->execute([$ders_id]);
        if ($stmt->fetchColumn() + count($uye_ids) > 3) {
            echo "<script>alert('Maksimum 3 kişi seçilebilir.'); window.history.back();</script>";
            exit;
        }

        foreach ($uye_ids as $uye_id) {
            $kontrol = $conn->prepare("SELECT id FROM kayitlar WHERE uye_id = ? AND egitmen_ders_saati_id = ?");
            $kontrol->execute([$uye_id, $ders_id]);
            if (!$kontrol->rowCount()) {
                $conn->prepare("INSERT INTO kayitlar (uye_id, egitmen_ders_saati_id) VALUES (?, ?)")->execute([$uye_id, $ders_id]);
            }
        }
    }

    header("Location: admin_ders_programi.php");
    exit;
}

// Silme
if (isset($_POST['tek_iptal'])) {
    $conn->prepare("DELETE FROM kayitlar WHERE id = ?")->execute([$_POST['kayit_id']]);
    header("Location: admin_ders_programi.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Ders Programı</title>
<style>
body { font-family: Arial; margin: 20px; background: #fdfaf6; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; text-align: center; }
th, td { border: 1px solid #ccc; padding: 10px; }
th { background: #e67e22; color: white; }
button { padding: 6px 10px; background: #2980b9; color: white; border: none; border-radius: 4px; cursor: pointer; }
.modal { display: none; position: fixed; background: white; padding: 20px; border: 1px solid #ccc; border-radius: 10px; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1001; width: 300px; }
.modal.active { display: block; }
.overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; }
.overlay.active { display: block; }
.kutu { background: #eee; padding: 6px 10px; margin-bottom: 5px; display: flex; justify-content: space-between; }
</style>
</head>
<body>

<h2>Ders Programı - Eğitmen ID: <?= $egitmen_id ?></h2>
<div id="overlay" class="overlay"></div>

<table>
  <tr>
    <th>Saat</th>
    <?php
    $gunler = [];
    for ($i = 0; $i < 7; $i++) {
        $dt = new DateTime();
        $dt->modify("+$i day");
        $gun = $gun_map[$dt->format('l')];
        $tarih = $dt->format('d.m.Y');
        $gunler[] = ['gun' => $gun, 'tarih' => $dt->format('Y-m-d'), 'goster' => "$gun<br><small>$tarih</small>"];
        echo "<th>{$gunler[$i]['goster']}</th>";
    }
    ?>
  </tr>
  <?php foreach ($saatler as [$b, $s]): ?>
  <tr>
    <td><?= "$b<br>-$s" ?></td>
    <?php foreach ($gunler as $g): 
      $gun = $g['gun'];
      $tarihSaat = new DateTime($g['tarih'] . ' ' . $b);
      $gecmisMi = $tarihSaat < new DateTime();
      $uid = md5($gun.$b);
      $kisi = count($dolular[$gun][$b]['uyeler'] ?? []);
      ?>
      <td>
        <?php if ($gun == 'Pazar'): ?>
          Kapalı
        <?php else: ?>
          <button onclick="showModal('m<?= $uid ?>')">Kayıtlı (<?= $kisi ?>)</button>
          <div id="m<?= $uid ?>" class="modal">
            <button class="modal-close" data-modal="m<?= $uid ?>" style="float:right">&times;</button>
            <h4><?= $gun ?> - <?= $b ?></h4>
            <?php foreach ($dolular[$gun][$b]['uyeler'] ?? [] as $u): ?>
              <div class="kutu">
                <?= $u['ad'] ?>
                <form method="post" onsubmit="event.stopPropagation();">
                  <input type="hidden" name="kayit_id" value="<?= $u['kayit_id'] ?>">
                  <button name="tek_iptal" type="submit">&times;</button>
                </form>
              </div>
            <?php endforeach; ?>
            <?php if (!$gecmisMi && $kisi < 3): ?>
            <form method="post" onsubmit="return sinirla('#m<?= $uid ?>')">
              <input type="hidden" name="gun" value="<?= $gun ?>">
              <input type="hidden" name="saat" value="<?= $b ?>">
              <?php foreach ($uyeler as $u): ?>
                <label><input type="checkbox" name="uye_ids[]" value="<?= $u['id'] ?>"> <?= $u['ad'] . ' ' . $u['soyad'] ?></label><br>
              <?php endforeach; ?>
              <button name="kaydet">Kaydet</button>
            </form>
            <?php elseif ($gecmisMi): ?>
              <p style="color:red;">Geçmiş saate kayıt yapılamaz</p>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </td>
    <?php endforeach; ?>
  </tr>
  <?php endforeach; ?>
</table>

<script>
function showModal(id) {
    document.getElementById(id).classList.add("active");
    document.getElementById("overlay").classList.add("active");
}
function sinirla(secici) {
    let secilen = document.querySelectorAll(secici + " input[type='checkbox']:checked");
    if (secilen.length > 3) {
        alert("Maksimum 3 kişi seçebilirsiniz.");
        return false;
    }
    return true;
}
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.modal-close').forEach(btn => {
        btn.addEventListener("click", function () {
            let id = this.getAttribute("data-modal");
            document.getElementById(id).classList.remove("active");
            document.getElementById("overlay").classList.remove("active");
        });
    });
});
</script>

</body>
</html>

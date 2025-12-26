<?php
require_once 'config.php';
$FONK->oturumKontrol('uye');

$uye_id = $_SESSION['kullanici_id'];
$uye_detay = $conn->prepare("SELECT id FROM uyeler WHERE kullanici_id = ?");
$uye_detay->execute([$uye_id]);
$uye_id = $uye_detay->fetchColumn();

$egitmen_id = 3; // SABİT EĞİTMEN ID

// Randevu iptal işlemi
if (isset($_POST['iptal_et'])) {
    $gun = $_POST['gun'];
    $saat = $_POST['saat'];

    $stmt = $conn->prepare("DELETE FROM kayitlar 
        WHERE uye_id = ? AND egitmen_ders_saati_id IN (
            SELECT id FROM egitmen_ders_saatleri WHERE egitmen_id = ? AND gun = ? AND saat = ?
        )");
    $stmt->execute([$uye_id, $egitmen_id, $gun, $saat]);

    $stmt = $conn->prepare("UPDATE randevular 
        SET durum = 'iptal', iptal_edildi_by = 'uye' 
        WHERE uye_id = ? AND egitmen_id = ? AND gun = ? AND saat = ?");
    $stmt->execute([$uye_id, $egitmen_id, $gun, $saat]);

    header("Location: uye_panel.php?sayfa=ders_programi");
    exit;
}

$gun_map = [
    'Monday' => 'Pazartesi', 'Tuesday' => 'Sali', 'Wednesday' => 'Carsamba',
    'Thursday' => 'Persembe', 'Friday' => 'Cuma', 'Saturday' => 'Cumartesi', 'Sunday' => 'Pazar'
];

$saatler = [
    ['09:00', '10:00'], ['10:15', '11:15'], ['11:30', '12:30'],
    ['13:30', '14:30'], ['14:45', '15:45'], ['16:00', '17:00'],
    ['17:15', '18:15'], ['18:30', '19:30'], ['19:45', '20:45'],
];

// Randevu talep işlemi
if (isset($_POST['talep'])) {
    $gun = $_POST['gun'];
    $saat = $_POST['saat'];
    $tarih = date('Y-m-d');

    $kontrol = $conn->prepare("SELECT COUNT(*) FROM randevular WHERE uye_id = ? AND egitmen_id = ? AND gun = ? AND saat = ?");
    $kontrol->execute([$uye_id, $egitmen_id, $gun, $saat]);

    if ($kontrol->fetchColumn() == 0) {
        $conn->prepare("INSERT INTO randevular (uye_id, egitmen_id, tarih, gun, saat, durum)
                        VALUES (?, ?, ?, ?, ?, 'beklemede')")
              ->execute([$uye_id, $egitmen_id, $tarih, $gun, $saat]);
        header("Location: uye_panel.php?sayfa=ders_programi");
        exit;
    }
}

// Doluluk bilgisi
$doluluk = [];
$stmt = $conn->prepare("SELECT eds.gun, TIME_FORMAT(eds.saat, '%H:%i') as saat, COUNT(k.id) as kisi_sayisi
    FROM egitmen_ders_saatleri eds
    LEFT JOIN kayitlar k ON k.egitmen_ders_saati_id = eds.id
    WHERE eds.egitmen_id = ?
    GROUP BY eds.gun, eds.saat");
$stmt->execute([$egitmen_id]);
foreach ($stmt as $row) {
    $doluluk[$row['gun']][$row['saat']] = $row['kisi_sayisi'];
}

// Bekleyen talepler
$durumlar = [];
$stmt = $conn->prepare("SELECT gun, TIME_FORMAT(saat, '%H:%i') as saat, durum FROM randevular WHERE egitmen_id = ? AND uye_id = ?");
$stmt->execute([$egitmen_id, $uye_id]);
foreach ($stmt as $row) {
    $durumlar[$row['gun']][$row['saat']] = $row['durum'];
}

// Onaylanmış kayıtlar
$onayli = [];
$stmt = $conn->prepare("SELECT eds.gun, TIME_FORMAT(eds.saat, '%H:%i') as saat
    FROM kayitlar k
    JOIN egitmen_ders_saatleri eds ON eds.id = k.egitmen_ders_saati_id
    WHERE eds.egitmen_id = ? AND k.uye_id = ?");
$stmt->execute([$egitmen_id, $uye_id]);
foreach ($stmt as $row) {
    $onayli[$row['gun']][$row['saat']] = true;
}
?>

<style>
    body { font-family: 'Arial', sans-serif; background: #f4f6f8; margin: 0; }
    .kapali-td { background-color: #f2f2f2; color: #999; font-weight: bold; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; text-align: center; }
    th, td { border: 1px solid #ccc; padding: 10px; font-size: 14px; }
    th { background-color: #e67e22; color: white; }
    button { background-color: #3498db; color: white; border: none; padding: 6px 12px; margin-top: 6px; border-radius: 4px; cursor: pointer; font-size: 13px; }
    button:hover { background-color: #2980b9; }
    .iptal { background-color: #e74c3c; }
    .iptal:hover { background-color: #c0392b; }
</style>

<?php
$gunler = [];
$tarih_map = [];

for ($i = 0; $i < 7; $i++) {
    $date = new DateTime();
    $date->modify("+{$i} day");
    $gun = $gun_map[$date->format('l')];
    $tarih = $date->format('d.m.Y');
    $gunler[] = $gun;
    $tarih_map[$gun] = $tarih;
}

echo "<h2>Ders Programı</h2>";
echo "<table><tr><th>Saat</th>";
foreach ($gunler as $gun) {
    echo "<th>$gun<br><small>{$tarih_map[$gun]}</small></th>";
}
echo "</tr>";

foreach ($saatler as [$baslangic, $bitis]) {
    echo "<tr><td>$baslangic<br>-$bitis</td>";
    foreach ($gunler as $gun) {
        $tarih = $tarih_map[$gun];
        $hucresaat = DateTime::createFromFormat('d.m.Y H:i', "$tarih $baslangic");
        echo "<td>";
        if ($gun === "Pazar") {
            echo "<div class='kapali-td'>Kapalı</div>";
        } elseif ($hucresaat < new DateTime()) {
            echo "<div class='kapali-td'>Geçmiş</div>";
        } else {
            $kisi = $doluluk[$gun][$baslangic] ?? 0;
            $durum = $durumlar[$gun][$baslangic] ?? null;
            $onay = $onayli[$gun][$baslangic] ?? false;

            if ($durum === 'beklemede') {
                echo "<strong style='color:orange;'>Talep Gönderildi</strong>";
            } elseif ($durum === 'onaylandi') {
                echo "<strong style='color:green;'>Randevunuz Onaylandı</strong>";
                ?>
                <form method="post">
                    <input type="hidden" name="iptal_et" value="1">
                    <input type="hidden" name="gun" value="<?= $gun ?>">
                    <input type="hidden" name="saat" value="<?= $baslangic ?>">
                    <button type="submit" class="iptal">İptal Et</button>
                </form>
                <?php
            } elseif ($durum === 'reddedildi' || $durum === 'iptal') {
                $stmtIptal = $conn->prepare("SELECT iptal_edildi_by FROM randevular 
                    WHERE uye_id = ? AND egitmen_id = ? AND gun = ? AND saat = ?");
                $stmtIptal->execute([$uye_id, $egitmen_id, $gun, $baslangic]);
                $kim = $stmtIptal->fetchColumn();

                if ($kim === 'admin') {
                    echo "<strong style='color:red;'>Admin Tarafından İptal Edildi</strong>";
                } else {
                    echo "<strong style='color:red;'>Tarafınızdan İptal Edildi</strong>";
                }
            } elseif ($kisi >= 3) {
                echo "<span style='color:red;'>Dolu</span>";
            } else {
                if ($kisi > 0) echo "<span style='color:#777; font-size:12px;'>Kalan: " . (3 - $kisi) . "</span><br>";
                ?>
                <form method="post">
                    <input type="hidden" name="gun" value="<?= $gun ?>">
                    <input type="hidden" name="saat" value="<?= $baslangic ?>">
                    <button name="talep">Talep Et</button>
                </form>
                <?php
            }
        }
        echo "</td>";
    }
    echo "</tr>";
}
echo "</table>";
?>

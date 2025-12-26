<?php

class FONK {

    public function oturumKontrol($rol) {
        if (!isset($_SESSION['kullanici_id']) || $_SESSION['rol'] !== $rol) {
            if ($rol === 'admin') {
                header("Location: /admin_giris.php");
            } elseif ($rol === 'uye') {
                header("Location: /pages/uye_giris.php");
            } else {
                header("Location: /anasayfa.php"); // varsay覺lan y繹nlendirme
            }
            exit;
        }
    }


    public function yonlendir($url) {
        header("Location: $url");
        exit;
    }

    public function mesaj($metin, $renk = "black") {
        echo "<p style='color:$renk;'>$metin</p>";
}
}


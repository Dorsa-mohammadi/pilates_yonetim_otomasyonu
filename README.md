# 🧘 Pilates Studio Automation System

Bu proje, bir pilates stüdyosunun tüm günlük operasyonlarını dijital ortamda yönetebilmesi amacıyla geliştirilmiş **kapsamlı ve güvenli bir web tabanlı otomasyon sistemidir**.

Sistem; **üye kayıt süreci**, **ders ve randevu yönetimi**, **kapasite kontrolü**, **ödeme takibi**, **anket sistemi** ve **mesajlaşma** gibi tüm iş akışlarını tek bir merkezde toplar.

Amaç;  
- **Üyeler için** sade, güvenli ve kullanıcı dostu bir deneyim  
- **Yönetici için** kontrollü, düzenli ve sürdürülebilir bir yönetim altyapısı sunmaktır.

Uygulama, **rol bazlı yetkilendirme** mimarisiyle geliştirilmiş olup, üye ve yönetici panelleri tamamen birbirinden ayrılmıştır.



---



## 🚀 Kullanılan Teknolojiler

- PHP (Native)
- MySQL
- HTML5 / CSS3 / JavaScript
- PHPMailer
- SMTP
- Responsive Design
- XAMPP (Localhost)



---



## 👥 Kullanıcı Rolleri

- **Ziyaretçi**
  - Ön yüzü görüntüler
  - Kayıt ve giriş sayfalarına erişir

- **Üye**
  - Ders programını görüntüler
  - Randevu oluşturur
  - Eğitmen ile mesajlaşır
  - Anketleri yanıtlar
  - Ödeme bilgilerini görüntüler
  - Profil bilgilerini günceller

- **Yönetici (Eğitmen)**
  - Üyeleri onaylar / siler
  - Ders programını yönetir
  - Randevuları onaylar
  - Anket oluşturur ve sonuçları görüntüler
  - Ödeme takibini yapar

Her kullanıcı yalnızca kendi yetkisine ait alanlara erişebilir.



---



## 🌐 Ön Yüz (Ziyaretçi Alanı)

### 🏠 Anasayfa
![Anasayfa](screenshots/anasayfa.PNG)

![Anasayfa 1](screenshots/anasayfa1.PNG)

![Anasayfa 2](screenshots/anasayfa2.PNG)

![Anasayfa 3](screenshots/anasayfa3.PNG)

![Anasayfa 4](screenshots/anasayfa4.PNG)

- Sabit üst menü ve logo
- Anasayfa, Hakkımızda, Hizmetler, İletişim linkleri
- Mobil uyumlu hamburger menü
- “Bize Katılın” butonu
- Google Maps entegrasyonu

☰ Hamburger Menü
- Üye Girişi
- Yönetici Girişi
- Üye Kayıt



---



## 📝 Üye Kayıt ve Giriş Süreci

### 👤 Üye Kayıt
![Üye Kayıt](screenshots/UyeKayit.PNG)

- Kullanıcı kayıt formunu doldurur
- Hesap pasif olarak oluşturulur
- Yönetici onayından sonra aktif edilir



---



### 🔑 Üye Giriş
![Üye Giriş](screenshots/UyeGirisi.PNG)

- Sadece onaylı üyeler giriş yapabilir
- Pasif veya silinmiş hesaplar engellenir



---



### 🔐 Şifremi Unuttum & Şifre Yenileme
![Şifre Unuttum](screenshots/SifremiUnuttum.PNG)

![Şifre Yenileme](screenshots/SifreSifirlama.PNG)

- PHPMailer kullanılarak SMTP üzerinden mail gönderilir
- Şifreler hash’li olarak saklanır
- Güvenli şifre yenileme bağlantısı oluşturulur



---



## 👤 Üye Paneli
![Üye Paneli](screenshots/UyePaneli.PNG)



### 📅 Ders Programı & Randevu Oluşturma
![Randevu](screenshots/UyeDersProgrami.PNG)

- Gün, saat ve eğitmen seçimi
- Salon kapasitesi kontrolü
- Geçmiş tarih ve saatlere randevu engeli
- Kapasite doluysa randevu reddedilir



---



### 📨 Mesajlaşma
![Mesajlaşma](screenshots/Mesajlar.PNG)

- Üye ve eğitmen arasında sistem içi mesajlaşma



---



### 📊 Anketler
![Anketler](screenshots/UyeAnket.PNG)

- Yönetici tarafından oluşturulur
- Üyeler cevaplayabilir



---



### 💳 Ödeme Bilgileri
![Ödeme](screenshots/UyeOdemeBilgisi.PNG)

- Ödenen ve ödenmeyen aylar görüntülenir



---



### ⚙️ Profil Yönetimi
![Profil](screenshots/Bilgilerim.PNG)

- Ad Soyad
- E-posta
- İletişim bilgileri güncellenebilir



---




## 🛠️ Yönetici / Eğitmen Paneli

### 🔑 Yönetici Giriş
![Yönetici Giriş](screenshots/YoneticiGirisi.PNG)

- Yönetici (eğitmen) sisteme giriş yapar
- Kullanıcı adı ve şifre ile güvenli erişim
- Yetkisiz kullanıcılar erişemez
- Giriş sonrası yönetici paneli açılır


---


### 👥 Üye Yönetimi
![Üye Yönetimi](screenshots/UyelikTalepleri.PNG)

- Üyeleri görüntüleme
- Onaylama
- Silme



---



### 📆 Ders Programı Yönetimi
![Ders Programı](screenshots/AdminDersProgrami.PNG)

![Ders Programı 1](screenshots/AdminDersProgrami1.PNG)

- Ders programı düzenleme
- Değişiklikler üyelere otomatik yansır




---




### 📨 Mesajlaşma (Yönetici ↔ Üye)
![Yönetici Mesajlaşma](screenshots/UyelereMesajGonder.PNG)

- Yönetici, üyelerle birebir veya toplu mesajlaşabilir
- Üyelerden gelen mesajları görüntüler
- Aynı anda birden fazla üyeye mesaj gönderebilir



---



### 📅 Randevu Yönetimi
![Randevu Yönetimi](screenshots/AdminRandevuTalepleri.PNG)

- Randevu onaylama
- Randevu silme



---



### 📊 Anket Oluşturma & Sonuçlar
![Anket Sonuçları](screenshots/AnketOlustur.PNG)

![Anket Sonuçları](screenshots/AnketCevaplari.PNG)

- Anket oluşturma
- Üye cevaplarını görüntüleme




---




### 👥 Üye Listesi (Yönetici Paneli)

![Üye Listesi](screenshots/UyeListesi.PNG)


- Yönetici panelinde, kayıtlı tüm üyelerin bilgilerini görebilir ve yönetebilir
- Mevcut üyelerin bilgilerini güncelleyebilir  



---




### 💰 Ödeme Takibi
![Ödeme Takibi](screenshots/AdminOdemeler.PNG)

![Ödeme Takibi 2](screenshots/AdminOdemeler1.PNG)

- Üyelerin ödeme geçmişi
- Eksik ayların takibi



---




## 🔒 Güvenlik & Yetkilendirme

- Rol bazlı erişim kontrolü
- Yetkisiz erişim engelleme
- Kapasite ve tarih doğrulamaları
- Hash’lenmiş şifreler
- Ortam bazlı config yapısı
- .gitignore ile gizli dosya koruması


---


## 📂 Kurulum

```text
1. Projeyi klonlayın veya indirin
2. MySQL veritabanını import edin
3. config.ornek.php ve db.ornek.php dosyalarını kopyalayarak:
   - config.php
   - includes/db.php
   olarak oluşturun
4. Veritabanı ve mail ayarlarını düzenleyin
5. Projeyi XAMPP üzerinde çalıştırın

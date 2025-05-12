# PhpYonetim
# Kurumiçi Yazılım Projesi

## Açıklama
Saf PHP ile modüler MVC yapısında geliştirilen, AdminLTE 3.2 teması ve Font Awesome 6 ikon seti kullanılarak oluşturulmuş bir kurumiçi yazılım. Proje, kullanıcı yönetimi, yetkilendirme, stok yönetimi, görev takibi, doküman paylaşımı ve API entegrasyonu gibi özellikleri desteklemek için tasarlanmıştır. Composer kullanılmadan, Linux hosting üzerinde çalışacak şekilde optimize edilmiştir.

## Mevcut Durum
Projenin temel yapısı tamamlanmış olup, aşağıdaki bileşenler geliştirilmiştir:

### Tamamlanan Özellikler
- **Temel Yapı**:
  - **`/public/index.php`**: Uygulamanın giriş noktası, tüm istekleri yönlendirir.
  - **`/public/.htaccess`**: Apache için URL yönlendirme ve güvenlik başlıkları (XSS koruması, erişim kısıtlamaları).
  - **`/logs`**: Hata ve erişim logları için altyapı (error.log, access.log kullanılacak).
- **Statik Dosyalar** (`/public/assets`):
  - **CSS**: AdminLTE (`adminlte.min.css`), Font Awesome (`fontawesome.min.css`), DataTables (`datatables.min.css`), özel stiller (`style.css`).
  - **JS**: AdminLTE (`adminlte.js`), jQuery (`jquery.min.js`), Bootstrap 5 (`bootstrap.min.js`), DataTables (`datatables.min.js`), Chart.js (`chart.js`), özel script’ler (`scripts.js`).
  - **Görseller**: Kurumsal logo (`logo.png`).
  - **Uploads**: Kullanıcı yüklemeleri için dizin (`/public/assets/uploads`).
- **Core Sınıflar** (`/app/core`):
  - `Database.php`: PDO ile MySQL bağlantısı ve veritabanı işlemleri.
  - `Router.php`: URL yönlendirme ve modül/aksiyon eşleştirmesi.
  - `BaseController.php`: Tüm controller’lar için ortak işlevler (view yükleme, yönlendirme).
  - `Session.php`: Oturum yönetimi (set, get, flash mesajlar).
  - `Config.php`: Genel ayarlar (veritabanı, URL, uygulama adı).
  - `Helpers.php`: Yardımcı fonksiyonlar (XSS filtreleme, URL oluşturma).
  - `Auth.php`: Kullanıcı giriş/çıkış ve izin kontrolü.
  - `Role.php`: Rol işlemleri (ekleme, silme, listeleme).
  - `Permission.php`: İzin işlemleri (ekleme, silme, listeleme).
- **Şablonlar** (`/app/templates`):
  - `layout.php`: AdminLTE dashboard düzeni, dinamik içerik yükleme.
  - `header.php`: Üst menü, logo ve kullanıcı menüsü (Font Awesome ikonlarıyla).
  - `footer.php`: Alt kısım, sürüm ve copyright bilgileri.
  - `sidebar.php`: Dinamik yan menü, modül bağlantıları için hazır.
- **Kullanıcı ve Yetkilendirme Modülleri** (`/app/modules`):
  - **`/modules/user`**:
    - Kayıt, giriş, profil görüntüleme, kullanıcı listeleme ve düzenleme.
    - Arayüz: AdminLTE formları ve DataTables, Font Awesome ikonları.
    - İzinler: `user.view`, `user.edit`.
  - **`/modules/auth`**:
    - Rol ve izin yönetimi (ekleme, listeleme, düzenleme).
    - Arayüz: AdminLTE formları ve DataTables.
    - İzinler: `auth.view`, `auth.edit`.
- **Veritabanı Tabloları**:
  - `users`: Kullanıcı bilgileri (email, password, first_name, last_name).
  - `roles`: Rol bilgileri (name, description).
  - `permissions`: İzin bilgileri (name, description).
  - `role_permissions`: Rol-izin eşleşmeleri.
  - `user_roles`: Kullanıcı-rol eşleşmeleri.

### Gelecek Planlar
- **Modüller**:
  - Stok yönetimi (`stock`): Ürün yönetimi, giriş/çıkış, envanter takibi.
  - Görev yönetimi (`task`), doküman paylaşımı (`document`), API (`api`), fatura (`invoice`).
- **Veritabanı**: Stok ve diğer modüller için tablolar (`stock_products`, `stock_entries`, vb.).
- **Loglama**: `/logs/error.log` ve `/logs/access.log` dosyalarının oluşturulması.
- **Güvenlik**: CSRF token’ları, oturum güvenliği iyileştirmeleri.
- **Test**: Temel sistemin (giriş, yetkilendirme, arayüz) doğrulanması.
- **Deploy**: Linux hosting’e kurulum.

## Kurulum
1. **Bağımlılıkları Kurun**:
   - PHP 8.2 veya üstü
   - MySQL 8.0 veya MariaDB
   - Apache veya Nginx (mod_rewrite etkin)
2. **Dosyaları Yükleyin**:
   - Proje dosyalarını Linux hosting’inize yükleyin.
3. **Veritabanını Oluşturun**:
   - MySQL’de bir veritabanı oluşturun (örn: `kurumici_db`).
   - `/app/modules/user/database_schema.sql` dosyasındaki tablo şemalarını çalıştırın.
4. **Veritabanı Ayarlarını Yapılandırın**:
   - `/app/core/Config.php`’daki ayarları güncelleyin:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'kurumici_db');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('BASE_URL', 'http://localhost/kurumici/');
     ```
5. **Dosya İzinlerini Ayarlayın**:
   - `/public/assets/uploads`: 775 (kullanıcı yüklemeleri için)
   - `/logs`: 775 (log yazımı için, oluşturulacak)
   - Diğer dosyalar: 644, klasörler: 755
6. **URL Yönlendirmeyi Etkinleştirin**:
   - `/public/.htaccess` dosyasını kontrol edin, Apache’de `mod_rewrite` etkin olmalı.
7. **Test Edin**:
   - Tarayıcıda `http://localhost/kurumici/` adresine gidin.
   - `/user/login` sayfasını açarak giriş yapmayı deneyin.

## Bağımlılıklar
- **Sunucu**:
  - PHP 8.2+
  - MySQL 8.0+ veya MariaDB
  - Apache veya Nginx (mod_rewrite ile)
- **Kütüphaneler**:
  - AdminLTE 3.2: `/public/assets/css/adminlte.min.css`, `/public/assets/js/adminlte.js`
  - Font Awesome 6.4: `/public/assets/css/fontawesome.min.css`
  - DataTables 1.13.1: `/public/assets/css/datatables.min.css`, `/public/assets/js/datatables.min.js`
  - jQuery 3.6.0: `/public/assets/js/jquery.min.js`
  - Bootstrap 5.3: `/public/assets/js/bootstrap.min.js`
  - Chart.js 4.2.1: `/public/assets/js/chart.js`

## Kullanım
- **Giriş Noktası**: `/public/index.php`
- **Arayüz**: AdminLTE teması, Font Awesome ikonlarıyla modern ve kullanıcı dostu.
- **Modüller**:
  - **Kullanıcı Yönetimi**: `/user/login`, `/user/register`, `/user/list`, `/user/profile`
  - **Yetkilendirme**: `/auth/roleList`, `/auth/permissionList`
- **Yetkilendirme**:
  - Kullanıcılar, roller ve izinler üzerinden yetkilendirilir.
  - Örnek izinler: `user.view`, `user.edit`, `auth.view`, `auth.edit`.
- **Hata Ayıklama**:
  - Hatalar `/logs/error.log`’a yazılır (dosya oluşturulacak).
  - Erişim logları `/logs/access.log`’ta saklanacak.

## Geliştirici Notları
- **Modüler Yapı**: Yeni modüller `/app/modules` altına kolayca eklenebilir (örn: `/modules/stock`).
- **Güvenlik**:
  - XSS koruması: `/app/core/Helpers.php`’deki `sanitize()` fonksiyonu.
  - Şifre güvenliği: `password_hash()` ile şifreler saklanır.
  - PDO ile SQL injection koruması.
- **Özelleştirme**:
  - `/public/assets/css/style.css`: Marka renkleri ve stiller için.
  - `/public/assets/js/scripts.js`: Özel JS fonksiyonları için.
- **Test**:
  - Yerel ortamda veritabanı bağlantısını ve giriş sayfasını test edin.
  - AdminLTE arayüzünü ve Font Awesome ikonlarını kontrol edin.

## Lisans
MIT Lisansı

## Gelecek Adımlar
1. **`.gitignore`**: Git için yoksayılacak dosyaları tanımlamak.
2. **Log Dosyaları**: `/logs/error.log` ve `/logs/access.log` oluşturmak.
3. **Veritabanı Testi**: Tabloları oluşturup varsayılan veriler eklemek.
4. **Modüller**:
   - Stok yönetimi (`stock`): Ürün yönetimi, giriş/çıkış, envanter takibi.
   - Diğer modüller: `task`, `document`, `api`, `invoice`.
5. **Güvenlik**: CSRF token’ları ve oturum güvenliği.
6. **Deploy**: Linux hosting’e kurulum.
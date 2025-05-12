Kurumiçi Yönetim Sistemi
Bu proje, saf PHP ile geliştirilmiş, modüler MVC yapısında, Composer kullanılmadan Linux hosting üzerinde çalışabilen bir kurumiçi yönetim sistemidir. İşletmelerin stok, müşteri (eski adıyla cari), kullanıcı ve yetkilendirme süreçlerini yönetmek için tasarlanmıştır. Sistem, AdminLTE teması ve Font Awesome ikonlarıyla kullanıcı dostu bir arayüz sunar.
Özellikler

Modüler Yapı: Her modül (Stok, Customer, Kullanıcı, Yetkilendirme) bağımsız çalışır ve kolayca genişletilebilir.
Kullanıcı ve Yetkilendirme: Rol tabanlı erişim kontrolü (RBAC) ile güvenli kullanıcı yönetimi.
Stok Modülü: Ürün yönetimi, stok giriş/çıkış, envanter takibi, hiyerarşik gruplar (stok grubu, ara grubu, alt grubu), dinamik özellikler (renk, boyut, ağırlık), birden fazla resim desteği.
Customer Modülü: Müşteri hesap yönetimi, dinamik adresler (fatura/sevk ayrı), dinamik yetkili iletişim bilgileri, finansal işlemler (alış, satış, ödeme, tahsilat), bakiye takibi, Stok Modülü ile entegrasyon.
Filtreleme ve Raporlama: DataTables ile gelişmiş filtreleme, yazdırma ve dışa aktarma (PDF, Excel, CSV).
AdminLTE Entegrasyonu: Modern ve duyarlı arayüz, Select2 ve DataTables ile zengin kullanıcı deneyimi.
Yerel Bağımlılıklar: CDN kullanılmadan tüm JS/CSS kütüphaneleri yerel olarak entegre edilmiştir.

Teknolojiler

Backend: PHP 7.4+
Frontend: AdminLTE 3, Font Awesome 5, DataTables, Select2
Veritabanı: MySQL 8.0 veya MariaDB
Sunucu: Apache (Linux hosting)
Diğer: jQuery, Bootstrap 4, Chart.js

Kurulum
Gereksinimler

PHP 7.4 veya üzeri
MySQL 8.0 veya MariaDB
Apache sunucusu
Yazma izni olan /public/assets/uploads ve /logs dizinleri

Adımlar

Depoyu Klonlayın:
git clone <repository-url>
cd project-directory


Veritabanını Kurun:

database.sql dosyasını MySQL veya phpMyAdmin üzerinden çalıştırarak veritabanı ve tabloları oluşturun.
Veritabanı bağlantı ayarlarını /app/core/Database.php içinde güncelleyin:private $host = 'localhost';
private $db_name = 'your_database';
private $username = 'your_username';
private $password = 'your_password';




Dosya İzinlerini Ayarlayın:
chmod -R 755 public/assets/uploads
chmod -R 755 logs


Bağımlılıkları Kopyalayın:

/public/assets/js altına aşağıdaki dosyaları kopyalayın:
datatables.min.js, datatables.buttons.min.js, buttons.html5.min.js, jszip.min.js, pdfmake.min.js, vfs_fonts.js
jquery.min.js, bootstrap.min.js, chart.js, adminlte.js, scripts.js


/public/assets/css altına:
adminlte.min.css, fontawesome.min.css, datatables.min.css, buttons.dataTables.min.css, style.css


Bu dosyalar, DataTables ve AdminLTE’nin resmi sitelerinden indirilebilir.


Sunucuyu Yapılandırın:

Apache’ta public/ dizinini kök dizin olarak ayarlayın.
.htaccess dosyasını kontrol edin:RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]




Uygulamayı Başlatın:

Tarayıcıda http://your-domain/ adresine gidin.
Varsayılan kullanıcı ile giriş yapın (varsa database.sql’de tanımlı).



Modüller
1. Kullanıcı Modülü

Kullanıcı ekleme, düzenleme, silme.
E-posta ve şifre ile güvenli oturum yönetimi.
Tablo: users

2. Yetkilendirme Modülü

Rol tabanlı erişim kontrolü (RBAC).
İzinler ve rollerin yönetimi.
Tablolar: roles, permissions, role_permissions, user_roles

3. Stok Modülü

Özellikler:
Ürün yönetimi (kod, ad, birim, açıklama, barkod, resim).
Hiyerarşik gruplar (stok grubu, ara grubu, alt grubu).
Dinamik özellikler (renk, boyut, ağırlık).
Birden fazla resim yükleme.
Stok giriş/çıkış işlemleri.
Envanter sayımı ve düşük stok uyarıları.
Filtreleme (stok seviyesi, tarih, ürün, gruplar, özellikler).
Yazdırma ve dışa aktarma (PDF, Excel, CSV).


Tablolar:
stock_groups, stock_sub_groups, stock_sub_sub_groups
stock_products, stock_product_attributes, stock_product_images
stock_entries, stock_exits



4. Customer Modülü

Özellikler:
Müşteri hesap yönetimi (kod, unvan, tür, vergi bilgileri).
Dinamik adresler (fatura, sevk, diğer).
Dinamik yetkili iletişim bilgileri (isim, unvan, telefon, e-posta).
Finansal işlemler (alış, satış, ödeme, tahsilat).
Bakiye takibi (alacak, borç, net bakiye).
Stok Modülü entegrasyonu (stok giriş/çıkış bağlantıları).
Filtreleme (müşteri, adres, yetkili, işlem türü, tarih, bakiye).
Yazdırma ve dışa aktarma (PDF, Excel, CSV).


Tablolar:
customer_groups
customer_accounts, customer_addresses, customer_contacts
customer_transactions



Klasör Yapısı
project-directory/
├── app/
│   ├── core/
│   │   ├── Database.php
│   │   ├── BaseController.php
│   │   ├── Router.php
│   │   ├── Auth.php
│   │   ├── Session.php
│   │   └── Helpers.php
│   ├── modules/
│   │   ├── stock/
│   │   │   ├── models/
│   │   │   │   ├── StockModel.php
│   │   │   │   ├── StockGroupModel.php
│   │   │   │   ├── StockAttributeModel.php
│   │   │   │   └── StockImageModel.php
│   │   │   ├── views/
│   │   │   │   ├── product_list.php
│   │   │   │   ├── product_add.php
│   │   │   │   ├── product_edit.php
│   │   │   │   ├── product_detail.php
│   │   │   │   ├── group_list.php
│   │   │   │   ├── group_add.php
│   │   │   │   ├── group_edit.php
│   │   │   │   ├── entry_list.php
│   │   │   │   ├── entry_add.php
│   │   │   │   ├── exit_list.php
│   │   │   │   ├── exit_add.php
│   │   │   │   └── inventory_count.php
│   │   │   ├── controllers/
│   │   │   │   ├── StockController.php
│   │   │   │   └── StockGroupController.php
│   │   │   └── config.php
│   │   ├── customer/
│   │   │   ├── models/
│   │   │   │   ├── CustomerModel.php
│   │   │   │   ├── CustomerGroupModel.php
│   │   │   │   ├── CustomerAddressModel.php
│   │   │   │   └── CustomerContactModel.php
│   │   │   ├── views/
│   │   │   │   ├── customer_list.php
│   │   │   │   ├── customer_add.php
│   │   │   │   ├── customer_edit.php
│   │   │   │   ├── customer_detail.php
│   │   │   │   ├── group_list.php
│   │   │   │   ├── group_add.php
│   │   │   │   ├── group_edit.php
│   │   │   │   ├── transaction_list.php
│   │   │   │   ├── transaction_add.php
│   │   │   │   ├── transaction_edit.php
│   │   │   │   └── balance_summary.php
│   │   │   ├── controllers/
│   │   │   │   ├── CustomerController.php
│   │   │   │   └── CustomerGroupController.php
│   │   │   └── config.php
│   └── templates/
│       ├── layout.php
│       ├── header.php
│       ├── sidebar.php
│       └── footer.php
├── public/
│   ├── assets/
│   │   ├── css/
│   │   │   ├── adminlte.min.css
│   │   │   ├── fontawesome.min.css
│   │   │   ├── datatables.min.css
│   │   │   ├── buttons.dataTables.min.css
│   │   │   └── style.css
│   │   ├── js/
│   │   │   ├── jquery.min.js
│   │   │   ├── bootstrap.min.js
│   │   │   ├── datatables.min.js
│   │   │   ├── datatables.buttons.min.js
│   │   │   ├── buttons.html5.min.js
│   │   │   ├── jszip.min.js
│   │   │   ├── pdfmake.min.js
│   │   │   ├── vfs_fonts.js
│   │   │   ├── chart.js
│   │   │   ├── adminlte.js
│   │   │   └── scripts.js
│   │   └── uploads/
│   │       └── products/
│   ├── index.php
│   └── .htaccess
├── logs/
│   └── audit.log
└── database.sql

Geliştirme Durumu

Tamamlanan Modüller:
Kullanıcı Modülü: Kullanıcı yönetimi tamamlandı.
Yetkilendirme Modülü: RBAC tamamlandı.
Stok Modülü: Ürün yönetimi, stok işlemleri, filtreleme, yazdırma tamamlandı (Mayıs 2025).
Customer Modülü: Müşteri hesapları, dinamik adres/yetkili, işlemler, bakiye takibi tamamlandı (Mayıs 2025).


Planlanan Modüller:
Görev (Task) Modülü
Doküman (Document) Modülü
API Modülü
Fatura (Invoice) Modülü


Son Değişiklikler:
Cari Modülü, Customer Modülü olarak yeniden adlandırıldı ve tüm dosya/terimler güncellendi.
Customer Modülü’ne dinamik adres (fatura/sevk ayrı) ve yetkili iletişim bilgileri eklendi.
Tüm listeleme sayfalarına yazdırma ve dışa aktarma (PDF, Excel, CSV) eklendi, CDN yerine yerel bağımlılıklar kullanıldı.
Veritabanı şeması (database.sql) tüm modülleri kapsayacak şekilde birleştirildi.



Katkıda Bulunma

Depoyu forklayın ve klonlayın.
Yeni bir özellik veya hata düzeltmesi için dal oluşturun:git checkout -b feature/yeni-ozellik


Değişikliklerinizi yapın ve commit edin:git commit -m "Yeni özellik: Örnek açıklama"


Ana dala push edin ve pull request oluşturun:git push origin feature/yeni-ozellik



Lisans
Bu proje, MIT Lisansı altında lisanslanmıştır.
İletişim

E-posta: [email@example.com]
Geliştirici: [Your Name]


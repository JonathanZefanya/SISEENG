# SISEENG - Sistem Informasi Manajemen Gereja

![Version](https://img.shields.io/badge/version-1.0.0-blue)
![PHP](https://img.shields.io/badge/PHP-8.0+-purple)
![License](https://img.shields.io/badge/license-MIT-green)

Sistem Informasi Manajemen Gereja (SISEENG) adalah aplikasi web berbasis PHP Native dengan arsitektur MVC untuk membantu gereja mengelola informasi, jadwal, dan kegiatan jemaat. Tampilannya bergaya *super-app* (mobile-first, bottom navigation, kartu membulat) dan responsif di HP maupun desktop.

## 📋 Fitur Utama

### Website Publik
- **Beranda**: sapaan sesuai waktu (WIB), kartu ibadah terdekat beserta pengkhotbahnya, menu ikon, kegiatan mendatang, artikel terbaru, lokasi, dan ajakan donasi
- **Tentang Kami**: profil, visi-misi, dan sejarah gereja
- **Jadwal Ibadah & Jadwal Pengkhotbah**: jadwal ibadah rutin dan jadwal pengkhotbah per bulan
- **Kegiatan/Acara**: daftar dan detail event gereja
- **Artikel/Renungan**: artikel rohani dengan kategori
- **Donasi**: rekening bank dan QRIS
- **Kontak**: form kontak dengan captcha aritmatika, serta peta lokasi
- **Navigasi mobile**: bottom navigation dan menu "Lainnya" berupa bottom sheet

### Panel Admin
- **Dashboard**: sapaan, aksi cepat, statistik, pesan terbaru, dan kegiatan mendatang
- **Jadwal Ibadah**: CRUD jadwal ibadah rutin
- **Jadwal Pengkhotbah**:
  - Generate jadwal otomatis untuk satu bulan dari jadwal ibadah
  - Filter bulan tanpa perlu klik tombol
  - Pemisah per minggu
  - Mengingat bulan terakhir yang dibuka
- **Artikel & Kategori Artikel**: CRUD artikel dan kategori
- **Kegiatan**: CRUD event dan acara
- **Data Jemaat**: CRUD data anggota jemaat
- **Pesan Masuk**: pesan dari form kontak, dengan badge jumlah pesan belum dibaca
- **Rekening Donasi**: kelola rekening dan gambar QRIS
- **Pengaturan Website**:
  - Identitas gereja, logo (otomatis dipakai sebagai favicon), dan **warna tema**
  - Hero, kontak, donasi, media sosial, dan halaman Tentang
- **Kelola Admin & Log Aktivitas** (khusus Super Admin)
- **Tampilan mobile**: bottom navigation, dan tabel otomatis berubah menjadi kartu di layar kecil

### Keamanan
- ✅ SQL Injection Protection (PDO Prepared Statements)
- ✅ XSS Protection (`htmlspecialchars` lewat helper `e()`)
- ✅ CSRF Token pada semua form
- ✅ Secure Session (regenerate ID, HttpOnly, SameSite)
- ✅ Password Hashing (bcrypt)
- ✅ Rate Limiting pada login
- ✅ Captcha pada form kontak
- ✅ Role-Based Access Control (RBAC)

## 🛠️ Teknologi

- **Backend**: PHP 8.0+ (Native MVC, tanpa framework)
- **Database**: MySQL 5.7+ / 8.0 / MariaDB
- **Frontend**: Bootstrap 5.3, Bootstrap Icons, vanilla JavaScript
- **Font**: Plus Jakarta Sans (Google Fonts)
- **Container** (opsional): Docker + Docker Compose (PHP 8.3 + Apache)

## 📦 Instalasi

Ada tiga cara menjalankan aplikasi. Pilih salah satu.

### Opsi A: Docker, pakai MySQL yang sudah ada di komputer

Cocok untuk development, jika MySQL sudah terpasang di komputer (misalnya dari XAMPP atau Laragon).

1. Buat database dan import skema (lihat [Database](#-database)).
2. Pastikan user dan password MySQL di [config/config.php](config/config.php) sesuai.
3. Jalankan:
   ```bash
   docker compose up -d --build
   ```
4. Akses:
   - Website: http://localhost:8080/project-website/siseeng/
   - Admin: http://localhost:8080/project-website/siseeng/auth/login
   - phpMyAdmin: http://localhost:8081 (terhubung ke MySQL di komputer)

Container web terhubung ke MySQL di komputer lewat `host.docker.internal`.

### Opsi B: Docker lengkap (Apache + PHP + MySQL + phpMyAdmin)

Cocok untuk komputer yang belum punya MySQL, misalnya komputer gereja.

```bash
docker compose -f docker-compose.gereja.yml up -d --build
```

- Saat pertama kali dijalankan, database dibuat dan diisi otomatis dari `database/deploy.sql`. Import ini hanya terjadi sekali, selama volume `db_data` masih kosong.
- Semua container otomatis hidup lagi setelah komputer restart (`restart: unless-stopped`).
- Nilai `MYSQL_DATABASE` dan `MYSQL_ROOT_PASSWORD` di `docker-compose.gereja.yml` **harus sama** dengan `DB_NAME` dan `DB_PASS` di `config/config.php`.
- Jika website ingin dibuka dari perangkat lain di jaringan gereja, ganti `APP_URL` di compose dengan IP komputer tersebut, misalnya `http://192.168.1.10:8080/project-website/siseeng`.

Alamat akses sama dengan Opsi A.

### Opsi C: Manual (XAMPP / Laragon / Apache)

1. Salin project ke folder web server, misalnya `htdocs/project-website/siseeng`.
2. Buat database dan import skema (lihat [Database](#-database)).
3. Sesuaikan [config/config.php](config/config.php) (lihat [Konfigurasi](#-konfigurasi)).
4. Pastikan `mod_rewrite` aktif dan `AllowOverride All`.
5. Pastikan folder `public/uploads/` bisa ditulis oleh web server.
6. Akses `http://localhost/project-website/siseeng/`.

> Jika project diletakkan di path lain, sesuaikan juga `RewriteBase` di [public/.htaccess](public/.htaccess).

### Persyaratan (untuk instalasi manual)
- PHP 8.0+ dengan ekstensi `pdo_mysql`
- MySQL 5.7+ / MariaDB 10.3+
- Apache dengan `mod_rewrite` (dan `mod_headers` untuk security header)

## 🗄️ Database

File skema lengkap yang disarankan adalah **`database/deploy.sql`**. File ini sudah mencakup semua tabel, termasuk jadwal pengkhotbah, kategori artikel, rekening donasi, dan settings.

```sql
CREATE DATABASE db_siseeng CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
```bash
mysql -u root -p db_siseeng < database/deploy.sql
```

Atau import lewat phpMyAdmin. File `add_*.sql` dan file SQL per fitur di folder `database/` adalah migrasi untuk database lama.

### Default Admin Login
- **Email**: admin@gbiciseeng.com
- **Password**: admin123

⚠️ **PENTING**: Segera ubah password setelah login pertama!

## ⚙️ Konfigurasi

Semua pengaturan ada di [config/config.php](config/config.php). Nilai database dan URL bisa di-override lewat environment variable. Docker memakai mekanisme ini, sehingga instalasi manual tetap memakai nilai default di file.

| Konstanta | Env var | Default | Keterangan |
|---|---|---|---|
| `APP_URL` | `APP_URL` | `http://localhost/project-website/siseeng` | URL dasar aplikasi |
| `DB_HOST` | `DB_HOST` | `localhost` | Host MySQL |
| `DB_NAME` | `DB_NAME` | `db_siseeng` | Nama database |
| `DB_USER` | `DB_USER` | `root` | User MySQL |
| `DB_PASS` | `DB_PASS` | `root` | Password MySQL |

Pengaturan lain di file yang sama:

```php
define('ENVIRONMENT', 'development');   // 'production' untuk menyembunyikan error
date_default_timezone_set('Asia/Jakarta'); // Zona waktu WIB

define('SESSION_LIFETIME', 7200);       // 2 jam
define('CSRF_TOKEN_EXPIRE', 3600);      // 1 jam
define('LOGIN_MAX_ATTEMPTS', 5);        // Maksimal percobaan login
define('LOGIN_LOCKOUT_TIME', 60);       // Lama blokir (detik)
```

## 🎨 Kustomisasi Tampilan

Semua kustomisasi berikut bisa dilakukan dari **Admin → Pengaturan → tab Umum**, tanpa mengubah kode.

- **Warna tema**: pilih dari 10 preset atau warna kustom (color picker / kode hex).
  - Warna turunan (gelap, terang, latar muda) dihitung otomatis.
  - Berlaku untuk website publik, halaman login, dan panel admin.
  - Pratinjau langsung tampil sebelum disimpan, dan ada peringatan jika warnanya terlalu terang untuk teks putih.
- **Logo**: dipakai di header website dan otomatis menjadi **favicon** serta ikon *Add to Home Screen*. Logo berbentuk persegi akan tampil paling rapi sebagai favicon.

Warna tema disimpan sebagai setting `theme_color`, lalu dirender oleh helper `themeStyleTag()` di [core/Helpers.php](core/Helpers.php) sebagai CSS variable (`--brand`, `--brand-dark`, `--brand-soft`, dan lainnya).

## 📁 Struktur Folder

```
siseeng/
├── app/
│   ├── Controllers/            # Controller publik
│   │   └── Admin/              # Controller panel admin
│   ├── Middleware/             # AuthMiddleware, RoleMiddleware (RBAC & menu)
│   └── Models/                 # Model
├── config/
│   └── config.php              # Konfigurasi aplikasi
├── core/
│   ├── App.php                 # Router & daftar route
│   ├── Controller.php          # Base controller
│   ├── Database.php            # Wrapper PDO
│   ├── Helpers.php             # Helper (url, e, setting, tema, favicon, dll)
│   ├── Model.php               # Base model
│   ├── Security.php            # Utilitas keamanan
│   └── Session.php             # Manajemen session & flash
├── database/
│   ├── deploy.sql              # Skema lengkap (disarankan)
│   ├── schema.sql              # Skema awal
│   └── *.sql                   # Migrasi per fitur
├── public/
│   ├── assets/
│   │   ├── css/style.css       # Stylesheet website publik
│   │   ├── css/admin.css       # Stylesheet panel admin
│   │   ├── js/                 # JavaScript
│   │   └── images/
│   ├── uploads/                # File upload (logo, hero, QRIS, dll)
│   ├── index.php               # Entry point
│   └── .htaccess               # URL rewriting
├── views/
│   ├── admin/                  # View admin
│   ├── auth/                   # Halaman login
│   ├── errors/                 # Halaman error
│   ├── layouts/                # Layout public & admin
│   └── public/                 # View website publik
├── Dockerfile                  # PHP 8.3 + Apache + pdo_mysql
├── docker-compose.yml          # Web + phpMyAdmin (MySQL di komputer)
├── docker-compose.gereja.yml   # Web + MySQL + phpMyAdmin (lengkap)
└── README.md
```

## 🔐 Role & Permission

### Super Admin
- Semua akses Admin
- Kelola Admin (CRUD & aktif/nonaktifkan admin lain)
- Lihat & bersihkan Log Aktivitas

### Admin
- Jadwal Ibadah & Jadwal Pengkhotbah
- Artikel & Kategori Artikel
- Kegiatan
- Data Jemaat
- Pesan Masuk
- Rekening Donasi
- Pengaturan Website

## 📝 Routes

Semua route didefinisikan di [core/App.php](core/App.php). Beberapa modul admin juga menerima alias berbahasa Inggris, misalnya `admin/articles` dan `admin/events`.

### Public
| URL | Controller |
|-----|------------|
| `/` | HomeController@index |
| `/tentang` | AboutController@index |
| `/tentang/visi-misi` | AboutController@visiMisi |
| `/tentang/sejarah` | AboutController@sejarah |
| `/jadwal-pengkhotbah` | HomeController@preacherSchedule |
| `/kegiatan` | EventController@index |
| `/kegiatan/detail/{slug}` | EventController@detail |
| `/artikel` | ArticleController@index |
| `/artikel/baca/{slug}` | ArticleController@read |
| `/donasi` | DonationController@index |
| `/kontak` | ContactController@index |
| `/kontak/kirim` (POST) | ContactController@send |
| `/auth/login` | AuthController@login |
| `/auth/proses-login` (POST) | AuthController@processLogin |
| `/auth/logout` (POST) | AuthController@logout |

### Admin
| URL | Controller |
|-----|------------|
| `/admin/dashboard` | Admin\DashboardController |
| `/admin/jadwal` | Admin\ScheduleController |
| `/admin/jadwal-pengkhotbah` | Admin\PreacherScheduleController |
| `/admin/artikel` | Admin\ArticleController |
| `/admin/kategori-artikel` | Admin\ArticleCategoryController |
| `/admin/kegiatan` | Admin\EventController |
| `/admin/jemaat` | Admin\MemberController |
| `/admin/pesan` | Admin\MessageController |
| `/admin/rekening-donasi` | Admin\DonationAccountController |
| `/admin/pengaturan` | Admin\SettingController |
| `/admin/users` | Admin\UserController (Super Admin) |
| `/admin/logs` | Admin\ActivityLogController (Super Admin) |

## 🤝 Kontribusi

1. Fork repository
2. Buat branch baru (`git checkout -b feature/NamaFitur`)
3. Commit perubahan (`git commit -m 'feat: tambah NamaFitur'`)
4. Push ke branch (`git push origin feature/NamaFitur`)
5. Buka Pull Request

## 📄 Lisensi

Didistribusikan di bawah MIT License. Lihat [LICENSE](LICENSE) untuk detailnya.

## 📞 Kontak & Support

Untuk pertanyaan dan dukungan, silakan buka issue di repository ini.

---

**SISEENG** - Dikembangkan dengan ❤️ untuk pelayanan gereja.

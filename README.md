# SISEENG - Sistem Informasi Manajemen Gereja

![Version](https://img.shields.io/badge/version-1.0.0-blue)
![PHP](https://img.shields.io/badge/PHP-8.0+-purple)
![License](https://img.shields.io/badge/license-MIT-green)

Sistem Informasi Manajemen Gereja (SISEENG) adalah aplikasi web berbasis PHP Native dengan arsitektur MVC yang dirancang untuk membantu gereja dalam mengelola informasi dan kegiatan jemaat.

## 📋 Fitur Utama

### Website Publik
- **Beranda** - Tampilan informasi utama gereja
- **Tentang Kami** - Profil, visi-misi, dan sejarah gereja
- **Jadwal Ibadah** - Informasi waktu dan lokasi ibadah
- **Kegiatan/Acara** - Daftar event dan kegiatan gereja
- **Artikel/Renungan** - Artikel rohani dan renungan harian
- **Donasi** - Informasi rekening dan QRIS untuk donasi
- **Kontak** - Form kontak dan informasi lokasi gereja

### Panel Admin
- **Dashboard** - Ringkasan statistik dan aktivitas
- **Manajemen User** - CRUD admin (Super Admin only)
- **Jadwal Ibadah** - CRUD jadwal ibadah rutin
- **Artikel** - CRUD artikel dan renungan
- **Kegiatan** - CRUD event dan acara
- **Data Jemaat** - CRUD data anggota jemaat
- **Pesan Masuk** - Kelola pesan dari form kontak
- **Log Aktivitas** - Riwayat aktivitas admin (Super Admin only)

### Keamanan
- ✅ SQL Injection Protection (PDO Prepared Statements)
- ✅ XSS Protection (htmlspecialchars)
- ✅ CSRF Token pada semua form
- ✅ Secure Session (regenerate_id, HttpOnly, SameSite)
- ✅ Password Hashing (bcrypt)
- ✅ Rate Limiting pada login
- ✅ Role-Based Access Control (RBAC)

## 🛠️ Teknologi

- **Backend**: PHP 8.0+ (Native MVC, tanpa framework berat)
- **Database**: MySQL 5.7+ / MariaDB
- **Frontend**: Bootstrap 5.3, Bootstrap Icons
- **Font**: Google Fonts (Inter, Merriweather)

## 📦 Instalasi

### Persyaratan Sistem
- PHP 8.0 atau lebih tinggi
- MySQL 5.7+ / MariaDB 10.3+
- Apache/Nginx dengan mod_rewrite
- Composer (opsional)

### Langkah Instalasi

1. **Clone atau Download Repository**
   ```bash
   git clone https://github.com/username/siseeng.git
   # atau extract ZIP ke folder htdocs/www
   ```

2. **Konfigurasi Database**
   - Buat database baru di MySQL:
     ```sql
     CREATE DATABASE siseeng CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
     ```
   - Import schema database:
     ```bash
     mysql -u root -p siseeng < database/schema.sql
     ```

3. **Konfigurasi Aplikasi**
   - Buka file `config/config.php`
   - Sesuaikan konfigurasi database:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'siseeng');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     ```
   - Sesuaikan `BASE_URL` sesuai environment:
     ```php
     define('BASE_URL', 'http://localhost/siseeng/');
     ```

4. **Set Permissions**
   ```bash
   chmod 755 -R /path/to/siseeng
   chmod 777 -R /path/to/siseeng/public/assets/uploads
   ```

5. **Akses Aplikasi**
   - Website: `http://localhost/siseeng/`
   - Admin: `http://localhost/siseeng/login`

### Default Admin Login
- **Email**: admin@siseeng.com
- **Password**: admin123

⚠️ **PENTING**: Segera ubah password setelah login pertama!

## 📁 Struktur Folder

```
siseeng/
├── app/
│   ├── Controllers/        # Controller classes
│   │   ├── Admin/          # Admin controllers
│   │   └── ...             # Public controllers
│   ├── Middleware/         # Middleware (Auth, RBAC)
│   └── Models/             # Model classes
├── config/
│   └── config.php          # Konfigurasi aplikasi
├── core/
│   ├── App.php             # Router/Application core
│   ├── Controller.php      # Base controller
│   ├── Database.php        # Database wrapper
│   ├── Helpers.php         # Helper functions
│   ├── Model.php           # Base model
│   ├── Security.php        # Security utilities
│   └── Session.php         # Session management
├── database/
│   └── schema.sql          # Database schema
├── public/
│   ├── assets/
│   │   ├── css/            # Stylesheets
│   │   ├── js/             # JavaScript files
│   │   ├── images/         # Image assets
│   │   └── uploads/        # User uploads
│   ├── index.php           # Entry point
│   └── .htaccess           # URL rewriting
├── views/
│   ├── admin/              # Admin views
│   ├── auth/               # Auth views (login)
│   ├── errors/             # Error pages
│   ├── layouts/            # Layout templates
│   └── public/             # Public views
└── README.md
```

## 🔐 Role & Permission

### Super Admin
- Semua akses Admin
- Manajemen User (CRUD admin lain)
- Lihat Log Aktivitas
- Toggle status user

### Admin
- CRUD Jadwal Ibadah
- CRUD Artikel
- CRUD Kegiatan
- CRUD Data Jemaat
- Lihat & Balas Pesan

## 🔧 Konfigurasi Lanjutan

### Rate Limiting
Edit di `config/config.php`:
```php
define('RATE_LIMIT_ATTEMPTS', 5);    // Max attempts
define('RATE_LIMIT_DECAY', 60);       // Cooldown (seconds)
```

### Session Security
```php
define('SESSION_LIFETIME', 3600);     // 1 hour
define('SESSION_REGENERATE_TIME', 300); // 5 minutes
```

### CSRF Token
```php
define('CSRF_TOKEN_EXPIRE', 3600);    // 1 hour
```

## 📝 API Routes

### Public Routes
| Method | URL | Controller |
|--------|-----|------------|
| GET | / | HomeController@index |
| GET | /tentang | AboutController@index |
| GET | /tentang/visi-misi | AboutController@visiMisi |
| GET | /tentang/sejarah | AboutController@sejarah |
| GET | /kegiatan | EventController@index |
| GET | /kegiatan/{id} | EventController@detail |
| GET | /artikel | ArticleController@index |
| GET | /artikel/{slug} | ArticleController@read |
| GET | /donasi | DonationController@index |
| GET | /kontak | ContactController@index |
| POST | /kontak/kirim | ContactController@send |
| GET | /login | AuthController@login |
| POST | /login | AuthController@authenticate |
| GET | /logout | AuthController@logout |

### Admin Routes
| Method | URL | Controller |
|--------|-----|------------|
| GET | /admin | DashboardController@index |
| GET | /admin/users | UserController@index |
| GET | /admin/schedules | ScheduleController@index |
| GET | /admin/articles | ArticleController@index |
| GET | /admin/events | EventController@index |
| GET | /admin/members | MemberController@index |
| GET | /admin/messages | MessageController@index |
| GET | /admin/logs | ActivityLogController@index |

## 🤝 Kontribusi

Kontribusi sangat diterima! Silakan:

1. Fork repository
2. Buat branch baru (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buka Pull Request

## 📄 Lisensi

Distributed under the MIT License. See `LICENSE` for more information.

## 📞 Kontak & Support

Untuk pertanyaan dan dukungan, silakan buka issue di repository ini.

---

**SISEENG** - Dikembangkan dengan ❤️ untuk pelayanan gereja.

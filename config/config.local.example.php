<?php
/**
 * Contoh konfigurasi lokal per server.
 *
 * Salin file ini menjadi config/config.local.php lalu isi nilainya.
 * config.local.php di-ignore git, jadi kredensial tidak ikut ter-commit.
 * Environment variable dengan nama yang sama tetap lebih diutamakan.
 * Key yang dihapus dari sini memakai default di config.php.
 */
return [
    // 'development' menampilkan detail error; server live WAJIB 'production'
    'APP_ENV'  => 'development',
    'APP_URL'  => 'http://localhost/siseeng',

    'DB_HOST'  => 'localhost',
    'DB_NAME'  => 'db_siseeng',
    'DB_USER'  => 'siseeng',
    'DB_PASS'  => 'ganti-dengan-password-kuat',
];

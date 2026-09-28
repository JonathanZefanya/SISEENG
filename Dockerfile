FROM php:8.3-apache

# Ekstensi PHP untuk koneksi MySQL via PDO
RUN docker-php-ext-install pdo_mysql

# Aktifkan mod_rewrite & mod_headers, izinkan .htaccess
RUN a2enmod rewrite headers \
    && sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Batas upload (video latar hero maks 20MB + gambar-gambar lain dalam satu form)
# display_errors=Off: warning bawaan PHP sebelum aplikasi berjalan (mis. upload terlalu besar)
# tidak dicetak ke halaman; di mode development aplikasi menyalakannya lagi (config/config.php)
RUN { echo 'upload_max_filesize=25M'; echo 'post_max_size=45M'; echo 'display_errors=Off'; echo 'log_errors=On'; } > /usr/local/etc/php/conf.d/uploads.ini

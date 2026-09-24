FROM php:8.3-apache

# Ekstensi PHP untuk koneksi MySQL via PDO
RUN docker-php-ext-install pdo_mysql

# Aktifkan mod_rewrite & mod_headers, izinkan .htaccess
RUN a2enmod rewrite headers \
    && sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

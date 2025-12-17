FROM php:8.2-apache

# Enable required Apache modules FIRST (no restart)
RUN a2enmod rewrite headers

# Disable all MPMs, then enable prefork ONLY
RUN a2dismod mpm_event mpm_worker mpm_prefork || true \
 && a2enmod mpm_prefork

# PHP extensions for WordPress
RUN docker-php-ext-install mysqli pdo pdo_mysql

# PHP upload limits
RUN echo "upload_max_filesize=64M" > /usr/local/etc/php/conf.d/uploads.ini \
 && echo "post_max_size=64M" >> /usr/local/etc/php/conf.d/uploads.ini \
 && echo "memory_limit=256M" >> /usr/local/etc/php/conf.d/uploads.ini

WORKDIR /var/www/html

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html

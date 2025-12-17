FROM php:8.2-apache

# --- HARD RESET APACHE MPMs ---
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load \
 && rm -f /etc/apache2/mods-enabled/mpm_*.conf \
 && rm -f /etc/apache2/mods-available/mpm_*.conf \
 && rm -f /etc/apache2/mods-available/mpm_*.load

# Re-enable ONLY prefork
RUN a2enmod mpm_prefork

# Required Apache modules
RUN a2enmod rewrite headers

# PHP extensions for WordPress
RUN docker-php-ext-install mysqli pdo pdo_mysql

# PHP limits
RUN echo "upload_max_filesize=64M" > /usr/local/etc/php/conf.d/uploads.ini \
 && echo "post_max_size=64M" >> /usr/local/etc/php/conf.d/uploads.ini \
 && echo "memory_limit=256M" >> /usr/local/etc/php/conf.d/uploads.ini

WORKDIR /var/www/html
COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html

FROM php:8.2-apache

# Enable required Apache modules only
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

# Force Apache to use prefork at runtime (without a2enmod)
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

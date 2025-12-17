FROM php:8.2-apache

# Disable all other MPMs and enable prefork ONLY
RUN a2dismod mpm_event mpm_worker || true \
    && a2enmod mpm_prefork

# Enable required Apache modules
RUN a2enmod rewrite headers

# PHP extensions for WordPress
RUN docker-php-ext-install mysqli pdo pdo_mysql

# PHP upload limits (safe defaults)
RUN echo "upload_max_filesize=64M" > /usr/local/etc/php/conf.d/uploads.ini \
 && echo "post_max_size=64M" >> /usr/local/etc/php/conf.d/uploads.ini \
 && echo "memory_limit=256M" >> /usr/local/etc/php/conf.d/uploads.ini

# Set working directory
WORKDIR /var/www/html

# Copy WordPress files
COPY . /var/www/html

# Fix permissions
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html

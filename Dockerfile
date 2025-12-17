# Use the official WordPress PHP-FPM image
FROM wordpress:php8.2-fpm

# PHP extensions needed by WordPress
RUN docker-php-ext-install mysqli pdo pdo_mysql

# PHP configuration for uploads
RUN echo "upload_max_filesize=64M" > /usr/local/etc/php/conf.d/uploads.ini \
 && echo "post_max_size=64M" >> /usr/local/etc/php/conf.d/uploads.ini \
 && echo "memory_limit=256M" >> /usr/local/etc/php/conf.d/uploads.ini

# Set working directory
WORKDIR /var/www/html

# Copy WordPress files from repo
COPY . /var/www/html

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html

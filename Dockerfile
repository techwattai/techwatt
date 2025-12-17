FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    curl \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# PHP config
RUN echo "upload_max_filesize=64M" > /usr/local/etc/php/conf.d/uploads.ini \
 && echo "post_max_size=64M" >> /usr/local/etc/php/conf.d/uploads.ini \
 && echo "memory_limit=256M" >> /usr/local/etc/php/conf.d/uploads.ini

# Nginx config
COPY nginx.conf /etc/nginx/conf.d/default.conf

# WordPress files
WORKDIR /var/www/html
COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html

EXPOSE 8080

CMD service nginx start && php-fpm

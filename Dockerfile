FROM php:8.2-fpm

# Install system dependencies (KEEP nginx!)
RUN apt-get update && apt-get install -y \
    nginx \
    curl \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libfreetype6-dev \
    libzip-dev \
    git \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install \
        gd \
        mysqli \
        pdo \
        pdo_mysql \
        zip \
    && rm -rf /var/lib/apt/lists/*

# PHP config
RUN echo "upload_max_filesize=64M" > /usr/local/etc/php/conf.d/uploads.ini \
 && echo "post_max_size=64M" >> /usr/local/etc/php/conf.d/uploads.ini \
 && echo "memory_limit=256M" >> /usr/local/etc/php/conf.d/uploads.ini

# Nginx config (KEEP your existing one)
COPY nginx.conf /etc/nginx/conf.d/default.conf

# WordPress files
WORKDIR /var/www/html
COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html

EXPOSE 8080

# Start BOTH services correctly
CMD sh -c "php-fpm -D && nginx -g 'daemon off;'"

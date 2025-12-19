FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
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

# Nginx config
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Supervisor config
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# App files
WORKDIR /var/www/html
COPY . /var/www/html

# Ensure wp-content exists for volume
RUN mkdir -p /var/www/html/wp-content/uploads \
 && chown -R www-data:www-data /var/www/html \
 && chmod -R 775 /var/www/html/wp-content

# Copy wp-content into volume if volume is empty
RUN mkdir -p /docker-wp-content
COPY wp-content /docker-wp-content

EXPOSE 8080

CMD ["/usr/bin/supervisord", "-n"]

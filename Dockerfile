FROM php:8.2-fpm

# Install system dependencies + Imagick
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
    libmagickwand-dev \
    imagemagick \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
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
RUN echo "upload_max_filesize=128M" > /usr/local/etc/php/conf.d/uploads.ini \
 && echo "post_max_size=128M" >> /usr/local/etc/php/conf.d/uploads.ini \
 && echo "memory_limit=1024M" >> /usr/local/etc/php/conf.d/uploads.ini \ 
 && echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/uploads.ini \
 && echo "max_input_time=300" >> /usr/local/etc/php/conf.d/uploads.ini


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

CMD sh -c "chown -R www-data:www-data /var/www/html/wp-content/uploads \
 && chmod -R 775 /var/www/html/wp-content/uploads \
 && /usr/bin/supervisord -n"


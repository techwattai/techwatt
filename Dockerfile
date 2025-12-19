# Base PHP-FPM image
FROM php:8.2-fpm

# -----------------------------
# 1️⃣ Install system dependencies + Imagick
# -----------------------------
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    curl \
    unzip \
    git \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libfreetype6-dev \
    libzip-dev \
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

# -----------------------------
# 2️⃣ PHP settings for large uploads
# -----------------------------
RUN echo "upload_max_filesize=128M" > /usr/local/etc/php/conf.d/uploads.ini \
 && echo "post_max_size=128M" >> /usr/local/etc/php/conf.d/uploads.ini \
 && echo "memory_limit=1024M" >> /usr/local/etc/php/conf.d/uploads.ini \
 && echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/uploads.ini \
 && echo "max_input_time=300" >> /usr/local/etc/php/conf.d/uploads.ini

# -----------------------------
# 3️⃣ Nginx config
# -----------------------------
COPY nginx.conf /etc/nginx/conf.d/default.conf

# -----------------------------
# 4️⃣ Supervisor config
# -----------------------------
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# -----------------------------
# 5️⃣ App files
# -----------------------------
WORKDIR /var/www/html
COPY . /var/www/html

# Ensure wp-content/uploads exists for volume
RUN mkdir -p /var/www/html/wp-content/uploads \
 && chown -R www-data:www-data /var/www/html \
 && chmod -R 775 /var/www/html/wp-content

# -----------------------------
# 6️⃣ Expose port
# -----------------------------
EXPOSE 8080

# -----------------------------
# 7️⃣ CMD: fix permissions on mounted volume & start services
# -----------------------------
CMD sh -c "\
    if [ ! -d /var/www/html/wp-content/uploads ]; then mkdir -p /var/www/html/wp-content/uploads; fi && \
    chown -R www-data:www-data /var/www/html/wp-content/uploads && \
    chmod -R 775 /var/www/html/wp-content/uploads && \
    /usr/bin/supervisord -n"

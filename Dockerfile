# Use an official WordPress image
FROM wordpress:php8.2-apache

# Enable required Apache modules
RUN a2enmod rewrite

# Disable all MPMs, then enable ONLY prefork
RUN a2dismod mpm_event mpm_worker || true \
 && a2enmod mpm_prefork

# Copy custom Apache config
COPY apache.conf /etc/apache2/conf-available/custom.conf
RUN a2enconf custom

# Copy PHP configuration
#COPY php.ini /usr/local/etc/php/conf.d/custom.ini

# Set permissions (important for uploads)
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

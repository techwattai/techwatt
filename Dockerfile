# Use an official WordPress image
FROM wordpress:php8.2-apache

# Copy all local files into container
COPY . /var/www/html

# Give ownership to www-data user (required by WordPress)
RUN chown -R www-data:www-data /var/www/html

# Expose default web port
EXPOSE 80

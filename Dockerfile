# Use an official WordPress image
 FROM wordpress:php8.2-apache

# ✅ Fix Apache MPM conflict (WordPress needs prefork)
RUN a2dismod mpm_event mpm_worker \&& a2enmod mpm_prefork

# Copy all local files into container
COPY . /var/www/html

# Give ownership to www-data user (required by WordPress)
RUN chown -R www-data:www-data /var/www/html

# Expose default web port
EXPOSE 80

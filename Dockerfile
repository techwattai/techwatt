FROM wordpress:php8.2-apache

# 🔥 FORCE Apache to use only prefork (WordPress requirement)
RUN rm -f /etc/apache2/mods-enabled/mpm_event.load \
          /etc/apache2/mods-enabled/mpm_worker.load \
 && ln -sf /etc/apache2/mods-available/mpm_prefork.load \
           /etc/apache2/mods-enabled/mpm_prefork.load

# Enable rewrite (WordPress needs this)
RUN a2enmod rewrite

# Copy WordPress files
COPY . /var/www/html

# Permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

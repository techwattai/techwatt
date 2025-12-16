FROM wordpress:php8.2-apache

# 🔥 NUKE all MPMs completely
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load \
          /etc/apache2/mods-enabled/mpm_*.conf \
          /etc/apache2/mods-available/mpm_event.* \
          /etc/apache2/mods-available/mpm_worker.*

# ✅ Re-enable ONLY prefork
RUN ln -s /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load \
 && ln -s /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf

# WordPress needs rewrite
RUN a2enmod rewrite

# Force Apache to listen on Railway port
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf \
 && sed -i 's/:80/:8080/' /etc/apache2/sites-enabled/000-default.conf

COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html

EXPOSE 8080

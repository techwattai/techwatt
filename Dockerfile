FROM wordpress:php8.2-apache

# 🔥 Ensure only prefork MPM (WordPress requirement)
RUN rm -f /etc/apache2/mods-enabled/mpm_event.load \
          /etc/apache2/mods-enabled/mpm_worker.load \
 && ln -sf /etc/apache2/mods-available/mpm_prefork.load \
           /etc/apache2/mods-enabled/mpm_prefork.load

RUN a2enmod rewrite

# 🔥 Make Apache listen on Railway PORT (8080)
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf \
 && sed -i 's/:80/:8080/' /etc/apache2/sites-enabled/000-default.conf

COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html

EXPOSE 8080

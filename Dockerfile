FROM php:8.2-apache

# Enable mod_rewrite (optional)
RUN a2enmod rewrite

# Set Apache to use Railway PORT
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Listen on all interfaces
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

COPY . /var/www/html/

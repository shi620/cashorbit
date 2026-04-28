FROM php:8.2-apache

ENV PORT=80
EXPOSE 80

COPY . /var/www/html/

CMD ["apache2-foreground"]

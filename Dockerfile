FROM php:8.2-apache

# (optional) DB के लिए
RUN docker-php-ext-install pdo pdo_mysql

COPY . /var/www/html/

FROM php:8.2-apache

# (optional) useful PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

COPY . /var/www/html/

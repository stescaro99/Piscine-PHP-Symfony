FROM php:8.2-apache

RUN apt-get update && \
    apt-get install -y libicu-dev git unzip && \
    docker-php-ext-install intl

RUN a2enmod rewrite

WORKDIR /var/www/html

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html/public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf
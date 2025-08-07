FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && \
    apt-get install -y libicu-dev zip unzip git curl && \
    docker-php-ext-install intl pdo pdo_mysql

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy project files
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html

CMD ["php-fpm"]

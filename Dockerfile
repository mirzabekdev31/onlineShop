FROM php:8.3-fpm

# System dependencies
RUN apt-get update && apt-get install -y \
    nginx unzip git curl libzip-dev libpng-dev libonig-dev libxml2-dev libicu-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Project files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Permissions
RUN chmod -R 775 storage bootstrap/cache && \
    chown -R www-data:www-data /var/www

# Nginx config
COPY nginx.conf /etc/nginx/nginx.conf

# Entrypoint
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 8080

CMD ["/entrypoint.sh"]

FROM php:8.3-fpm

# MySQL PDO va mysqli drayverlarini o‘rnatish
RUN docker-php-ext-install pdo pdo_mysql mysqli

# PHP intl extension (Number format uchun kerak)
RUN apt-get update && apt-get install -y libicu-dev && docker-php-ext-install intl

# Loyihaning ishchi katalogini belgilash
WORKDIR /var/www

# Loyiha fayllarini konteyner ichiga nusxalash
COPY . .

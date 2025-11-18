#!/bin/bash
set -e

echo "=== Starting Laravel App ==="

php artisan config:clear
php artisan cache:clear

echo "=== Waiting for database ==="
for i in {1..30}; do
    if php artisan migrate:status >/dev/null 2>&1; then
        echo "DB ready"
        break
    fi
    echo "Waiting... ($i/30)"
    sleep 2
done

php artisan migrate --force
php artisan config:cache

# Start services
php-fpm -D
echo "PHP-FPM started"

nginx -g "daemon off;"

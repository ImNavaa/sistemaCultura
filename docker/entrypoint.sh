#!/bin/bash
set -e

# The persistent disk mounts at storage/app; make sure the directory
# tree Laravel expects exists on it (empty on first boot) and is
# writable by the webserver user.
mkdir -p storage/app/public storage/app/private
chown -R www-data:www-data storage bootstrap/cache

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan migrate --force
php artisan db:seed --force
php artisan storage:link || true

php-fpm -D
exec nginx -g 'daemon off;'

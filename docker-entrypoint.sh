#!/bin/bash
set -e

# Support dynamic PORT environment variable (Render / Railway / Heroku)
if [ -n "$PORT" ]; then
    sed -i "s/80/$PORT/g" /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf
fi

# Ensure storage link
php artisan storage:link --no-interaction || true

# Optimize cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations and seeders safely
php artisan migrate --force --seed || true

# Execute Apache in foreground
exec apache2-foreground


#!/bin/bash
set -e

echo "[*] Starting entrypoint script..."

# Navigate to application directory
cd /var/www/app

# Wait a moment for filesystem to be ready
sleep 1

# Ensure database file exists with correct permissions
if [ ! -f /var/www/app/database/database.sqlite ]; then
    echo "[*] Creating SQLite database file..."
    touch /var/www/app/database/database.sqlite
fi

chown www-data:www-data /var/www/app/database/database.sqlite
chmod 664 /var/www/app/database/database.sqlite

# Run migrations
echo "[*] Running database migrations..."
php artisan migrate --force || true

# Run seeders
echo "[*] Running database seeders..."
php artisan db:seed --force || true

# Clear and cache configurations
echo "[*] Optimizing Laravel..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Create storage link if not exists
if [ ! -L /var/www/app/public/storage ]; then
    echo "[*] Creating storage link..."
    php artisan storage:link || true
fi

# Set correct permissions
echo "[*] Setting permissions..."
chown -R www-data:www-data /var/www/app/storage
chown -R www-data:www-data /var/www/app/bootstrap/cache
chown -R www-data:www-data /var/www/app/database
chmod -R 775 /var/www/app/storage
chmod -R 775 /var/www/app/bootstrap/cache

# Display environment info
echo "[*] Application Environment: $(php artisan env)"
echo "[*] Application URL: $(grep APP_URL .env | cut -d '=' -f2)"
echo "[*] Debug Mode: $(grep APP_DEBUG .env | cut -d '=' -f2)"

echo "[*] Starting services via supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf


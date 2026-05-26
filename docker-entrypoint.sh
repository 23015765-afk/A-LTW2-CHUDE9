#!/bin/bash
set -e

echo "=========================================="
echo "  Du Lich Viet — Starting up..."
echo "=========================================="

# Tạo APP_KEY nếu chưa có
if [ -z "$APP_KEY" ]; then
    echo ">> Generating APP_KEY..."
    php artisan key:generate --force
fi

# Cache config, routes, views
echo ">> Caching config, routes, views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Chạy database migrations
echo ">> Running database migrations..."
php artisan migrate --force

# Chỉ seed nếu bảng users chưa có dữ liệu (tránh xóa data mỗi lần restart)
USER_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null | tail -1 | tr -d '[:space:]')
if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then
    echo ">> Seeding database (first run)..."
    php artisan db:seed --force
else
    echo ">> Database already has data (users: $USER_COUNT), skipping seed."
fi

# Tạo storage symlink
echo ">> Creating storage symlink..."
php artisan storage:link || true

# Set permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo ">> Server is ready!"
echo "=========================================="

# Khởi động Apache
exec "$@"

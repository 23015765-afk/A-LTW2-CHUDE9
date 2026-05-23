# ============================================================
# Stage 1: Build frontend assets (Node.js)
# ============================================================
FROM node:20-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json* ./
RUN npm install

COPY resources/ resources/
COPY vite.config.js ./
COPY public/ public/

RUN npm run build

# ============================================================
# Stage 2: PHP production image
# ============================================================
FROM php:8.2-apache AS production

# Cài đặt system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Cài Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Cấu hình Apache: DocumentRoot trỏ vào /var/www/html/public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' \
        /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|<Directory /var/www/>|<Directory /var/www/html/public>|g' \
        /etc/apache2/apache2.conf \
    && a2enmod rewrite

# Thêm cấu hình AllowOverride để .htaccess hoạt động
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

WORKDIR /var/www/html

# Copy toàn bộ source code
COPY . .

# Copy frontend build từ stage 1
COPY --from=frontend /app/public/build ./public/build

# Cài PHP dependencies (không có dev packages)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Tạo thư mục storage và set permissions
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Tạo symlink storage -> public/storage
RUN php artisan storage:link || true

# Script khởi động
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache2-foreground"]

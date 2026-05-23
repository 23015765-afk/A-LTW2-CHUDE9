# 1. Sử dụng bản PHP 8.2 kết hợp máy chủ Apache chuẩn cho Laravel
FROM php:8.2-apache

# 2. Cài đặt các thư viện hệ thống cần thiết cho Laravel chạy mượt
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl

# 3. Kích hoạt tính năng ghi đè đường dẫn (Rewrite) cực kỳ quan trọng của Laravel
RUN a2enmod rewrite

# 4. Cài đặt các extension PHP bắt buộc (PDO, MySQL, MBString...)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 5. Cài đặt công cụ Composer vào bên trong máy chủ
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Thiết lập thư mục làm việc chính
WORKDIR /var/www/html

# 7. Copy toàn bộ code từ máy em vào máy chủ Render
COPY . .

# 8. Cài đặt các thư viện code Laravel
RUN composer install --no-dev --optimize-autoloader

# 9. Trao quyền đọc ghi file cho máy chủ (Tránh lỗi 500 khi ghi log/cache)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 10. Sửa cấu hình Apache để trỏ thẳng vào thư mục public của Laravel
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# 11. Mở cổng mạng 80 để mọi người truy cập vào xem web
EXPOSE 80
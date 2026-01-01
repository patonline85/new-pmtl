FROM php:8.2-apache

# Copy toàn bộ code từ GitHub vào thư mục web của Container
COPY . /var/www/html/

# Cấp quyền cho Apache được đọc và ghi (để tránh lỗi permission sau này)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Mở cổng 80 (cổng trong container)
EXPOSE 80

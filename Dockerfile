FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev nodejs npm nginx \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .
COPY docker/Dockerfile.env .env

RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build # cache-bust-2

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

COPY docker/nginx.conf /etc/nginx/sites-available/default
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 8080

CMD ["/start.sh"]
# force-rebuild-1778074080

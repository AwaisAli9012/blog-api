FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev nodejs npm nginx \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

COPY docker/nginx.conf /etc/nginx/templates/default.conf.template

RUN a2dismod mpm_event || true

EXPOSE 80

CMD envsubst '$PORT' < /etc/nginx/templates/default.conf.template > /etc/nginx/sites-available/default && service nginx start && php-fpm

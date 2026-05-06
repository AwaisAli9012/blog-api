#!/bin/bash
echo '[www]' > /usr/local/etc/php-fpm.d/zz-docker.conf
echo 'listen = 127.0.0.1:9000' >> /usr/local/etc/php-fpm.d/zz-docker.conf
echo 'clear_env = no' >> /usr/local/etc/php-fpm.d/zz-docker.conf

cat > /var/www/html/.env << ENVEOF
APP_KEY=${APP_KEY}
APP_ENV=${APP_ENV:-production}
APP_DEBUG=${APP_DEBUG:-false}
DB_CONNECTION=${DB_CONNECTION:-mysql}
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}
SESSION_DRIVER=file
CACHE_STORE=file
ENVEOF

php-fpm -D
sleep 2
nginx -g 'daemon off;'

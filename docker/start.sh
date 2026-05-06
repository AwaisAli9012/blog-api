#!/bin/bash
echo '[www]' > /usr/local/etc/php-fpm.d/zz-listen.conf
echo 'listen = 127.0.0.1:9000' >> /usr/local/etc/php-fpm.d/zz-listen.conf
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php-fpm -D
sleep 2
nginx -g 'daemon off;'

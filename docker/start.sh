#!/bin/bash
echo '[www]' > /usr/local/etc/php-fpm.d/zz-docker.conf
echo 'listen = 127.0.0.1:9000' >> /usr/local/etc/php-fpm.d/zz-docker.conf
echo 'clear_env = no' >> /usr/local/etc/php-fpm.d/zz-docker.conf
php artisan migrate --force
php-fpm -D
sleep 2
nginx -g 'daemon off;'

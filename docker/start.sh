#!/bin/bash
sed -i 's|listen = /run/php/php8.4-fpm.sock|listen = 127.0.0.1:9000|' /usr/local/etc/php-fpm.d/www.conf 2>/dev/null || true
sed -i 's|listen = /run/php/php-fpm.sock|listen = 127.0.0.1:9000|' /usr/local/etc/php-fpm.d/www.conf 2>/dev/null || true
php-fpm -D
sleep 2
nginx -g 'daemon off;'

#!/bin/bash
php-fpm -D
sleep 2
service nginx start
wait

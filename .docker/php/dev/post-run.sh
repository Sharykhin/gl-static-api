#!/bin/sh

cd /app && composer install
php-fpm -F
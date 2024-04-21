FROM php:8.3-fpm

RUN apt-get update \
&& docker-php-ext-install pdo pdo_mysql
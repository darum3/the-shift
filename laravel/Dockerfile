ARG PHP_VERSION
FROM php:${PHP_VERSION}-fpm

RUN apt-get -y update && \
    apt-get -y install sendmail libpng-dev libzip-dev
RUN docker-php-ext-install pdo_mysql gd zip

ENV TZ Asia/Tokyo
RUN rm /etc/localtime \
    && echo "${TZ}" > /etc/timezone \
    && dpkg-reconfigure -f noninteractive tzdata

WORKDIR /laravel

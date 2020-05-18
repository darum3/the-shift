ARG PHP_VERSION
FROM php:${PHP_VERSION}

LABEL MAINTAINER Yamashita<darum@yamas.xyz>

RUN apt-get -y update && \
    apt-get -y install libpng-dev libzip-dev && \
    docker-php-ext-install pdo_mysql gd zip && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug

WORKDIR /laravel

ENTRYPOINT [ "./phpunit.sh"]

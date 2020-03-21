ARG PHP_VERSION
FROM php:${PHP_VERSION}

LABEL MAINTAINER Yamashita<darum@yamas.xyz>

RUN apt-get -y update && \
    apt-get -y install libpng-dev libzip-dev
RUN docker-php-ext-install pdo_mysql gd zip

WORKDIR /laravel

ENTRYPOINT [ "./phpunit.sh"]

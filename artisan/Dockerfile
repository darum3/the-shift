ARG PHP_VERSION
FROM php:${PHP_VERSION}

LABEL MAINTAINER Yamashita<darum@yamas.xyz>

RUN apt-get -y update && \
    apt-get -y install libpng-dev libzip-dev tzdata
RUN docker-php-ext-install pdo_mysql gd zip

ENV TZ Asia/Tokyo
RUN rm /etc/localtime \
    && echo "${TZ}" > /etc/timezone \
    && dpkg-reconfigure -f noninteractive tzdata

WORKDIR /laravel

ENTRYPOINT [ "php", "artisan" ]
CMD ["list"]

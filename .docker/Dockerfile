FROM php:8.2-fpm-alpine as php

COPY --from=composer:2.5.1 /usr/bin/composer /usr/bin/composer

RUN apk add --update linux-headers icu && \
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev gcc g++ musl-dev && \
    apk add --no-cache git && \
    docker-php-ext-configure intl && \
    docker-php-ext-install opcache intl && \
    pecl install apcu xdebug pcov && \
    docker-php-ext-enable apcu xdebug pcov && \
    cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini && \
    chmod +r /usr/local/etc/php/conf.d/* && \
    apk del .build-deps && \
    rm -Rf /tmp/*

COPY .docker/php/ini/development.ini ./

RUN mv development.ini /usr/local/etc/php/conf.d/zz-php.ini

FROM nginx:1.23.3-alpine as nginx

COPY .docker/nginx/conf.d /etc/nginx/conf.d/

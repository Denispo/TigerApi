FROM php:8.1-fpm-alpine
RUN apk update; \
    apk upgrade;
RUN docker-php-ext-install mysqli && \
    docker-php-ext-install pdo_mysql && \
    docker-php-ext-install exif


# RUN docker-php-ext-install mbstring

RUN apk add --update linux-headers

# This example is extremely barebones and specific to Alpine linux, however in most containers at/below
# php 7.3, you can just run pecl + docker-php-ext-enable (see below) after you've ensured that you have
# also installed phpize (necessary for building PHP extensions).
#RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
     # Important part:
#    && pecl install xdebug \
    # Clean up..
#    && docker-php-ext-enable xdebug

# Copy over our custom xdebug INI configuration which will coexist with the automatically generated
# $PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini created by the "docker-php-ext-enable xdebug" command above.
#COPY ./xdebug.ini $PHP_INI_DIR/conf.d

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# resi problem: iconv(): Wrong charset, conversion from `UTF-8' to `ASCII//TRANSLIT//IGNORE' is not allowed
# Zdroj reseni: https://github.com/phpearth/docker-php/issues/3
RUN apk add --no-cache --repository http://dl-3.alpinelinux.org/alpine/edge/testing gnu-libiconv
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so php

# aby fungoval prikaz "composer require google/cloud-firestore" potrebujeme pry grpc

RUN apk add zlib-dev
RUN apk add linux-headers
#RUN pecl install grpc
#RUN	docker-php-ext-enable grpc


# ----- GD + podpora JPEG
# 1. nejprve pridame balicky
RUN apk add --no-cache freetype libpng libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev

# 2. Nakonfigurujeme GD (jo, prvne konfigurace, pak instalace... :/ )
RUN docker-php-ext-configure gd --with-jpeg

# 3. Nainstalujeme GD (bez 2. by se nenainstalovala podpora pro JPEG)
RUN docker-php-ext-install gd

# funguje i bez ...-enable gd. Asi je gd povolene nekde defaultne v 8.0-fpm-alpine
#RUN	docker-php-ext-enable gd

#https://releasecandidate.dev/posts/2020/how-to-install-php-ext-intl-on-alpine-docker/
RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        zlib-dev \
        icu-dev \
        g++ \
    && docker-php-ext-configure intl \
    && docker-php-ext-install \
        intl \
    && docker-php-ext-enable intl
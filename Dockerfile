FROM php:8.4-fpm-alpine

RUN apk add --no-cache $PHPIZE_DEPS mariadb-client \
    && docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www
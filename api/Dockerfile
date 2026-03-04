ARG PHP_VERSION=8.4

# Base image
FROM php:${PHP_VERSION}-fpm-alpine AS base

ENV MEMORY_LIMIT="256M"
COPY ./.devops/php/php.ini $PHP_INI_DIR/conf.d/

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN apk update && apk upgrade;
RUN install-php-extensions intl pdo_mysql;

ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /srv

COPY . .

# Development image
FROM base AS dev

# Production image
FROM base AS prod

RUN composer install

RUN php bin/console lexik:jwt:generate-keypair --overwrite --no-interaction
RUN php bin/console cache:clear

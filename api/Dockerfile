ARG PHP_VERSION=8.4

# Base image
FROM php:${PHP_VERSION}-fpm-alpine as base

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
FROM base as dev

# Production image
FROM base as prod

RUN composer install

RUN php bin/console lexik:jwt:generate-keypair --overwrite --no-interaction

RUN php bin/console doctrine:database:drop --if-exists --force
RUN php bin/console doctrine:database:create --if-not-exists
RUN php bin/console doctrine:migrations:migrate --no-interaction
RUN php bin/console doctrine:schema:update --force

RUN php bin/console cache:clear

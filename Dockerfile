FROM php:7.4-fpm

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN apt update && apt install -y git zip unzip

COPY ./install_composer.sh /usr/local/bin/install_composer
RUN chmod +x /usr/local/bin/install_composer \
    && install_composer \
    && mv composer.phar /usr/local/bin/composer

COPY src /var/www/html

WORKDIR /var/www/html

RUN composer install
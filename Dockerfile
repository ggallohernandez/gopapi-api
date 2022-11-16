FROM php:7.4-fpm

WORKDIR /var/www/html

EXPOSE 9000

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN apt update && apt install -y git zip unzip

COPY ./install_composer.sh /usr/local/bin/install_composer
RUN chmod +x /usr/local/bin/install_composer \
    && install_composer \
    && mv composer.phar /usr/local/bin/composer

COPY src /var/www/html

RUN composer install

# change file permissions of existing files and folders to 755/644
RUN chmod u=rwX,g=srX,o=rX -R /var/www/html
RUN find /var/www/html -type d -exec chmod g=rwxs "{}" \;
RUN find /var/www/html -type f -exec chmod g=rws "{}" \;
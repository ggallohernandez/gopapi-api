FROM php:7.4-fpm

WORKDIR /var/www/html

EXPOSE 9000

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN apt update && apt install -y git zip unzip wget

RUN mkdir -p /var/local/step \
    && wget -O /tmp/step-cli.deb https://dl.step.sm/gh-release/cli/docs-cli-install/v0.21.0/step-cli_0.21.0_amd64.deb \
    && dpkg -i /tmp/step-cli.deb

COPY ./install_composer.sh /usr/local/bin/install_composer
RUN chmod +x /usr/local/bin/install_composer \
    && install_composer \
    && mv composer.phar /usr/local/bin/composer

COPY src /var/www/html

# change file permissions of existing files and folders to 755/644
RUN chmod u=rwX,g=srX,o=rX -R /var/www/html
RUN find /var/www/html -type d -exec chmod g=rwxs "{}" \;
RUN find /var/www/html -type f -exec chmod g=rws "{}" \;

CMD composer install && docker-php-entrypoint php-fpm
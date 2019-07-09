FROM php:7.3-cli-stretch

ARG DEBIAN_FRONTEND=noninteractive

SHELL ["/bin/bash", "-euxo", "pipefail", "-c"]

RUN apt-get update; \
    apt-get full-upgrade -y; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/*; \
    pecl install xdebug-2.7.2; \
    docker-php-ext-enable xdebug; \
    curl -fsSLo composer-setup.php https://getcomposer.org/installer; \
    php composer-setup.php; \
    rm composer-setup.php; \
    mv composer.phar /usr/local/bin/composer; \
    chown www-data:www-data -R /var/www/

WORKDIR /usr/src

USER www-data

CMD ["composer", "list"]

FROM php:7.3-cli-stretch

ARG DEBIAN_FRONTEND=noninteractive

SHELL ["/bin/bash", "-euxo", "pipefail", "-c"]

RUN apt-get update; \
    apt-get full-upgrade -y; \
    apt-get install -y --no-install-recommends \
        libzip-dev \
        unzip \
    ; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/*; \
    docker-php-ext-install zip; \
    pecl install xdebug-2.7.2; \
    docker-php-ext-enable xdebug; \
    curl -fsSLo composer-setup.php https://getcomposer.org/installer; \
    php composer-setup.php; \
    rm composer-setup.php; \
    mv composer.phar /usr/local/bin/composer;

WORKDIR /usr/src

ENV COMPOSER_ALLOW_SUPERUSER 1

CMD ["composer", "list"]

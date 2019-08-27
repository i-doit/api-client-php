FROM php:7.3-cli-buster

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
    docker-php-ext-install \
        zip; \
    pecl install \
        xdebug-2.7.2; \
    docker-php-ext-enable \
        xdebug; \
    php -r \
        "copy('https://getcomposer.org/installer', 'composer-setup.php');"; \
    php -r \
        "if (hash_file('sha384', 'composer-setup.php') === 'a5c698ffe4b8e849a443b120cd5ba38043260d5c4023dbf93e1558871f1f07f58274fc6f4c93bcfd858c6bd0775cd8d1') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"; \
    php composer-setup.php --install-dir=/usr/bin --filename=composer; \
    php -r \
        "unlink('composer-setup.php');";

WORKDIR /usr/src

ENV COMPOSER_ALLOW_SUPERUSER 1

CMD ["composer", "list"]

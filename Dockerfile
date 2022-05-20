FROM php:8.0-cli

ARG DEBIAN_FRONTEND=noninteractive

SHELL ["/bin/bash", "-euxo", "pipefail", "-c"]

RUN apt-get update; \
    apt-get full-upgrade -y; \
    apt-get install -y --no-install-recommends \
        curl \
        libzip-dev \
        unzip \
    ; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/*; \
    docker-php-ext-install \
        zip; \
    pecl install \
        xdebug; \
    docker-php-ext-enable \
        xdebug; \
    echo "memory_limit = -1" > /usr/local/etc/php/conf.d/zzz-idoitapi.ini; \
    curl -fsSL \
        "https://composer.github.io/installer.sha384sum" \
        -o composer-setup.php.checksum; \
    curl -fsSL \
        "https://getcomposer.org/installer" \
        -o composer-setup.php; \
    sha384sum --check --strict \
        composer-setup.php.checksum; \
    php composer-setup.php \
        --2 \
        --install-dir=/usr/local/bin \
        --filename=composer; \
    rm \
        composer-setup.php \
        composer-setup.php.checksum

WORKDIR /usr/src

ENV COMPOSER_ALLOW_SUPERUSER 1

CMD ["composer", "list"]

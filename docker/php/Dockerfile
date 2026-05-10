FROM php:8.2-fpm-alpine

ARG UID=1000
ARG GID=1000

RUN apk add --no-cache \
        bash \
        curl \
        freetype-dev \
        icu-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        libwebp-dev \
        libzip-dev \
        oniguruma-dev \
        shadow \
        unzip \
        zip \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        exif \
        gd \
        intl \
        mbstring \
        pcntl \
        pdo_mysql \
        zip \
    && groupmod -g "${GID}" www-data \
    && usermod -u "${UID}" -g "${GID}" www-data

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-petmingle.ini
COPY docker/php/entrypoint.sh /usr/local/bin/petmingle-entrypoint

WORKDIR /var/www/html

ENTRYPOINT ["petmingle-entrypoint"]
CMD ["php-fpm"]

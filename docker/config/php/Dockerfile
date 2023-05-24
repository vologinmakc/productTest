FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

RUN apt-get install -y --no-install-recommends \
    zip \
    wget \
    nano \
    unzip \
    curl \
    mc \
    jpegoptim optipng pngquant gifsicle

RUN pecl install redis && docker-php-ext-enable redis

RUN docker-php-ext-install pcntl bcmath opcache

RUN apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

RUN curl -sL https://deb.nodesource.com/setup_16.x | bash \
    && apt-get install --yes nodejs \
    && npm i -g nodemon \
    && npm i -g yarn

# Installing composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install xdebug
RUN pecl install xdebug-2.9.8 \
    && docker-php-ext-enable xdebug

RUN echo xdebug.remote_enable=1 >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo xdebug.remote_port=9001 >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo xdebug.remote_autostart=1 >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo xdebug.idekey=PHPSTORM >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo xdebug.remote_host=${XDEBUG_REMOTE_HOST} >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN pecl install xhprof \
    && docker-php-ext-enable xhprof

RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Changing Workdir
WORKDIR /application
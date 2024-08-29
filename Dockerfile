FROM php:8.1.12-fpm-buster

MAINTAINER Stephen "honghua1207@sina.com"

RUN sed -i "s@http://deb.debian.org@http://mirrors.aliyun.com@g" /etc/apt/sources.list && \
    rm -Rf /var/lib/apt/lists/* && \
    apt-get update && \
    apt-get install -y librabbitmq-dev libzip-dev libfreetype6-dev libjpeg62-turbo-dev libmcrypt-dev libpng-dev curl telnet zlib1g-dev && \
    /bin/cp /usr/share/zoneinfo/Asia/Shanghai /etc/localtime && echo 'Asia/Shanghai' > /etc/timezone && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd zip pdo pdo_mysql opcache mysqli && \
    rm -rf /tmp/pear && \
    apt-get clean && rm -rf /var/cache/apt/*

WORKDIR /var/www

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /var/www
RUN composer config repo.packagist composer https://mirrors.aliyun.com/composer/
RUN composer install --no-dev -o --ignore-platform-reqs

EXPOSE 9000

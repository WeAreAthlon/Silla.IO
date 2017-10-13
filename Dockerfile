FROM php:latest

RUN apt-get update -yqq
RUN apt-get install git libcurl4-gnutls-dev libjpeg-dev libpng-dev zlib1g-dev libfreetype6-dev libsqlite3-dev libpcre3-dev -yqq

RUN docker-php-ext-install mbstring pdo_mysql curl json gd zip
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

